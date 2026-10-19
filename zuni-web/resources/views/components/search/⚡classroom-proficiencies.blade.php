<?php

use App\Models\Classroom;
use App\Models\ClassroomProficiencyAverage;
use App\Models\ClassroomProficiencySnapshot;
use App\Models\Proficiency;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Médias atuais e evolução (snapshots) das proficiências de uma sala. Uso:
 *   <livewire:search.classroom-proficiencies :classroomId="$classroom->id" />
 *
 * "Atual" (tabela e ponto laranja) e "snapshot" (linha azul) são visualmente distintos.
 * Sem dados fictícios: sala sem alunos elegíveis mostra "—".
 */
new class extends Component
{
    #[Locked]
    public int $classroomId = 0;

    public string $proficiencyId = '';

    /** Dados do gráfico (lidos no navegador via $wire.chart). */
    public array $chart = ['labels' => [], 'snapshots' => [], 'counts' => [], 'current' => null, 'name' => ''];

    public function mount(): void
    {
        Gate::authorize('viewProficiencies', $this->classroom);

        $first = $this->averages->first(fn ($a) => $a->proficiency?->is_active);
        $this->proficiencyId = (string) ($first?->proficiency_id ?? '');
        $this->buildChart();
    }

    #[Computed]
    public function classroom(): Classroom
    {
        return Classroom::query()->findOrFail($this->classroomId);
    }

    #[Computed]
    public function averages()
    {
        Gate::authorize('viewProficiencies', $this->classroom);

        return ClassroomProficiencyAverage::query()
            ->where('classroom_id', $this->classroomId)
            ->with('proficiency:id,name,is_active')
            ->get()
            ->filter(fn ($a) => $a->proficiency?->is_active)
            ->sortBy('proficiency.name')
            ->values();
    }

    public function updatedProficiencyId(): void
    {
        $this->buildChart();
    }

    private function buildChart(): void
    {
        Gate::authorize('viewProficiencies', $this->classroom);

        $proficiency = Proficiency::query()->active()->find((int) $this->proficiencyId);

        if (! $proficiency) {
            $this->chart = ['labels' => [], 'snapshots' => [], 'counts' => [], 'current' => null, 'name' => ''];

            return;
        }

        $snapshots = ClassroomProficiencySnapshot::query()
            ->where('classroom_id', $this->classroomId)
            ->where('proficiency_id', $proficiency->id)
            ->orderBy('reference_date')
            ->limit(120)
            ->get();

        $current = ClassroomProficiencyAverage::query()
            ->where('classroom_id', $this->classroomId)
            ->where('proficiency_id', $proficiency->id)
            ->first();

        $this->chart = [
            'name' => $proficiency->name,
            'labels' => $snapshots->map(fn ($s) => $s->reference_date->format('d/m/Y'))->all(),
            'snapshots' => $snapshots->map(fn ($s) => $s->average !== null ? (float) $s->average : null)->all(),
            'counts' => $snapshots->map(fn ($s) => $s->students_count)->all(),
            'current' => $current?->average !== null ? (float) $current->average : null,
            'currentCount' => $current?->students_count ?? 0,
            'currentAt' => $current?->calculated_at?->format('d/m/Y H:i'),
        ];
    }
};
?>

<div class="card bg-base-100 shadow">
    <div class="card-body space-y-5">

        <div>
            <h2 class="card-title">Proficiências da sala</h2>
            <p class="text-xs text-base-content/50">
                Média dos alunos com matrícula ativa na sala. A evolução usa snapshots diários (um por dia, a partir da primeira geração).
            </p>
        </div>

        {{-- MÉDIAS ATUAIS --}}
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Habilidade</th>
                        <th class="text-right">Média atual</th>
                        <th class="text-right">Alunos incluídos</th>
                        <th>Calculada em</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->averages as $avg)
                        <tr wire:key="avg-{{ $avg->id }}">
                            <td>{{ $avg->proficiency->name }}</td>
                            <td class="text-right font-semibold">
                                {{ $avg->average !== null ? number_format((float) $avg->average, 2, ',', '.') : '—' }}
                            </td>
                            <td class="text-right">{{ $avg->students_count }}</td>
                            <td class="text-sm text-base-content/70">{{ $avg->calculated_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-base-content/60">Ainda não há médias calculadas para esta sala.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- EVOLUÇÃO --}}
        @if ($this->averages->isNotEmpty())
            <div class="divider my-0"></div>

            <div class="form-control max-w-sm">
                <label class="label" for="cp-skill"><span class="label-text">Evolução da habilidade</span></label>
                <select id="cp-skill" wire:model.live="proficiencyId" class="select select-bordered w-full">
                    @foreach ($this->averages as $avg)
                        <option value="{{ $avg->proficiency_id }}">{{ $avg->proficiency->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="relative w-full h-[320px]" wire:ignore>
                <canvas id="classroomProficiencyChart-{{ $classroomId }}"></canvas>
            </div>

            @if (empty($chart['labels']))
                <p class="text-sm text-base-content/50">
                    Ainda não há snapshots históricos para esta habilidade. O primeiro é gerado pelo comando
                    <code>proficiency:snapshot</code> (agendado diariamente).
                </p>
            @else
                <p class="text-xs text-base-content/50">
                    Período: {{ $chart['labels'][0] }} a {{ end($chart['labels']) }} · {{ count($chart['labels']) }} snapshot(s).
                </p>
            @endif
        @endif

    </div>
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endassets

@script
<script>
    const canvasId = 'classroomProficiencyChart-' + $wire.classroomId;
    let chartInstance = null;

    const render = (data) => {
        const canvas = document.getElementById(canvasId);
        if (!canvas || typeof Chart === 'undefined') return;

        if (chartInstance) chartInstance.destroy();

        const labels = [...data.labels];
        const snapshots = [...data.snapshots];
        const counts = [...data.counts];
        const currentSeries = labels.map(() => null);

        // O valor ATUAL aparece como um ponto próprio ao final, separado dos snapshots.
        if (data.current !== null && data.current !== undefined) {
            labels.push('Atual' + (data.currentAt ? ' (' + data.currentAt + ')' : ''));
            snapshots.push(null);
            counts.push(data.currentCount);
            currentSeries.push(data.current);
        }

        chartInstance = new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Snapshots históricos',
                        data: snapshots,
                        borderColor: 'rgb(37, 99, 235)',
                        backgroundColor: 'rgba(37, 99, 235, 0.15)',
                        tension: 0.2,
                        spanGaps: true,
                    },
                    {
                        label: 'Média atual',
                        data: currentSeries,
                        borderColor: 'rgb(234, 88, 12)',
                        backgroundColor: 'rgb(234, 88, 12)',
                        pointStyle: 'rectRot',
                        pointRadius: 7,
                        showLine: false,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { min: 0, max: 10 } },
                plugins: {
                    title: { display: true, text: data.name },
                    tooltip: {
                        callbacks: {
                            afterLabel: (ctx) => 'Alunos incluídos: ' + (counts[ctx.dataIndex] ?? '—'),
                        },
                    },
                },
            },
        });
    };

    render($wire.chart);
    $wire.$watch('chart', (value) => render(value));
</script>
@endscript
