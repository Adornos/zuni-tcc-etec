<?php

use App\Enums\ObservationDirection as Dir;
use App\Services\Proficiency\ProficiencyCalculator;
use Carbon\CarbonImmutable;

/*
| Testes da fórmula pura (sem banco, sem relógio). Datas sempre explícitas.
| Padrão: neutro 5, meia-vida 30 dias, valor inicial com peso 2, peso 1 por observação,
| nível = 5 ± intensidade.
*/

function pcCalc(array $overrides = []): ProficiencyCalculator
{
    return new ProficiencyCalculator(...array_merge([
        'min' => 0.0, 'max' => 10.0, 'neutral' => 5.0, 'halfLifeDays' => 30.0,
        'priorWeight' => 2.0, 'observationWeight' => 1.0, 'levelPerIntensity' => 1.0,
        'confidenceLow' => 2.0, 'confidenceHigh' => 4.0, 'precision' => 4,
        'asymmetricWeights' => false, 'asymmetryStrength' => 0.0, 'minMultiplier' => 0.1,
        'reachableExtremes' => false, 'extremeGain' => 1.0,
    ], $overrides));
}

function pcAt(string $date): CarbonImmutable
{
    return CarbonImmutable::parse($date);
}

function pcObs(string $at, float $level, ?float $weight = null): array
{
    return array_filter(['at' => pcAt($at), 'level' => $level, 'weight' => $weight], fn ($v) => $v !== null);
}

it('converte direção e intensidade em nível (neutro ± intensidade)', function () {
    $c = pcCalc();

    expect($c->level(Dir::POSITIVE, 2.0))->toBe(7.0)
        ->and($c->level(Dir::NEGATIVE, 2.0))->toBe(3.0)
        ->and($c->level(Dir::POSITIVE, 0.5))->toBe(5.5)
        ->and($c->level(Dir::POSITIVE, 5.0))->toBe(10.0)
        ->and($c->level(Dir::NEGATIVE, 5.0))->toBe(0.0);
});

it('aceita qualquer fator de nível por intensidade e limita o nível à escala', function () {
    expect(pcCalc(['levelPerIntensity' => 0.5])->level(Dir::POSITIVE, 2.0))->toBe(6.0)
        ->and(pcCalc(['levelPerIntensity' => 0.25])->level(Dir::NEGATIVE, 3.0))->toBe(4.25)
        ->and(pcCalc(['levelPerIntensity' => 3.0])->level(Dir::POSITIVE, 5.0))->toBe(10.0)
        ->and(pcCalc(['levelPerIntensity' => 3.0])->level(Dir::NEGATIVE, 5.0))->toBe(0.0);
});

it('aplica a meia-vida da leitura: 1, 0,5 e 0,25 em 0, 30 e 60 dias', function () {
    $c = pcCalc();
    $obs = pcAt('2026-03-01 10:00');

    expect($c->recency($obs, $obs))->toBe(1.0)
        ->and(round($c->recency($obs, $obs->addDays(30)), 6))->toBe(0.5)
        ->and(round($c->recency($obs, $obs->addDays(60)), 6))->toBe(0.25);
});

it('ignora observações posteriores ao instante lido', function () {
    $c = pcCalc();

    expect($c->recency(pcAt('2026-03-10 10:00'), pcAt('2026-03-01 10:00')))->toBe(0.0);

    $r = $c->evaluate(5.0, [pcObs('2026-03-10 10:00', 9.0)], pcAt('2026-03-01 10:00'));
    expect($r['value'])->toBe(5.0)->and($r['count'])->toBe(0);
});

it('sem observações o valor é o inicial, com 0 observações efetivas', function () {
    $r = pcCalc()->evaluate(5.0, [], pcAt('2026-03-01 10:00'));

    expect($r)->toBe(['value' => 5.0, 'raw' => 5.0, 'effective' => 0.0, 'count' => 0]);
    expect(pcCalc()->evaluate(7.0, [], pcAt('2026-03-01 10:00'))['value'])->toBe(7.0); // valor inicial importado
});

it('calcula a média ponderada de uma observação: (2×5 + 1×7) / 3', function () {
    $r = pcCalc()->evaluate(5.0, [pcObs('2026-03-01 10:00', 7.0)], pcAt('2026-03-01 10:00'));

    expect($r['value'])->toBe(5.6667)->and($r['effective'])->toBe(1.0)->and($r['count'])->toBe(1);
});

it('trata positiva e negativa de forma simétrica em torno do neutro', function () {
    $up = pcCalc()->evaluate(5.0, [pcObs('2026-03-01 10:00', 7.0)], pcAt('2026-03-01 10:00'))['value'];
    $down = pcCalc()->evaluate(5.0, [pcObs('2026-03-01 10:00', 3.0)], pcAt('2026-03-01 10:00'))['value'];

    expect(round($up - 5, 4))->toBe(round(5 - $down, 4));
});

it('dá mais peso ao que é recente: um negativo recente supera um positivo antigo', function () {
    $r = pcCalc()->evaluate(
        5.0,
        [pcObs('2026-03-01 10:00', 7.0), pcObs('2026-04-30 10:00', 3.0)],
        pcAt('2026-04-30 10:00')
    );

    // 60 dias depois: o positivo vale 0,25; o negativo vale 1.
    expect($r['value'])->toBe(4.5385)->and($r['effective'])->toBe(1.25);
});

it('não depende da ordem dos registros nem do intervalo entre eles', function () {
    $a = [pcObs('2026-03-01 10:00', 7.0), pcObs('2026-03-20 10:00', 3.0), pcObs('2026-04-10 10:00', 8.0)];
    $b = [$a[2], $a[0], $a[1]];
    $at = pcAt('2026-04-30 10:00');

    expect(pcCalc()->evaluate(5.0, $a, $at))->toBe(pcCalc()->evaluate(5.0, $b, $at));
});

it('usa o peso individual da observação quando informado', function () {
    $at = pcAt('2026-03-01 10:00');
    $double = pcCalc()->evaluate(5.0, [pcObs('2026-03-01 10:00', 7.0, 2.0)], $at);

    // (2×5 + 2×7) / (2 + 2) = 6,0
    expect($double['value'])->toBe(6.0)->and($double['effective'])->toBe(2.0);
});

it('reduz a dependência da frequência de registro (mesmo comportamento, leitura no dia 168)', function () {
    $final = function (int $step) {
        $obs = array_map(fn ($d) => ['at' => pcAt('2026-01-01 00:00')->addDays($d), 'level' => 6.5], range(0, 167, $step));

        return pcCalc()->evaluate(5.0, $obs, pcAt('2026-01-01 00:00')->addDays(168))['value'];
    };

    expect($final(7))->toBe(6.1042)
        ->and($final(14))->toBe(5.8427)
        ->and($final(30))->toBe(5.5906)
        ->and($final(60))->toBe(5.2669);
});

it('volta ao valor inicial quando fica muito tempo sem observações', function () {
    $r = pcCalc()->evaluate(5.0, [pcObs('2026-01-01 10:00', 9.0)], pcAt('2026-01-01 10:00')->addDays(300));

    expect(abs($r['value'] - 5.0))->toBeLessThan(0.01);
});

it('mantém o valor dentro da escala mesmo com muitas observações extremas', function () {
    $obs = array_fill(0, 30, pcObs('2026-03-01 10:00', 10.0));
    $r = pcCalc()->evaluate(5.0, $obs, pcAt('2026-03-01 10:00'));

    expect($r['value'])->toBe(9.6875)->and($r['value'])->toBeLessThanOrEqual(10.0);
});

it('classifica a confiança pelas observações efetivas', function () {
    $c = pcCalc();

    expect($c->confidence(0.0))->toBe('low')
        ->and($c->confidence(1.99))->toBe('low')
        ->and($c->confidence(2.0))->toBe('medium')
        ->and($c->confidence(3.99))->toBe('medium')
        ->and($c->confidence(4.0))->toBe('high');
});

it('rejeita intensidade inválida', function (float $intensity) {
    pcCalc()->level(Dir::POSITIVE, $intensity);
})->with([0.0, -1.0, NAN, INF])->throws(InvalidArgumentException::class);

it('rejeita configuração inválida', function (array $bad) {
    pcCalc($bad);
})->with([
    'meia-vida zero' => [['halfLifeDays' => 0.0]],
    'peso do valor inicial zero' => [['priorWeight' => 0.0]],
    'peso de observação negativo' => [['observationWeight' => -1.0]],
    'neutro fora da escala' => [['neutral' => 11.0]],
])->throws(InvalidArgumentException::class);


/* ---------------------------------------------------------------------- */
/* Pesos assimétricos (chave PROFICIENCY_ASYMMETRIC_WEIGHTS)               */
/* ---------------------------------------------------------------------- */

it('desligada, a assimetria é sempre 1', function () {
    $c = pcCalc(['asymmetricWeights' => false, 'asymmetryStrength' => 1.0]);

    foreach ([0.0, 2.5, 5.0, 7.5, 10.0] as $v) {
        expect($c->asymmetryMultiplier(Dir::POSITIVE, $v))->toBe(1.0)
            ->and($c->asymmetryMultiplier(Dir::NEGATIVE, $v))->toBe(1.0);
    }
});

it('em 5 o peso para cima e para baixo é o mesmo', function () {
    $c = pcCalc(['asymmetricWeights' => true, 'asymmetryStrength' => 1.0]);

    expect($c->asymmetryMultiplier(Dir::POSITIVE, 5.0))->toBe(1.0)
        ->and($c->asymmetryMultiplier(Dir::NEGATIVE, 5.0))->toBe(1.0);
});

it('em 2,5 subir pesa mais que descer; em 7,5 subir pesa menos que descer', function () {
    $c = pcCalc(['asymmetricWeights' => true, 'asymmetryStrength' => 1.0]);

    expect($c->asymmetryMultiplier(Dir::POSITIVE, 2.5))->toBe(1.5)
        ->and($c->asymmetryMultiplier(Dir::NEGATIVE, 2.5))->toBe(0.5)
        ->and($c->asymmetryMultiplier(Dir::POSITIVE, 7.5))->toBe(0.5)
        ->and($c->asymmetryMultiplier(Dir::NEGATIVE, 7.5))->toBe(1.5);
});

it('a força da assimetria escala o efeito e o multiplicador nunca cai abaixo do mínimo', function () {
    $half = pcCalc(['asymmetricWeights' => true, 'asymmetryStrength' => 0.4]);
    $full = pcCalc(['asymmetricWeights' => true, 'asymmetryStrength' => 1.0]);

    expect($half->asymmetryMultiplier(Dir::POSITIVE, 2.5))->toBe(1.2)
        ->and($half->asymmetryMultiplier(Dir::NEGATIVE, 2.5))->toBe(0.8)
        ->and($full->asymmetryMultiplier(Dir::POSITIVE, 10.0))->toBe(0.1)   // 1 − 1 = 0 → piso
        ->and($full->asymmetryMultiplier(Dir::NEGATIVE, 0.0))->toBe(0.1);
});

it('o peso gravado é o peso fixo vezes o multiplicador', function () {
    $c = pcCalc(['asymmetricWeights' => true, 'asymmetryStrength' => 1.0, 'observationWeight' => 2.0]);

    expect($c->observationWeightFor(Dir::POSITIVE, 2.5))->toBe(3.0)
        ->and($c->observationWeightFor(Dir::NEGATIVE, 2.5))->toBe(1.0);
});

/* ---------------------------------------------------------------------- */
/* Extremos alcançáveis (chave PROFICIENCY_REACHABLE_EXTREMES)             */
/* ---------------------------------------------------------------------- */

it('desligado, o valor exibido é o bruto', function () {
    $r = pcCalc(['reachableExtremes' => false, 'extremeGain' => 1.8])
        ->evaluate(5.0, [pcObs('2026-03-01 10:00', 10.0)], pcAt('2026-03-01 10:00'));

    expect($r['value'])->toBe(6.6667)->and($r['raw'])->toBe(6.6667);
});

it('ligado, expande a distância do neutro (bruto 6,6667 → 8,0) sem passar de 0 a 10', function () {
    $c = pcCalc(['reachableExtremes' => true, 'extremeGain' => 1.8]);
    $at = pcAt('2026-03-01 10:00');

    $up = $c->evaluate(5.0, [pcObs('2026-03-01 10:00', 10.0)], $at);
    $down = $c->evaluate(5.0, [pcObs('2026-03-01 10:00', 0.0)], $at);
    $many = $c->evaluate(5.0, array_fill(0, 30, pcObs('2026-03-01 10:00', 10.0)), $at);

    expect($up['value'])->toBe(8.0)->and($up['raw'])->toBe(6.6667)
        ->and($down['value'])->toBe(2.0)
        ->and($many['value'])->toBe(10.0);
});

it('com a expansão, 9–10 passa a ser atingível por quem recebe sempre a nota máxima', function () {
    $obs = array_map(fn ($d) => ['at' => pcAt('2026-01-01 00:00')->addDays($d), 'level' => 10.0], range(0, 161, 7));
    $at = pcAt('2026-01-01 00:00')->addDays(161);

    $plain = pcCalc(['priorWeight' => 1.0])->evaluate(5.0, $obs, $at)['value'];
    $boost = pcCalc(['priorWeight' => 1.0, 'reachableExtremes' => true, 'extremeGain' => 1.8])->evaluate(5.0, $obs, $at)['value'];

    expect($plain)->toBeLessThan(9.5)->and($boost)->toBe(10.0);
});

it('rejeita ganho menor que 1 e força negativa', function (array $bad) {
    pcCalc($bad);
})->with([
    'ganho menor que 1' => [['extremeGain' => 0.9]],
    'força negativa' => [['asymmetryStrength' => -0.1]],
])->throws(InvalidArgumentException::class);
