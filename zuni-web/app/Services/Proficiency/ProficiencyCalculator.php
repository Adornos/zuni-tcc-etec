<?php

namespace App\Services\Proficiency;

use App\Enums\ObservationDirection;
use Carbon\CarbonInterface;
use InvalidArgumentException;

/**
 * Cálculo puro (sem banco e sem relógio) do modelo de média ponderada pelo tempo:
 *
 *   nível         = clamp(neutro ± intensidade × level_per_intensity)
 *   recência      = 0.5 ^ (dias / half_life_days)         (observação futura não conta)
 *   valor         = (prior_weight × inicial + Σ peso × recência × nível)
 *                   / (prior_weight + Σ peso × recência)
 *
 * O valor fica sempre entre o menor e o maior nível envolvido, portanto dentro da escala,
 * sem cortes artificiais. Observações têm peso fixo: o intervalo entre registros não
 * altera o quanto cada uma vale; só a idade em relação ao instante da leitura.
 */
final class ProficiencyCalculator
{
    public function __construct(
        private readonly float $min = 0.0,
        private readonly float $max = 10.0,
        private readonly float $neutral = 5.0,
        private readonly float $halfLifeDays = 30.0,
        private readonly float $priorWeight = 2.0,
        private readonly float $observationWeight = 1.0,
        private readonly float $levelPerIntensity = 1.0,
        private readonly float $confidenceLow = 2.0,
        private readonly float $confidenceHigh = 4.0,
        private readonly int $precision = 4,
        private readonly bool $asymmetricWeights = false,
        private readonly float $asymmetryStrength = 0.0,
        private readonly float $minMultiplier = 0.1,
        private readonly bool $reachableExtremes = false,
        private readonly float $extremeGain = 1.0,
    ) {
        if ($min >= $max) {
            throw new InvalidArgumentException('A escala precisa ter mínimo menor que máximo.');
        }
        if ($neutral < $min || $neutral > $max) {
            throw new InvalidArgumentException('O valor neutro precisa estar dentro da escala.');
        }
        if ($halfLifeDays <= 0 || $priorWeight <= 0 || $observationWeight <= 0 || $levelPerIntensity <= 0) {
            throw new InvalidArgumentException('half_life_days, prior_weight, observation_weight e level_per_intensity precisam ser positivos.');
        }
        if ($confidenceLow < 0 || $confidenceHigh < $confidenceLow) {
            throw new InvalidArgumentException('Limites de confiança inválidos.');
        }
        if ($asymmetryStrength < 0 || $minMultiplier < 0) {
            throw new InvalidArgumentException('A força da assimetria e o multiplicador mínimo não podem ser negativos.');
        }
        if ($extremeGain < 1) {
            throw new InvalidArgumentException('O ganho nas pontas precisa ser no mínimo 1.');
        }
    }

    public static function fromConfig(): self
    {
        $c = config('proficiency');

        return new self(
            min: (float) $c['scale']['min'],
            max: (float) $c['scale']['max'],
            neutral: (float) $c['neutral'],
            halfLifeDays: (float) $c['half_life_days'],
            priorWeight: (float) $c['prior_weight'],
            observationWeight: (float) $c['observation_weight'],
            levelPerIntensity: (float) $c['level_per_intensity'],
            confidenceLow: (float) $c['confidence']['low'],
            confidenceHigh: (float) $c['confidence']['high'],
            precision: (int) $c['precision'],
            asymmetricWeights: (bool) ($c['asymmetric_weights'] ?? false),
            asymmetryStrength: (float) ($c['asymmetry_strength'] ?? 0.0),
            minMultiplier: (float) ($c['min_asymmetry_multiplier'] ?? 0.1),
            reachableExtremes: (bool) ($c['reachable_extremes'] ?? false),
            extremeGain: (float) ($c['extreme_gain'] ?? 1.0),
        );
    }

    public function neutral(): float
    {
        return $this->neutral;
    }

    public function observationWeight(): float
    {
        return $this->observationWeight;
    }

    /**
     * Multiplicador do peso conforme o valor atual do aluno (pesos assimétricos).
     * Em `neutro` vale 1 nos dois sentidos; abaixo dele subir pesa mais e descer pesa menos;
     * acima, subir pesa menos e descer pesa mais. Desligado: sempre 1.
     */
    public function asymmetryMultiplier(ObservationDirection $direction, float $currentValue): float
    {
        if (! $this->asymmetricWeights || $this->asymmetryStrength == 0.0) {
            return 1.0;
        }

        $span = $currentValue >= $this->neutral ? ($this->max - $this->neutral) : ($this->neutral - $this->min);
        $offset = $span > 0 ? ($currentValue - $this->neutral) / $span : 0.0; // -1 .. +1

        $multiplier = $direction === ObservationDirection::POSITIVE
            ? 1 - $this->asymmetryStrength * $offset
            : 1 + $this->asymmetryStrength * $offset;

        return max($this->minMultiplier, $multiplier);
    }

    /**
     * Peso a gravar em uma nova observação: peso fixo × multiplicador da assimetria.
     */
    public function observationWeightFor(ObservationDirection $direction, float $currentValue): float
    {
        return $this->observationWeight * $this->asymmetryMultiplier($direction, $currentValue);
    }

    /**
     * Valor exibido a partir da média bruta (expansão das pontas, se ligada).
     */
    public function displayValue(float $raw): float
    {
        $value = $this->reachableExtremes
            ? $this->neutral + $this->extremeGain * ($raw - $this->neutral)
            : $raw;

        return min($this->max, max($this->min, $value));
    }

    /**
     * Nível registrado por uma observação: neutro ± intensidade × level_per_intensity,
     * limitado à escala.
     */
    public function level(ObservationDirection $direction, float $intensity): float
    {
        if (! is_finite($intensity) || $intensity <= 0) {
            throw new InvalidArgumentException('A intensidade precisa ser um número positivo.');
        }

        $level = $this->neutral + $direction->sign() * $intensity * $this->levelPerIntensity;

        return round(min($this->max, max($this->min, $level)), $this->precision);
    }

    /**
     * Fator de recência na leitura. Observação posterior ao instante lido não conta (0).
     */
    public function recency(CarbonInterface $observedAt, CarbonInterface $at): float
    {
        $seconds = $at->getTimestamp() - $observedAt->getTimestamp();

        if ($seconds < 0) {
            return 0.0;
        }

        return 0.5 ** (($seconds / 86400) / $this->halfLifeDays);
    }

    /**
     * @param  iterable<array{at: CarbonInterface, level: float, weight?: float}>  $observations
     * @return array{value: float, raw: float, effective: float, count: int}
     */
    public function evaluate(float $initial, iterable $observations, CarbonInterface $at): array
    {
        $numerator = $this->priorWeight * $initial;
        $denominator = $this->priorWeight;
        $effective = 0.0;
        $count = 0;

        foreach ($observations as $observation) {
            $recency = $this->recency($observation['at'], $at);

            if ($recency <= 0.0) {
                continue;
            }

            $weight = ($observation['weight'] ?? $this->observationWeight) * $recency;

            $numerator += $weight * $observation['level'];
            $denominator += $weight;
            $effective += $weight;
            $count++;
        }

        $raw = $numerator / $denominator;

        return [
            'value' => round($this->displayValue($raw), $this->precision),
            'raw' => round($raw, $this->precision),
            'effective' => round($effective, $this->precision),
            'count' => $count,
        ];
    }

    /**
     * low | medium | high, a partir das observações efetivas (soma dos pesos recentes).
     */
    public function confidence(float $effective): string
    {
        return match (true) {
            $effective < $this->confidenceLow => 'low',
            $effective < $this->confidenceHigh => 'medium',
            default => 'high',
        };
    }
}
