<?php

namespace App\Services\Proficiency;

use App\Enums\ObservationDirection;
use App\Enums\ObservationStatus;
use App\Enums\UserStatus;
use App\Models\Proficiency;
use App\Models\ProficiencyObservation;
use App\Models\StudentProficiency;
use App\Models\StudentSheet;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Único ponto de escrita de proficiências: registrar, anular e atualizar o cache.
 * Autorização é feita por quem chama (Policy/Gate); aqui ficam cálculo, transações,
 * travas de concorrência e efeitos agregados (médias).
 *
 * Modelo: o valor é uma média ponderada pelo tempo (ver ProficiencyCalculator). Como a
 * ordem dos registros não altera o resultado, observações retroativas são permitidas e
 * anular uma observação basta recalcular o valor sem ela (não há reprocessamento em cadeia).
 */
class ProficiencyService
{
    public function __construct(
        private readonly ProficiencyCalculator $calculator,
        private readonly ProficiencyValueResolver $resolver,
        private readonly ClassroomAverageService $averages,
    ) {
    }

    /**
     * Cria (valor neutro) as proficiências ativas que o aluno ainda não tem. Idempotente.
     */
    public function ensureInitialized(StudentSheet $sheet): void
    {
        $now = now();

        $rows = Proficiency::query()->active()->pluck('id')->map(fn ($id) => $this->newRow($sheet->id, $id, $now))->all();

        if ($rows !== []) {
            DB::table('student_proficiencies')->insertOrIgnore($rows);
        }
    }

    /**
     * Inicializa uma habilidade (nova ou reativada) para todos os alunos elegíveis.
     */
    public function initializeProficiency(Proficiency $proficiency): void
    {
        $now = now();

        StudentSheet::query()
            ->whereHas('user', fn ($q) => $q->where('status', UserStatus::ACTIVE->value))
            ->orderBy('id')
            ->chunkById(500, function ($sheets) use ($proficiency, $now) {
                DB::table('student_proficiencies')->insertOrIgnore(
                    $sheets->map(fn ($s) => $this->newRow($s->id, $proficiency->id, $now))->all()
                );
            });

        $this->averages->refreshAll();
    }

    /**
     * Registra uma observação e atualiza histórico, cache do valor e média da sala
     * numa única transação.
     *
     * @throws ValidationException
     */
    public function record(
        User $author,
        StudentSheet $sheet,
        Proficiency $proficiency,
        ObservationDirection $direction,
        float $intensity,
        ?string $reason = null,
        ?CarbonInterface $observedAt = null,
        ?string $token = null,
    ): ProficiencyObservation {
        $observedAt ??= now();

        $limits = config('proficiency.intensity');
        if ($intensity < $limits['min'] || $intensity > $limits['max']) {
            throw ValidationException::withMessages([
                'intensity' => "A intensidade deve estar entre {$limits['min']} e {$limits['max']}.",
            ]);
        }

        if (! $proficiency->is_active) {
            throw ValidationException::withMessages(['proficiency_id' => 'Esta habilidade está inativa.']);
        }

        if ($observedAt->greaterThan(now()->addMinute())) {
            throw ValidationException::withMessages(['observed_at' => 'A data da observação não pode estar no futuro.']);
        }

        $sheet->loadMissing('user');
        if ($sheet->user?->status !== UserStatus::ACTIVE) {
            throw ValidationException::withMessages(['student' => 'O aluno não está elegível para avaliação (matrícula não ativa).']);
        }

        try {
            return DB::transaction(function () use ($author, $sheet, $proficiency, $direction, $intensity, $reason, $observedAt, $token) {
                if ($token && ($existing = ProficiencyObservation::query()->where('request_token', $token)->first())) {
                    return $existing; // reenvio do mesmo formulário
                }

                DB::table('student_proficiencies')->insertOrIgnore([
                    $this->newRow($sheet->id, $proficiency->id, now()),
                ]);

                // Trava a linha da série: observações simultâneas do mesmo aluno/habilidade se enfileiram aqui.
                StudentProficiency::query()
                    ->where('student_sheet_id', $sheet->id)
                    ->where('proficiency_id', $proficiency->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $before = $this->evaluateSeries($sheet->id, $proficiency->id, $observedAt);

                $level = $this->calculator->level($direction, $intensity);
                $weight = $this->calculator->observationWeightFor($direction, $before['value']);

                $observation = ProficiencyObservation::create([
                    'student_sheet_id' => $sheet->id,
                    'proficiency_id' => $proficiency->id,
                    'author_id' => $author->id,
                    'direction' => $direction,
                    'intensity' => $intensity,
                    'reason' => $reason,
                    'observed_at' => $observedAt,
                    'level' => $level,
                    // Peso fixo × multiplicador da assimetria (valor do aluno no instante do registro).
                    'weight' => $weight,
                    // Distância entre o nível observado e o valor no instante do registro.
                    'requested_delta' => round($level - $before['value'], 4),
                    'applied_delta' => 0,
                    'value_before' => $before['value'],
                    'value_after' => $before['value'],
                    'status' => ObservationStatus::VALID,
                    'request_token' => $token,
                ]);

                $after = $this->evaluateSeries($sheet->id, $proficiency->id, $observedAt);

                // Quanto o valor realmente se moveu no instante do registro.
                $observation->forceFill([
                    'applied_delta' => round($after['value'] - $before['value'], 4),
                    'value_after' => $after['value'],
                ])->save();

                $this->refreshSeries($sheet->id, $proficiency->id);
                $this->averages->refreshForSheet($sheet);

                return $observation;
            });
        } catch (UniqueConstraintViolationException $e) {
            if ($token && ($existing = ProficiencyObservation::query()->where('request_token', $token)->first())) {
                return $existing;
            }

            throw $e;
        }
    }

    /**
     * Pré-visualiza o efeito de UMA avaliação (valor com sinal de -5 a 5) sem gravar nada.
     * Usa exatamente o mesmo cálculo do registro: nível, peso (com assimetria) e expansão das pontas.
     * Retorna null para valor 0 (sem avaliação) ou fora da faixa de intensidade.
     *
     * @return array{proficiency_id: int, before: float, after: float, delta: float, level: float, multiplier: float, weight: float}|null
     */
    public function previewEntry(StudentSheet $sheet, int $proficiencyId, float $signedValue, ?CarbonInterface $at = null): ?array
    {
        $limits = config('proficiency.intensity');
        $intensity = abs($signedValue);

        if ($intensity < $limits['min'] || $intensity > $limits['max']) {
            return null;
        }

        $at ??= now();
        $direction = $signedValue > 0 ? ObservationDirection::POSITIVE : ObservationDirection::NEGATIVE;

        $before = $this->evaluateSeries($sheet->id, $proficiencyId, $at);
        $level = $this->calculator->level($direction, $intensity);
        $weight = $this->calculator->observationWeightFor($direction, $before['value']);

        $after = $this->evaluateSeries($sheet->id, $proficiencyId, $at, [
            $sheet->id => [$proficiencyId => [['at' => $at, 'level' => $level, 'weight' => $weight]]],
        ]);

        return [
            'proficiency_id' => $proficiencyId,
            'before' => $before['value'],
            'after' => $after['value'],
            'delta' => round($after['value'] - $before['value'], 4),
            'level' => $level,
            'multiplier' => $this->calculator->asymmetryMultiplier($direction, $before['value']),
            'weight' => $weight,
        ];
    }

    /**
     * Registra várias avaliações (uma por habilidade) com a mesma justificativa, de uma só vez.
     * Tudo ou nada: se uma falhar, nenhuma é gravada. O token torna o envio idempotente.
     *
     * @param  array<int, array{proficiency_id: int, value: float}>  $entries  valor com sinal, de -5 a 5 (0 é ignorado)
     * @return array<int, ProficiencyObservation>
     *
     * @throws ValidationException
     */
    public function recordBatch(User $author, StudentSheet $sheet, array $entries, ?string $reason, ?string $token = null, ?CarbonInterface $at = null): array
    {
        $entries = array_values(array_filter($entries, fn ($e) => abs((float) $e['value']) > 0));

        if ($entries === []) {
            throw ValidationException::withMessages(['inputs' => 'Informe ao menos uma avaliação.']);
        }

        $at ??= now();

        return DB::transaction(function () use ($author, $sheet, $entries, $reason, $token, $at) {
            $saved = [];

            foreach ($entries as $entry) {
                $value = (float) $entry['value'];

                $saved[] = $this->record(
                    $author,
                    $sheet,
                    Proficiency::query()->findOrFail((int) $entry['proficiency_id']),
                    $value > 0 ? ObservationDirection::POSITIVE : ObservationDirection::NEGATIVE,
                    abs($value),
                    $reason,
                    $at,
                    $token ? $token.'-'.$entry['proficiency_id'] : null,
                );
            }

            return $saved;
        });
    }

    /**
     * Anula (sem apagar) uma observação; o valor é recalculado sem ela e a média da sala atualizada.
     *
     * @throws ValidationException
     */
    public function void(ProficiencyObservation $observation, User $actor, string $reason): ProficiencyObservation
    {
        $reason = trim($reason);

        if ($reason === '') {
            throw ValidationException::withMessages(['void_reason' => 'Informe o motivo da anulação.']);
        }

        return DB::transaction(function () use ($observation, $actor, $reason) {
            /** @var ProficiencyObservation $fresh */
            $fresh = ProficiencyObservation::query()->lockForUpdate()->findOrFail($observation->getKey());

            if ($fresh->isVoided()) {
                throw ValidationException::withMessages(['observation' => 'Esta observação já foi anulada.']);
            }

            $sheet = StudentSheet::query()->findOrFail($fresh->student_sheet_id);

            // Nível, peso e valores calculados no registro ficam preservados para auditoria.
            $fresh->forceFill([
                'status' => ObservationStatus::VOIDED,
                'voided_by' => $actor->id,
                'voided_at' => now(),
                'void_reason' => $reason,
            ])->save();

            $this->refreshSeries($sheet->id, $fresh->proficiency_id);
            $this->averages->refreshForSheet($sheet);

            return $fresh->refresh();
        });
    }

    /**
     * Recalcula e grava o valor em cache de um aluno+habilidade (leitura em "agora").
     */
    public function refreshSeries(int $sheetId, int $proficiencyId, ?CarbonInterface $at = null): void
    {
        $at ??= now();
        $result = $this->evaluateSeries($sheetId, $proficiencyId, $at);

        DB::table('student_proficiencies')
            ->where('student_sheet_id', $sheetId)
            ->where('proficiency_id', $proficiencyId)
            ->update([
                'value' => $result['value'],
                'effective_count' => $result['effective'],
                'observations_count' => $result['count'],
                'last_observed_at' => $result['last_observed_at'],
                'calculated_at' => $at,
                'updated_at' => $at,
            ]);
    }

    /**
     * Atualiza o cache de todos os alunos (o decaimento do tempo muda o valor sem novas observações).
     * Chamado diariamente pelo comando agendado.
     *
     * @return int quantidade de linhas atualizadas
     */
    public function refreshAll(?CarbonInterface $at = null): int
    {
        $at ??= now();
        $updated = 0;

        DB::table('student_proficiencies')->orderBy('id')->chunkById(500, function ($rows) use ($at, &$updated) {
            $resolved = $this->resolver->resolve($rows->pluck('student_sheet_id')->unique()->all(), $at);

            foreach ($rows as $row) {
                $r = $resolved[$row->student_sheet_id][$row->proficiency_id] ?? null;

                if ($r === null) {
                    continue;
                }

                DB::table('student_proficiencies')->where('id', $row->id)->update([
                    'value' => $r['value'],
                    'effective_count' => $r['effective'],
                    'observations_count' => $r['count'],
                    'last_observed_at' => $r['last_observed_at'],
                    'calculated_at' => $at,
                    'updated_at' => $at,
                ]);
                $updated++;
            }
        });

        return $updated;
    }

    /**
     * Valor ao vivo (sem cache) de um aluno, para exibição.
     *
     * @return array<int, array{value: float, effective: float, count: int, confidence: string, last_observed_at: ?\Illuminate\Support\Carbon, base_value: float}>
     *         indexado por proficiency_id
     */
    public function valuesFor(StudentSheet $sheet, ?CarbonInterface $at = null): array
    {
        return $this->resolver->resolve([$sheet->id], $at)[$sheet->id] ?? [];
    }

    /**
     * @param  array<int, array<int, array<int, array{at: CarbonInterface, level: float, weight: float}>>>  $extra
     * @return array{value: float, raw: float, effective: float, count: int, confidence: string, last_observed_at: ?\Illuminate\Support\Carbon, base_value: float}
     */
    private function evaluateSeries(int $sheetId, int $proficiencyId, CarbonInterface $at, array $extra = []): array
    {
        $resolved = $this->resolver->resolve([$sheetId], $at, [$proficiencyId], $extra)[$sheetId][$proficiencyId] ?? null;

        if ($resolved !== null) {
            return $resolved;
        }

        // Aluno ainda sem linha para a habilidade: parte do valor neutro (usado em pré-visualizações).
        $neutral = $this->calculator->neutral();
        $evaluation = $this->calculator->evaluate($neutral, $extra[$sheetId][$proficiencyId] ?? [], $at);

        return $evaluation + [
            'confidence' => $this->calculator->confidence($evaluation['effective']),
            'last_observed_at' => null,
            'base_value' => $neutral,
        ];
    }

    private function newRow(int $sheetId, int $proficiencyId, CarbonInterface $now): array
    {
        $neutral = $this->calculator->neutral();

        return [
            'student_sheet_id' => $sheetId,
            'proficiency_id' => $proficiencyId,
            'base_value' => $neutral,
            'value' => $neutral,
            'effective_count' => 0,
            'observations_count' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
