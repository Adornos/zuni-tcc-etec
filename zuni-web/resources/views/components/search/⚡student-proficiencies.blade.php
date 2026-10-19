<?php

use App\Enums\ObservationDirection;
use App\Models\Proficiency;
use App\Models\ProficiencyObservation;
use App\Models\StudentSheet;
use App\Services\Proficiency\ProficiencyService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Proficiências de um aluno: valores atuais e histórico de observações (equipe), com anulação.
 * O registro de avaliações fica no componente `student-evaluation` (topo da página, professor). Uso:
 *   <livewire:search.student-proficiencies :sheetId="$sheet->id" />
 *
 * O valor NUNCA vem do navegador: o cliente envia só habilidade, direção, intensidade,
 * data e justificativa; o nível, o peso e o valor são calculados no ProficiencyService
 * (modelo de média ponderada pelo tempo).
 * Toda ação pública reautoriza no servidor.
 */
new class extends Component
{
    use WithPagination;

    #[Locked]
    public int $sheetId = 0;

    public ?int $voidingId = null;
    public string $voidReason = '';

    public function mount(): void
    {
        Gate::authorize('viewProficiencies', $this->sheet);

    }

    #[Computed]
    public function sheet(): StudentSheet
    {
        return StudentSheet::query()->with('user')->findOrFail($this->sheetId);
    }

    public function canSeeHistory(): bool
    {
        return Gate::allows('viewProficiencyHistory', $this->sheet);
    }

    #[Computed]
    public function rows()
    {
        // Valores calculados AO VIVO (inclui o decaimento do tempo), sem depender do cache.
        $values = app(ProficiencyService::class)->valuesFor($this->sheet);

        // Habilidades ativas + inativas que o aluno já tem (o histórico não some).
        return Proficiency::query()
            ->orderBy('name')
            ->get()
            ->filter(fn ($p) => $p->is_active || isset($values[$p->id]))
            ->map(function ($p) use ($values) {
                $p->setAttribute('current', $values[$p->id] ?? null);

                return $p;
            })
            ->values();
    }

    #[Computed]
    public function history()
    {
        abort_unless($this->canSeeHistory(), 403);

        return ProficiencyObservation::query()
            ->where('student_sheet_id', $this->sheetId)
            ->with(['proficiency:id,name', 'author:id,name', 'voidedBy:id,name'])
            ->orderByDesc('observed_at')
            ->orderByDesc('id')
            ->paginate(10, pageName: 'obsPage');
    }

    /** Atualiza valores e histórico quando uma avaliação é salva no componente do topo da página. */
    #[On('proficiency-saved')]
    public function refreshData(): void
    {
        unset($this->rows, $this->history);
        $this->resetPage('obsPage');
    }

    public function startVoid(int $id): void
    {
        $observation = ProficiencyObservation::query()->where('student_sheet_id', $this->sheetId)->findOrFail($id);
        Gate::authorize('void', $observation);

        $this->voidingId = $id;
        $this->voidReason = '';
    }

    public function cancelVoid(): void
    {
        $this->voidingId = null;
        $this->voidReason = '';
    }

    public function confirmVoid(ProficiencyService $service): void
    {
        abort_if($this->voidingId === null, 404);

        $observation = ProficiencyObservation::query()
            ->where('student_sheet_id', $this->sheetId)
            ->findOrFail($this->voidingId);

        Gate::authorize('void', $observation);

        $this->validate(
            ['voidReason' => ['required', 'string', 'min:3', 'max:1000']],
            [],
            ['voidReason' => 'motivo da anulação']
        );

        try {
            $service->void($observation, Auth::user(), $this->voidReason);
        } catch (ValidationException $e) {
            $this->addError('voidReason', collect($e->errors())->flatten()->first());

            return;
        }

        $this->cancelVoid();
                unset($this->rows, $this->history);
    }
};
?>

<div class="card bg-base-100 shadow">
    <div class="card-body space-y-5">

        <div>
            <h2 class="card-title">Proficiências</h2>
            <p class="text-xs text-base-content/50">Escala de 0 a 10 · valor neutro inicial 5.</p>
        </div>

        {{-- VALORES ATUAIS --}}
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Habilidade</th>
                        <th class="text-right">Valor atual</th>
                        <th class="text-right">Observações válidas</th>
                        <th>Confiança</th>
                        <th>Última observação</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $confidenceLabel = ['low' => ['Baixa', 'badge-warning'], 'medium' => ['Média', 'badge-info'], 'high' => ['Alta', 'badge-success']];
                    @endphp
                    @forelse ($this->rows as $row)
                        @php($cur = $row->current)
                        <tr wire:key="prof-row-{{ $row->id }}">
                            <td>
                                {{ $row->name }}
                                @unless ($row->is_active)
                                    <span class="badge badge-ghost badge-sm">inativa</span>
                                @endunless
                            </td>
                            <td class="text-right font-semibold">
                                {{ $cur ? number_format($cur['value'], 2, ',', '.') : '—' }}
                            </td>
                            <td class="text-right">
                                {{ $cur ? $cur['count'] : '—' }}
                            </td>
                            <td>
                                @if ($cur)
                                    <span class="badge badge-sm {{ $confidenceLabel[$cur['confidence']][1] }}">{{ $confidenceLabel[$cur['confidence']][0] }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-sm text-base-content/70">
                                {{ $cur && $cur['last_observed_at'] ? $cur['last_observed_at']->format('d/m/Y H:i') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-base-content/60">Nenhuma habilidade cadastrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="text-xs text-base-content/50">
            O valor é a média das observações, em que as mais recentes pesam mais (metade do peso a cada
            {{ rtrim(rtrim(number_format((float) config('proficiency.half_life_days'), 1, ',', ''), '0'), ',') }} dias).
            Poucas observações recentes = confiança baixa e valor mais próximo do neutro ({{ number_format((float) config('proficiency.neutral'), 0) }}).
        </p>

        {{-- HISTÓRICO --}}
        @if ($this->canSeeHistory())

            <div class="divider my-0"></div>

            <h3 class="font-semibold">Histórico de observações</h3>

            <div class="space-y-3">
                @forelse ($this->history as $obs)
                    <div wire:key="obs-{{ $obs->id }}"
                         class="rounded-box border border-base-300 p-3 {{ $obs->isVoided() ? 'opacity-60' : '' }}">

                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-medium">{{ $obs->proficiency?->name }}</span>
                                <span class="badge badge-sm {{ $obs->direction === \App\Enums\ObservationDirection::POSITIVE ? 'badge-success' : 'badge-error' }}">
                                    {{ $obs->direction->label() }}
                                </span>
                                @if ($obs->isVoided())
                                    <span class="badge badge-sm badge-neutral">Anulada</span>
                                @endif
                            </div>

                            @can('void', $obs)
                                <button type="button" wire:click="startVoid({{ $obs->id }})" class="btn btn-xs btn-outline btn-error">
                                    Anular
                                </button>
                            @endcan
                        </div>

                        <p class="text-xs text-base-content/60 mt-1">
                            {{ $obs->observed_at->format('d/m/Y H:i') }} · {{ $obs->author?->name ?? 'Autor removido' }}
                        </p>

                        <p class="text-sm mt-1">{{ $obs->reason }}</p>

                        <p class="text-xs text-base-content/70 mt-2">
                            Intensidade {{ number_format((float) $obs->intensity, 2, ',', '.') }} ·
                            nível {{ number_format((float) $obs->level, 2, ',', '.') }} ·
                            peso {{ number_format((float) $obs->weight, 2, ',', '.') }} ·
                            efeito no registro {{ number_format((float) $obs->value_before, 2, ',', '.') }} → {{ number_format((float) $obs->value_after, 2, ',', '.') }}
                        </p>

                        @if ($obs->isVoided())
                            <p class="text-xs text-error mt-1">
                                Anulada por {{ $obs->voidedBy?->name ?? '—' }} em {{ $obs->voided_at?->format('d/m/Y H:i') }}:
                                {{ $obs->void_reason }}
                            </p>
                        @endif

                        @if ($voidingId === $obs->id)
                            <div class="mt-3 space-y-2">
                                <label class="label" for="pf-void-{{ $obs->id }}"><span class="label-text">Motivo da anulação</span></label>
                                <textarea id="pf-void-{{ $obs->id }}" rows="2" wire:model="voidReason" class="textarea textarea-bordered w-full"></textarea>
                                @error('voidReason') <p class="text-error text-sm">{{ $message }}</p> @enderror
                                <div class="flex justify-end gap-2">
                                    <button type="button" wire:click="cancelVoid" class="btn btn-sm btn-ghost">Cancelar</button>
                                    <button type="button" wire:click="confirmVoid" class="btn btn-sm btn-error">Confirmar anulação</button>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-base-content/50">Nenhuma observação registrada.</p>
                @endforelse
            </div>

            <div>{{ $this->history->links() }}</div>
        @endif

    </div>
</div>
