<?php

namespace App\Services\Proficiency;

use App\Enums\ObservationDirection;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Calcula o valor de proficiência de alunos EM UM INSTANTE (agora ou uma data passada),
 * a partir do valor inicial (base_value) e das observações válidas até esse instante.
 * Usado pela página do aluno, pelo cache, pelas médias das salas e pelos snapshots.
 */
class ProficiencyValueResolver
{
    public function __construct(private readonly ProficiencyCalculator $calculator)
    {
    }

    /**
     * @param  array<int, int>  $sheetIds
     * @param  array<int, int>|null  $proficiencyIds
     * @param  array<int, array<int, array<int, array{at: CarbonInterface, level: float, weight: float}>>>  $extra
     *         observações hipotéticas por [student_sheet_id][proficiency_id], usadas só para pré-visualizar (nada é gravado).
     * @return array<int, array<int, array{value: float, raw: float, effective: float, count: int, confidence: string, last_observed_at: ?Carbon, base_value: float}>>
     *         indexado por [student_sheet_id][proficiency_id]; só inclui pares que existem em student_proficiencies.
     */
    public function resolve(array $sheetIds, ?CarbonInterface $at = null, ?array $proficiencyIds = null, array $extra = []): array
    {
        $at ??= now();

        if ($sheetIds === []) {
            return [];
        }

        $rows = DB::table('student_proficiencies')
            ->whereIn('student_sheet_id', $sheetIds)
            ->when($proficiencyIds !== null, fn ($q) => $q->whereIn('proficiency_id', $proficiencyIds))
            ->get(['student_sheet_id', 'proficiency_id', 'base_value']);

        $observations = DB::table('proficiency_observations')
            ->whereIn('student_sheet_id', $sheetIds)
            ->when($proficiencyIds !== null, fn ($q) => $q->whereIn('proficiency_id', $proficiencyIds))
            ->where('status', 'valid')
            ->where('observed_at', '<=', $at)
            ->get(['student_sheet_id', 'proficiency_id', 'observed_at', 'level', 'weight', 'direction', 'intensity'])
            ->groupBy(fn ($o) => $o->student_sheet_id.'-'.$o->proficiency_id);

        $result = [];

        foreach ($rows as $row) {
            $list = ($observations->get($row->student_sheet_id.'-'.$row->proficiency_id) ?? collect())
                ->map(fn ($o) => [
                    'at' => Carbon::parse($o->observed_at),
                    // Linhas antigas sem nível: reconstrói a partir de direção e intensidade.
                    'level' => $o->level !== null
                        ? (float) $o->level
                        : $this->calculator->level(ObservationDirection::from($o->direction), (float) $o->intensity),
                    'weight' => (float) $o->weight,
                ]);

            foreach ($extra[$row->student_sheet_id][$row->proficiency_id] ?? [] as $hypothetical) {
                $list->push($hypothetical);
            }

            $evaluation = $this->calculator->evaluate((float) $row->base_value, $list, $at);

            $result[$row->student_sheet_id][$row->proficiency_id] = $evaluation + [
                'confidence' => $this->calculator->confidence($evaluation['effective']),
                'last_observed_at' => $list->isEmpty() ? null : $list->max(fn ($o) => $o['at']),
                'base_value' => (float) $row->base_value,
            ];
        }

        return $result;
    }
}
