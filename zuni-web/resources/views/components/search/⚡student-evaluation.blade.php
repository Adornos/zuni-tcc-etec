<?php

use App\Models\Proficiency;
use App\Models\StudentSheet;
use App\Services\Proficiency\ProficiencyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Tela de avaliação do aluno (professor). Primeira coisa da página do aluno. Uso:
 *   <livewire:search.student-evaluation :sheetId="$sheet->id" />
 *
 * Desktop: grade 2×2 (radar à esquerda, parâmetros à direita, justificativa abaixo).
 * Celular: uma coluna com 3 linhas (radar, parâmetros, justificativa).
 *
 * Cada parâmetro (habilidade) tem « ‹ [peso] › ». O campo central aceita um valor manual de
 * -5 a 5 (negativo diminui, positivo aumenta); os botões preenchem valores pré-definidos
 * (leve e forte, via .env). Ao escolher, o radar mostra como ficará a nota (pré-visualização
 * calculada no servidor, com a mesma regra do registro). Nada é gravado até clicar em "Salvar".
 */
new class extends Component
{
    #[Locked]
    public int $sheetId = 0;

    /** [proficiency_id => valor digitado, de -5 a 5] */
    public array $inputs = [];

    public string $reason = '';
    public string $token = '';
    public ?string $notice = null;

    /** Dados do radar (lidos no navegador via $wire.chart). */
    public array $chart = ['labels' => [], 'current' => [], 'after' => [], 'preview' => false];

    public function mount(): void
    {
        Gate::authorize('observe', $this->sheet);

        $this->token = (string) Str::uuid();
        $this->refreshChart();
    }

    #[Computed]
    public function sheet(): StudentSheet
    {
        return StudentSheet::query()->with('user')->findOrFail($this->sheetId);
    }

    #[Computed]
    public function proficiencies()
    {
        return Proficiency::query()->active()->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function current(): array
    {
        return app(ProficiencyService::class)->valuesFor($this->sheet);
    }

    /** @return array<int, float> valores válidos com sinal, limitados à faixa de intensidade */
    private function parsed(): array
    {
        $limits = config('proficiency.intensity');
        $out = [];

        foreach ($this->inputs as $id => $raw) {
            $text = str_replace(',', '.', trim((string) $raw));

            if ($text === '' || ! is_numeric($text)) {
                continue;
            }

            $value = (float) $text;

            if (abs($value) < $limits['min']) {
                continue;
            }

            $out[(int) $id] = max(-$limits['max'], min($limits['max'], $value));
        }

        return $out;
    }

    #[Computed]
    public function previews(): array
    {
        $service = app(ProficiencyService::class);
        $out = [];

        foreach ($this->parsed() as $id => $value) {
            if ($this->proficiencies->contains('id', $id)) {
                $out[$id] = $service->previewEntry($this->sheet, $id, $value);
            }
        }

        return array_filter($out);
    }

    public function updatedInputs(): void
    {
        unset($this->previews);
        $this->refreshChart();
    }

    /**
     * Botões « ‹ › »: preenchem o campo com o valor pré-definido; clicar de novo no mesmo botão limpa.
     */
    public function press(int $proficiencyId, string $button): void
    {
        Gate::authorize('observe', $this->sheet);
        abort_unless($this->proficiencies->contains('id', $proficiencyId), 404);

        $cfg = config('proficiency.buttons');
        $map = ['bf-' => -$cfg['strong'], 'bl-' => -$cfg['mild'], 'bl+' => $cfg['mild'], 'bf+' => $cfg['strong']];
        abort_unless(isset($map[$button]), 422);

        $target = (float) $map[$button];
        $current = $this->parsed()[$proficiencyId] ?? null;

        $this->inputs[$proficiencyId] = ($current !== null && abs($current - $target) < 0.0001)
            ? ''
            : rtrim(rtrim(number_format($target, 2, '.', ''), '0'), '.');

        unset($this->previews);
        $this->refreshChart();
    }

    public function clearAll(): void
    {
        $this->inputs = [];
        $this->resetErrorBag();
        unset($this->previews);
        $this->refreshChart();
    }

    private function refreshChart(): void
    {
        $previews = $this->previews;
        $labels = [];
        $current = [];
        $after = [];

        foreach ($this->proficiencies as $p) {
            $now = $this->current[$p->id]['value'] ?? app(\App\Services\Proficiency\ProficiencyCalculator::class)->neutral();
            $labels[] = $p->name;
            $current[] = round($now, 2);
            $after[] = round($previews[$p->id]['after'] ?? $now, 2);
        }

        $this->chart = [
            'labels' => $labels,
            'current' => $current,
            'after' => $after,
            'preview' => $previews !== [],
        ];
    }

    public function save(ProficiencyService $service): void
    {
        Gate::authorize('observe', $this->sheet);
        $this->resetErrorBag();
        $this->notice = null;

        $limits = config('proficiency.intensity');

        // Valores digitados precisam ser números dentro de -5..5 (a validação estrita fica aqui, não só na tela).
        foreach ($this->inputs as $id => $raw) {
            $text = str_replace(',', '.', trim((string) $raw));

            if ($text === '') {
                continue;
            }

            if (! is_numeric($text) || abs((float) $text) > $limits['max']) {
                $this->addError("inputs.$id", 'Use um número entre -'.$limits['max'].' e '.$limits['max'].'.');

                return;
            }
        }

        $entries = [];
        foreach ($this->parsed() as $id => $value) {
            $entries[] = ['proficiency_id' => $id, 'value' => $value];
        }

        if ($entries === []) {
            $this->addError('inputs', 'Escolha ao menos uma avaliação (botões ou valor de -'.$limits['max'].' a '.$limits['max'].').');

            return;
        }

        $this->validate(
            ['reason' => ['required', 'string', 'min:3', 'max:2000']],
            ['reason.required' => 'Escreva a justificativa da avaliação.', 'reason.min' => 'A justificativa precisa ter ao menos 3 caracteres.'],
            ['reason' => 'justificativa']
        );

        try {
            $service->recordBatch(Auth::user(), $this->sheet, $entries, $this->reason, $this->token);
        } catch (ValidationException $e) {
            $this->addError('inputs', collect($e->errors())->flatten()->first());

            return;
        }

        $this->notice = count($entries) === 1 ? 'Avaliação registrada.' : count($entries).' avaliações registradas.';
        $this->inputs = [];
        $this->reason = '';
        $this->token = (string) Str::uuid();

        unset($this->current, $this->previews);
        $this->refreshChart();
        $this->dispatch('proficiency-saved');
    }
};
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

    @php
        $cfg = config('proficiency.buttons');
        $fmt = fn ($v) => number_format((float) $v, 2, ',', '.');
        $presets = ['bf-' => [-$cfg['strong'], '«', 'Queda forte'], 'bl-' => [-$cfg['mild'], '‹', 'Queda leve'],
                    'bl+' => [$cfg['mild'], '›', 'Alta leve'], 'bf+' => [$cfg['strong'], '»', 'Alta forte']];
    @endphp

    {{-- 1ª linha / lado esquerdo: radar com a pré-visualização --}}
    <section class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">Avaliar {{ $this->sheet->user?->name }}</h2>
            <div class="relative w-full h-[320px]" wire:ignore>
                <canvas id="evalRadar-{{ $sheetId }}" role="img" aria-label="Radar das proficiências do aluno"></canvas>
            </div>
            <p class="text-xs text-base-content/60">
                @if ($chart['preview'])
                    A linha tracejada mostra como ficará a nota do aluno depois de salvar a avaliação.
                @else
                    Escolha uma avaliação ao lado para ver como ficará a nota do aluno.
                @endif
            </p>
        </div>
    </section>

    {{-- 2ª linha / lado direito: parâmetros de avaliação --}}
    <section class="card bg-base-100 shadow-xl">
        <div class="card-body gap-3">
            <h2 class="card-title">Parâmetros de avaliação</h2>
            @error('inputs') <p class="text-error text-sm" role="alert">{{ $message }}</p> @enderror

            @forelse ($this->proficiencies as $p)
                @php
                    $cur = $this->current[$p->id] ?? null;
                    $pv = $this->previews[$p->id] ?? null;
                    $typed = $this->parsed()[$p->id] ?? null;
                @endphp

                <div wire:key="eval-{{ $p->id }}" class="rounded-box border border-base-300 p-3">
                    <div class="flex flex-wrap items-baseline justify-between gap-x-3">
                        <span class="font-medium">{{ $p->name }}</span>
                        <span class="text-sm">
                            {{ $cur ? $fmt($cur['value']) : '—' }}
                            @if ($pv)
                                → <b>{{ $fmt($pv['after']) }}</b>
                                <span class="text-xs text-base-content/60">(peso ×{{ $fmt($pv['multiplier']) }})</span>
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center justify-center gap-1.5 mt-2" role="group" aria-label="Avaliação de {{ $p->name }}">
                        @foreach (['bf-', 'bl-'] as $key)
                            @php($sel = $typed !== null && abs($typed - $presets[$key][0]) < 0.0001)
                            <button type="button" wire:click="press({{ $p->id }}, '{{ $key }}')"
                                    class="btn btn-sm {{ $sel ? 'btn-error' : 'btn-outline' }}"
                                    aria-label="{{ $presets[$key][2] }} ({{ $presets[$key][0] }})" aria-pressed="{{ $sel ? 'true' : 'false' }}">{{ $presets[$key][1] }}</button>
                        @endforeach

                        <input type="number" step="0.1" min="-{{ config('proficiency.intensity.max') }}" max="{{ config('proficiency.intensity.max') }}"
                               inputmode="decimal" placeholder="0"
                               wire:model.live.debounce.400ms="inputs.{{ $p->id }}"
                               class="input input-bordered input-sm w-20 text-center"
                               aria-label="Peso manual de {{ $p->name }}, de -{{ config('proficiency.intensity.max') }} a {{ config('proficiency.intensity.max') }}">

                        @foreach (['bl+', 'bf+'] as $key)
                            @php($sel = $typed !== null && abs($typed - $presets[$key][0]) < 0.0001)
                            <button type="button" wire:click="press({{ $p->id }}, '{{ $key }}')"
                                    class="btn btn-sm {{ $sel ? 'btn-success' : 'btn-outline' }}"
                                    aria-label="{{ $presets[$key][2] }} (+{{ $presets[$key][0] }})" aria-pressed="{{ $sel ? 'true' : 'false' }}">{{ $presets[$key][1] }}</button>
                        @endforeach
                    </div>

                    @error("inputs.{$p->id}") <p class="text-error text-xs mt-1 text-center">{{ $message }}</p> @enderror
                </div>
            @empty
                <p class="text-sm text-base-content/60">Nenhuma habilidade ativa cadastrada.</p>
            @endforelse

            <p class="text-xs text-base-content/50">
                Negativo diminui, positivo aumenta. Botões: ‹ › = {{ $cfg['mild'] }} · « » = {{ $cfg['strong'] }}. Ou digite um valor de -{{ config('proficiency.intensity.max') }} a {{ config('proficiency.intensity.max') }}.
            </p>
        </div>
    </section>

    {{-- 3ª linha / embaixo: justificativa e ações --}}
    <section class="card bg-base-100 shadow-xl lg:col-span-2">
        <div class="card-body gap-3">
            <div class="form-control">
                <label class="label" for="eval-reason-{{ $sheetId }}"><span class="label-text font-semibold">Justificativa</span></label>
                <textarea id="eval-reason-{{ $sheetId }}" rows="4" wire:model="reason" class="textarea textarea-bordered w-full"
                          placeholder="Descreva o que foi observado"></textarea>
                @error('reason') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            @if ($notice)
                <div class="alert alert-success text-sm" role="status">{{ $notice }}</div>
            @endif

            <div class="flex flex-wrap justify-end gap-2">
                <button type="button" wire:click="clearAll" class="btn btn-ghost">Limpar</button>
                <button type="button" wire:click="save" class="btn bg-Csecondary text-white" wire:loading.attr="disabled" wire:target="save">
                    Salvar avaliação
                </button>
            </div>
        </div>
    </section>

</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
@endassets

@script
<script>
    const canvasId = 'evalRadar-' + $wire.sheetId;
    let radar = null;

    const render = (d) => {
        const canvas = document.getElementById(canvasId);
        if (!canvas || typeof Chart === 'undefined') return;
        if (radar) radar.destroy();

        const datasets = [{
            label: 'Atual',
            data: d.current,
            borderColor: 'rgb(37, 99, 235)',
            backgroundColor: 'rgba(37, 99, 235, 0.18)',
            pointBackgroundColor: 'rgb(37, 99, 235)',
            borderWidth: 2,
        }];

        if (d.preview) {
            datasets.push({
                label: 'Após a avaliação',
                data: d.after,
                borderColor: 'rgb(234, 88, 12)',
                backgroundColor: 'rgba(234, 88, 12, 0.12)',
                pointBackgroundColor: 'rgb(234, 88, 12)',
                borderDash: [6, 4],
                borderWidth: 2,
            });
        }

        radar = new Chart(canvas.getContext('2d'), {
            type: 'radar',
            data: { labels: d.labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { r: { min: 0, max: 10, ticks: { stepSize: 2, backdropColor: 'transparent' } } },
                plugins: { legend: { position: 'bottom' } },
            },
        });
    };

    render($wire.chart);
    $wire.$watch('chart', (value) => render(value));
</script>
@endscript
