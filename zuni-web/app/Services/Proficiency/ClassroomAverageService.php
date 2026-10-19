<?php

namespace App\Services\Proficiency;

use App\Enums\UserStatus;
use App\Models\Classroom;
use App\Models\ClassroomProficiencyAverage;
use App\Models\Proficiency;
use App\Models\StudentSheet;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Média por sala e habilidade (atual) e snapshots históricos.
 *
 * Elegibilidade: aluno com users.status = 'active', pertencente à sala (student_sheets.classroom_id)
 * e com registro em student_proficiencies. As médias usam os valores calculados pelo
 * ProficiencyValueResolver (modelo ponderado pelo tempo), então refletem o decaimento mesmo
 * sem novas observações. Não existe histórico de pertencimento à sala: um snapshot de data
 * passada usa a composição ATUAL da sala (as observações, sim, são cortadas pela data).
 */
class ClassroomAverageService
{
    public function __construct(private readonly ProficiencyValueResolver $resolver)
    {
    }

    public function refresh(int $classroomId): void
    {
        $now = now();
        $averages = $this->averagesAt($classroomId, $now);

        DB::transaction(function () use ($classroomId, $averages, $now) {
            foreach (Proficiency::query()->pluck('id') as $proficiencyId) {
                $row = $averages[$proficiencyId] ?? null;

                ClassroomProficiencyAverage::updateOrCreate(
                    ['classroom_id' => $classroomId, 'proficiency_id' => $proficiencyId],
                    [
                        // Sem alunos elegíveis: null (e não 0, que seria uma "média" falsa).
                        'average' => $row['average'] ?? null,
                        'students_count' => $row['count'] ?? 0,
                        'calculated_at' => $now,
                    ]
                );
            }
        });
    }

    /**
     * @param  iterable<int|null>  $classroomIds
     */
    public function refreshMany(iterable $classroomIds): void
    {
        collect($classroomIds)->filter()->unique()->each(fn ($id) => $this->refresh((int) $id));
    }

    public function refreshForSheet(StudentSheet $sheet): void
    {
        if ($sheet->classroom_id) {
            $this->refresh($sheet->classroom_id);
        }
    }

    public function refreshAll(): void
    {
        Classroom::query()->pluck('id')->each(fn ($id) => $this->refresh((int) $id));
    }

    /**
     * Gera os snapshots do dia de referência (padrão: hoje) para habilidades ativas, calculados
     * com as observações até o FIM desse dia. Idempotente: a unicidade (sala, habilidade, data)
     * + INSERT OR IGNORE impede duplicatas e NUNCA sobrescreve um snapshot já gravado.
     *
     * @return int quantidade de snapshots novos
     */
    public function snapshot(?CarbonInterface $date = null, ?int $classroomId = null): int
    {
        $date ??= now();
        $at = $date->copy()->endOfDay();

        $classroomIds = $classroomId ? [$classroomId] : Classroom::query()->pluck('id')->all();
        $this->refreshMany($classroomIds);

        $activeIds = Proficiency::query()->active()->pluck('id');
        $now = now();
        $created = 0;

        foreach ($classroomIds as $id) {
            $averages = $this->averagesAt((int) $id, $at);

            $rows = $activeIds->map(fn ($proficiencyId) => [
                'classroom_id' => $id,
                'proficiency_id' => $proficiencyId,
                'reference_date' => $date->toDateString(),
                'average' => $averages[$proficiencyId]['average'] ?? null,
                'students_count' => $averages[$proficiencyId]['count'] ?? 0,
                'generated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            if ($rows !== []) {
                $created += DB::table('classroom_proficiency_snapshots')->insertOrIgnore($rows);
            }
        }

        return $created;
    }

    /**
     * @return array<int, array{average: float, count: int}> indexado por proficiency_id
     */
    private function averagesAt(int $classroomId, CarbonInterface $at): array
    {
        $sheetIds = StudentSheet::query()
            ->where('classroom_id', $classroomId)
            ->whereHas('user', fn ($q) => $q->where('status', UserStatus::ACTIVE->value))
            ->pluck('id')
            ->all();

        $sum = [];
        $count = [];

        foreach (array_chunk($sheetIds, 500) as $chunk) {
            foreach ($this->resolver->resolve($chunk, $at) as $perProficiency) {
                foreach ($perProficiency as $proficiencyId => $r) {
                    $sum[$proficiencyId] = ($sum[$proficiencyId] ?? 0) + $r['value'];
                    $count[$proficiencyId] = ($count[$proficiencyId] ?? 0) + 1;
                }
            }
        }

        $precision = (int) config('proficiency.precision', 4);
        $result = [];

        foreach ($sum as $proficiencyId => $total) {
            $result[$proficiencyId] = [
                'average' => round($total / $count[$proficiencyId], $precision),
                'count' => $count[$proficiencyId],
            ];
        }

        return $result;
    }
}
