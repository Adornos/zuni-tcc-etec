# Relatório — Sistema de Proficiências (Zuni) · modelo de média ponderada pelo tempo

> Status de verificação: **nada foi executado** (sem PHP no ambiente onde o código foi escrito). Valores esperados nos testes foram conferidos em Python com a mesma fórmula.

## Atualização — chaves opcionais e tela de avaliação

Parâmetros calibrados com professores (padrão do `.env`): força da assimetria **1**, ganho nas pontas **1,8**, meia-vida **30**, peso do valor inicial **1**, botão leve **2,5**, botão forte **5**.

| Chave (`.env`) | Padrão | O que faz |
|---|---|---|
| `PROFICIENCY_ASYMMETRIC_WEIGHTS` | `true` | peso da observação depende do valor do aluno no registro |
| `PROFICIENCY_ASYMMETRY_STRENGTH` | `1` | 0 = igual; 1 = máxima (em 2,5: subir ×1,5 e descer ×0,5; em 7,5 o inverso) |
| `PROFICIENCY_REACHABLE_EXTREMES` | `true` | expande a distância do neutro para 9–10 (e 0–1) serem atingíveis |
| `PROFICIENCY_EXTREME_GAIN` | `1.8` | valor exibido = 5 + ganho × (bruto − 5), limitado a 0–10 |
| `PROFICIENCY_BUTTON_MILD` / `_STRONG` | `2.5` / `5` | intensidade dos botões ‹ › e « » |

Desligando as duas chaves o comportamento volta ao do modelo anterior (média ponderada simples).

**Peso assimétrico:** `peso_cima = peso × max(0,1; 1 + força × (5 − v)/5)` e `peso_baixo = peso × max(0,1; 1 + força × (v − 5)/5)`, com `v` = valor exibido do aluno no instante do registro. O peso calculado é gravado na observação (fixo daí em diante, o que mantém a auditoria).

**Tela de avaliação (professor, primeira coisa da página do aluno)** — componente `student-evaluation`:
- Desktop: grade 2×2 (radar à esquerda, parâmetros à direita, justificativa embaixo). Celular: uma coluna com 3 linhas (radar, parâmetros, justificativa).
- Cada parâmetro (habilidade ativa) tem `« ‹ [peso] › »`; o campo central aceita um valor manual de **−5 a 5** e os botões preenchem os valores pré-definidos (clicar de novo limpa).
- Ao escolher, o radar mostra a linha tracejada **"Após a avaliação"**, calculada no servidor com a mesma regra do registro (`ProficiencyService::previewEntry`), sem gravar nada.
- "Salvar avaliação" grava todas as habilidades escolhidas de uma vez, com a mesma justificativa (`recordBatch`: tudo ou nada, idempotente por token).
- A autorização é reaplicada em toda ação (Gate `observe`); o histórico e a anulação continuam no componente `student-proficiencies`, que atualiza sozinho após salvar.

---

## A. O modelo

Cada observação vira um **nível** e tem **peso fixo**; o valor exibido é a média ponderada entre o valor inicial (peso baixo) e as observações, e o **tempo entra só na leitura**.

```
nível         = clamp(neutro ± intensidade × level_per_intensity, 0, 10)
recência(obs) = 0.5 ^ (dias_desde_a_observação / half_life_days)
valor         = (prior_weight × valor_inicial + Σ peso × recência × nível)
                / (prior_weight + Σ peso × recência)
confiança     = baixa / média / alta, pela soma dos pesos recentes (observações efetivas)
```

| Parâmetro (`.env`) | Padrão | Efeito |
|---|---|---|
| `PROFICIENCY_NEUTRAL` | 5 | valor inicial e nível "sem desvio" |
| `PROFICIENCY_HALF_LIFE_DAYS` | 30 | após esse tempo uma observação vale metade |
| `PROFICIENCY_PRIOR_WEIGHT` | 2 | peso do valor inicial, em "observações equivalentes" |
| `PROFICIENCY_OBSERVATION_WEIGHT` | 1 | peso de cada observação (fixo) |
| `PROFICIENCY_LEVEL_PER_INTENSITY` | 1 | pontos de nível por 1,0 de intensidade |

Também em `config/proficiency.php`: faixa da intensidade (0,1 a 5), a **régua** de intensidade mostrada ao professor e os limites de confiança (2 e 4).

Propriedades: o valor fica sempre dentro da escala **sem cortes artificiais**; a ordem de lançamento não importa (observação retroativa é aceita); sem observações o valor volta ao inicial; poucas observações recentes = valor perto do neutro e confiança baixa.

**Importante:** os padrões (30 dias, peso 2) são os simulados. A análise indicou que meia-vida de 60 a 90 dias reduz a dependência da frequência de registro; teste no simulador antes de decidir.

## B. Arquivos desta etapa

**Novos**
- `app/Services/Proficiency/ProficiencyValueResolver.php`: calcula o valor de alunos em qualquer instante.
- `app/Console/Commands/RefreshProficiencies.php` (`proficiency:refresh`).
- `database/migrations/2026_10_12_200000_adapt_proficiencies_to_time_weighted_model.php`: só adiciona colunas.

**Reescritos**
- `config/proficiency.php`, `ProficiencyCalculator`, `ProficiencyService`, `ClassroomAverageService`, `SnapshotProficiencies`.
- `⚡student-proficiencies` (tabela com observações válidas e confiança, régua de intensidade, histórico com nível e peso).
- `tests/Unit/ProficiencyCalculatorTest.php` (16) e `tests/Feature/ProficiencyTest.php` (34).

**Alterados**
- `Models/ProficiencyObservation` e `Models/StudentProficiency` (colunas novas).

## C. Banco de dados

A migration `..._200000_...` **não apaga nada**:
- `proficiency_observations.level` (nullable) e preenchimento das linhas existentes (neutro ± intensidade); o `weight` das linhas existentes passa a ser o peso fixo.
- `student_proficiencies`: `effective_count`, `observations_count`, `calculated_at`. O `base_value` passa a ser o valor inicial (inclusive os valores importados do legado).
- `proficiency_recalculation_logs`: não é mais usada (anular não reprocessa em cadeia); a tabela é mantida.

`requested_delta`, `applied_delta`, `value_before` e `value_after` agora descrevem o **efeito no instante do registro** (distância do nível ao valor atual, movimento real, e os dois valores). As linhas antigas mantêm os números do modelo anterior, mas o valor atual é sempre recalculado de nível + peso + data.

```
php artisan migrate               # não use migrate:fresh
php artisan proficiency:refresh   # recalcula o cache e as médias com o novo modelo
```

## D. Fluxo

1. O professor registra: habilidade, tipo, intensidade (pela régua), data e justificativa.
2. A transação trava a série, calcula o nível, grava a observação com o efeito no registro, atualiza o cache e a média da sala.
3. **Anular** (autor ou coordenador, com motivo): marca como `voided`, recalcula o valor sem ela e atualiza a média. Nada é apagado.
4. `proficiency:snapshot` (23:55): atualiza o cache (decaimento do tempo), as médias e grava o snapshot do dia. O snapshot de uma data usa as observações **até o fim daquela data**.
5. A página do aluno calcula o valor **ao vivo**; as médias das salas usam o mesmo cálculo.

## E. Testes (escritos, não executados)

Unitários (16): nível, meia-vida, observação futura, ordem irrelevante, peso individual, dependência da frequência, retorno ao inicial, confiança e validações.
Feature (34): níveis, tempo, retroativas, cache, integridade (único, anulação auditável, token, FK), médias, snapshots (inclusive data passada), comando, autorização, CRUD e integrações.
**Não cobertos:** as ações Livewire (`record`, `confirmVoid`) e a concorrência real (`lockForUpdate` é ignorado pelo SQLite dos testes).

## F. Decisões e limites

- **Compressão da escala:** com intensidade 1–2 o valor fica perto do neutro; para separar bem os alunos a régua precisa ser usada (padrão claro = 3 a 5).
- **Dependência da frequência:** diminui mas não some; meia-vida maior reduz.
- **Cache x ao vivo:** o valor do cache (radar da página do aluno) pode ter até um dia de defasagem; a tabela de proficiências da página do aluno e as médias das salas são calculadas ao vivo.
- **Sem histórico de turma:** um snapshot de data passada usa a composição atual da sala.
- **Scheduler:** precisa estar ativo (`schedule:work` ou cron).
- **Aberto desde antes:** a página do aluno ainda pode ser aberta por qualquer professor ou responsável pela URL.
