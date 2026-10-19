<?php

/*
|--------------------------------------------------------------------------
| Proficiências — modelo de média ponderada pelo tempo
|--------------------------------------------------------------------------
| Cada observação vira um NÍVEL na escala e tem peso fixo. O valor exibido é a
| média ponderada entre um valor inicial (neutro, com peso baixo) e as observações,
| e o tempo entra só na LEITURA: observações antigas valem menos que as recentes.
|
|   nível         = clamp(neutro ± intensidade × level_per_intensity, min, max)
|   recência(obs) = 0.5 ^ (dias_desde_a_observação / half_life_days)
|   bruto         = (prior_weight × valor_inicial + Σ peso × recência × nível)
|                   / (prior_weight + Σ peso × recência)
|   valor exibido = bruto                                         (extremos desligados)
|                 = clamp(neutro + extreme_gain × (bruto − neutro), min, max)   (ligados)
|
| Chaves opcionais (cada uma liga/desliga por .env):
|   - PROFICIENCY_ASYMMETRIC_WEIGHTS: o peso de uma observação depende do valor do aluno no
|     momento do registro. Em 5 o peso para cima e para baixo é o mesmo; abaixo de 5 subir pesa
|     mais e descer pesa menos; acima de 5 subir pesa menos e descer pesa mais.
|       peso_cima  = peso × max(mín, 1 + força × (neutro − valor) / distância)
|       peso_baixo = peso × max(mín, 1 + força × (valor − neutro) / distância)
|   - PROFICIENCY_REACHABLE_EXTREMES: expande a distância do neutro para tornar 9–10 (e 0–1) atingíveis.
|
| Todos os números abaixo podem ser ajustados por .env, sem alterar o código. O
| simulador (arquivo "simulador") usa exatamente esta fórmula para testar combinações.
*/

return [

    'scale' => [
        'min' => 0.0,
        'max' => 10.0,
    ],

    // Valor neutro: ponto de partida de todo aluno e nível de uma observação "sem desvio".
    'neutral' => (float) env('PROFICIENCY_NEUTRAL', 5.0),

    // Meia-vida da LEITURA (dias): após esse tempo, uma observação vale metade.
    'half_life_days' => (float) env('PROFICIENCY_HALF_LIFE_DAYS', 30.0),

    // Peso do valor inicial, em "observações equivalentes".
    'prior_weight' => (float) env('PROFICIENCY_PRIOR_WEIGHT', 1.0),

    // Pesos assimétricos (chave) e sua força (0 = igual; 1 = máxima).
    'asymmetric_weights' => (bool) env('PROFICIENCY_ASYMMETRIC_WEIGHTS', true),
    'asymmetry_strength' => (float) env('PROFICIENCY_ASYMMETRY_STRENGTH', 1.0),
    'min_asymmetry_multiplier' => 0.1,

    // Extremos alcançáveis (chave) e o ganho nas pontas (1 = sem expansão).
    'reachable_extremes' => (bool) env('PROFICIENCY_REACHABLE_EXTREMES', true),
    'extreme_gain' => (float) env('PROFICIENCY_EXTREME_GAIN', 1.8),

    // Botões de avaliação « ‹ › »: intensidade do botão leve (‹ ›) e do forte (« »).
    'buttons' => [
        'mild' => (float) env('PROFICIENCY_BUTTON_MILD', 2.5),
        'strong' => (float) env('PROFICIENCY_BUTTON_STRONG', 5.0),
    ],

    // Peso de cada observação (fixo; não depende do intervalo entre registros).
    'observation_weight' => (float) env('PROFICIENCY_OBSERVATION_WEIGHT', 1.0),

    // Pontos de nível por 1,0 de intensidade (1 = intensidade 5 vai de 5 a 10 ou de 5 a 0).
    'level_per_intensity' => (float) env('PROFICIENCY_LEVEL_PER_INTENSITY', 1.0),

    'intensity' => [
        'min' => 0.1,
        'max' => 5.0,
    ],

    // Régua de intensidade exibida ao professor (uniformiza os critérios).
    'rubric' => [
        1 => 'Ocasional ou leve',
        2 => 'Aparece às vezes',
        3 => 'Frequente, padrão claro',
        4 => 'Consistente',
        5 => 'Muito marcante ou sistemático',
    ],

    // Confiança = observações efetivas (soma dos pesos recentes): abaixo de low = baixa; abaixo de high = média.
    'confidence' => [
        'low' => 2.0,
        'high' => 4.0,
    ],

    // Casas decimais ao persistir valores.
    'precision' => 4,
];
