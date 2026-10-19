# Implementação do Sistema de Proficiências — Zuni Laravel

## 1. Contexto do projeto

Você está trabalhando no projeto Zuni, um sistema educacional infantil desenvolvido em Laravel, com foco na comunicação entre professores, coordenadores e responsáveis, além do acompanhamento do desenvolvimento dos alunos.

Sua tarefa é implementar o **sistema de atribuição, atualização, acompanhamento e análise de proficiências dos alunos**, respeitando integralmente a arquitetura, os padrões de código, as regras de negócio e as tecnologias já utilizadas no projeto.

O sistema deve permitir que professores registrem observações sobre habilidades dos alunos. Essas observações alterarão suas proficiências de acordo com o tipo de avaliação, a intensidade informada e o intervalo de tempo entre observações relacionadas.

Além disso, o sistema deverá calcular as médias das proficiências das salas e manter um histórico que permita visualizar a evolução dos alunos e das turmas.

**Não comece implementando imediatamente. Inspecione o projeto, identifique as estruturas existentes e estabeleça um plano de implementação antes de modificar qualquer arquivo.**

---

## 2. Etapa obrigatória: inspeção prévia do projeto

Antes de escrever ou modificar código, examine a estrutura existente e identifique:

- Versões do Laravel, PHP, Livewire e demais dependências relevantes.
- Models, migrations, relacionamentos e enums existentes relacionados a:
  - `StudentSheet`;
  - `Classroom`;
  - usuários e papéis de usuário;
  - professores e suas atribuições;
  - relatórios (`Report`), caso sejam relevantes para a integração;
  - registros de desempenho ou avaliações que já existam.
- Controllers, rotas, Form Requests, Policies, Services, Events, Listeners e Jobs relacionados ao domínio.
- Componentes Livewire e views Blade utilizados nas páginas de alunos e salas.
- Sistema de autenticação e autorização por papel de usuário.
- Padrões de nomenclatura, organização de pastas, validação, transações e tratamento de exceções.
- Sistema de gráficos já utilizado, caso exista.
- Testes automatizados e convenções adotadas pelo projeto.

Considere especialmente as entidades `StudentSheet` e `Classroom`, pois elas fazem parte do domínio já definido para o Zuni.

Não presuma que os nomes das tabelas, chaves estrangeiras, enums ou relacionamentos sejam iguais aos nomes sugeridos neste documento. Verifique as implementações reais antes de definir a solução.

### Regras de inspeção

1. Não sobrescreva implementações existentes sem compreender sua finalidade.
2. Não crie models, tabelas, componentes ou serviços duplicados.
3. Reutilize estruturas existentes quando isso for coerente com as responsabilidades de cada entidade.
4. Se a arquitetura existente divergir das sugestões deste documento, adapte a implementação sem comprometer os requisitos funcionais.
5. Caso exista uma decisão arquitetural que possa causar perda de dados, incompatibilidade ou alteração significativa de comportamento, explique o problema antes de prosseguir.
6. Preserve alterações locais existentes e não reverta mudanças que não façam parte desta tarefa.
7. Não execute operações destrutivas no banco de dados.
8. Após a inspeção, apresente um plano conciso, indicando os arquivos que pretende criar ou alterar, suas responsabilidades e as principais decisões arquiteturais. Em seguida, execute a implementação, salvo se houver um impedimento que exija esclarecimento.

---

## 3. Objetivo funcional

Cada aluno terá um conjunto de proficiências que representam seu desenvolvimento em habilidades educacionais e comportamentais.

Cada proficiência começa com valor neutro igual a `5`.

A escala será limitada ao intervalo de `0` a `10`:

- `0`: menor nível possível na escala.
- `5`: nível neutro inicial.
- `10`: maior nível possível na escala.

Os valores poderão conter casas decimais, pois as observações poderão produzir alterações fracionárias.

Exemplos de valores válidos: `4`, `5`, `5.5`, `6.25` e `10`.

A implementação deverá impedir que uma proficiência ultrapasse os limites definidos.

Cada habilidade deverá ser cadastrável no banco de dados, sem exigir a criação de novas colunas na tabela de alunos sempre que uma habilidade for adicionada.

O sistema deverá permitir:

1. Cadastrar e administrar habilidades.
2. Consultar as proficiências atuais de um aluno.
3. Registrar observações positivas e negativas.
4. Calcular a alteração da proficiência com base na recorrência e no tempo transcorrido.
5. Consultar o histórico de avaliações e alterações.
6. Corrigir ou anular registros de forma auditável.
7. Calcular a média de proficiência das salas.
8. Armazenar snapshots históricos das médias.
9. Consultar a evolução individual e coletiva ao longo do tempo.

---

## 4. Modelagem e responsabilidades das entidades

Avalie a necessidade das entidades abaixo e adapte seus nomes e relacionamentos aos padrões existentes.

### 4.1. `StudentSheet`

A ficha do aluno deverá continuar responsável pelos dados gerais e pelos relacionamentos já existentes.

Evite adicionar uma grande quantidade de campos de proficiência diretamente à entidade principal.

A relação com as proficiências deverá ser implementada por meio de relacionamentos apropriados, preferencialmente com registros separados para cada habilidade.

### 4.2. `Proficiency`

Crie, ou adapte uma entidade, para representar as habilidades cadastradas no sistema.

Cada habilidade deverá conter, conforme necessário:

- Identificador.
- Nome.
- Descrição.
- Estado ativo/inativo.
- Datas de criação e atualização.

Considere também a possibilidade de categorizar as habilidades, caso exista uma estrutura compatível no projeto.

As habilidades deverão ser cadastráveis no banco de dados. Evite utilizar um enum para representar a lista completa de habilidades, pois isso exigiria alterações de código sempre que uma nova habilidade fosse adicionada.

Uma habilidade inativada não deverá desaparecer do histórico dos alunos que já foram avaliados nela.

### 4.3. `StudentProficiency`

Crie, ou adapte, uma entidade responsável por representar o estado atual de uma habilidade para determinado aluno.

Cada registro deverá relacionar um `StudentSheet` a uma `Proficiency`.

Campos sugeridos:

- Identificador.
- Referência ao aluno.
- Referência à habilidade.
- Valor atual da proficiência.
- Data da última observação válida.
- Datas de criação e atualização.

O valor inicial será `5`.

Garanta a unicidade da combinação aluno + habilidade, salvo se a arquitetura existente justificar uma modelagem diferente.

A criação dos registros iniciais deverá respeitar as regras do projeto. Avalie se as proficiências devem ser inicializadas quando um aluno é aprovado, matriculado ou vinculado à turma, evitando criar registros para alunos ainda não elegíveis.

Não crie proficiências duplicadas quando o mesmo evento for executado mais de uma vez.

### 4.4. `ProficiencyObservation`

Crie, ou adapte, uma entidade que registre cada avaliação feita por um professor.

Essa entidade será o histórico de origem das alterações das proficiências.

Campos sugeridos:

- Identificador.
- Referência ao aluno.
- Referência à habilidade avaliada.
- Referência ao usuário responsável pela observação.
- Tipo da observação.
- Intensidade informada.
- Justificativa.
- Data e hora da observação.
- Valor anterior à alteração.
- Alteração efetivamente aplicada.
- Valor resultante.
- Estado do registro, caso seja necessário distinguir avaliações válidas, corrigidas e anuladas.
- Referência ao registro original, caso seja adotada uma estratégia de correção por novos registros.
- Datas de criação e atualização.

Os campos definitivos deverão refletir os padrões do projeto e as necessidades de auditoria.

**Importante:** o histórico não poderá ser reduzido a um simples registro do valor atual. Deve ser possível explicar quais observações contribuíram para determinada alteração.

Considere armazenar a intensidade solicitada, o peso temporal aplicado, a alteração calculada e a alteração efetiva após a aplicação dos limites da escala. Isso permitirá auditar o cálculo posteriormente.

Não permita que uma alteração administrativa no valor atual apague silenciosamente a origem histórica da proficiência.

### 4.5. `ClassroomPerformance`

Essa entidade deverá representar o desempenho agregado de uma sala.

Avalie se ela deve armazenar:

- Referência à sala.
- Referência à habilidade.
- Média atual da proficiência.
- Quantidade de alunos incluídos no cálculo.
- Data de referência.
- Dados de snapshots históricos, se aplicável.

A solução poderá separar o estado atual dos snapshots históricos em entidades diferentes, se isso melhorar a consistência ou evitar ambiguidades.

Não armazene apenas uma média geral da sala se isso impedir a análise individual por habilidade.

As médias deverão ser calculadas a partir das proficiências individuais válidas dos alunos pertencentes à sala no período considerado.

---

## 5. Regra de negócio: atualização das proficiências

O cálculo das proficiências deverá ser implementado em uma camada de domínio ou serviço dedicado, seguindo a arquitetura existente.

Não distribua a fórmula temporal entre controllers, views, componentes Livewire ou listeners independentes.

### 5.1. Observações positivas e negativas

Uma observação deverá identificar se o comportamento observado contribui positivamente ou negativamente para a habilidade avaliada.

Exemplos:

- O aluno demonstrou engajamento durante a aula: observação positiva.
- O aluno apresentou dificuldade recorrente em determinada habilidade: observação negativa.

O registro deverá permitir informar uma intensidade, de modo que observações diferentes possam produzir alterações de magnitudes diferentes.

A intensidade deverá ser validada e interpretada de forma consistente. Defina uma escala apropriada após inspecionar o projeto e documente sua semântica.

Não confunda a intensidade informada pelo professor com o peso temporal calculado pelo sistema.

### 5.2. Recorrência e decaimento temporal

O sistema deverá considerar o intervalo entre observações relacionadas à mesma habilidade do mesmo aluno.

A regra desejada é:

- Uma observação inicial possui um impacto de referência.
- Uma nova observação realizada em intervalo curto pode manter um impacto elevado.
- Uma observação semelhante realizada após intervalo maior terá seu impacto reduzido.
- Observações positivas e negativas devem obedecer à mesma regra temporal, invertendo apenas o sinal da alteração.
- O tempo transcorrido deverá influenciar o impacto, sem produzir saltos inesperados ou alterações arbitrárias.

Exemplo conceitual:

Um aluno começa com proficiência `5`.

1. O professor registra uma observação positiva de engajamento. A proficiência aumenta para `6`, se a intensidade e o peso temporal resultarem em alteração de `+1`.
2. O professor registra outra observação positiva pouco tempo depois. O sistema calcula uma nova alteração usando a regra de recorrência.
3. Uma observação semelhante realizada uma semana depois poderá ter peso de `0.5`, conforme os parâmetros de configuração adotados.
4. Uma observação negativa deverá seguir a mesma lógica, aplicando uma alteração negativa.

Esses exemplos ilustram o comportamento desejado, mas a fórmula definitiva deverá ser explícita, testável e documentada.

### 5.3. Fórmula configurável

Não fixe a regra temporal diretamente em diversos pontos do código.

Implemente um mecanismo centralizado e configurável para calcular o peso temporal.

A configuração deverá permitir ajustar os parâmetros sem precisar modificar os controllers ou as views.

Considere os seguintes elementos:

- Intervalo transcorrido desde a observação anterior relevante.
- Peso de referência.
- Taxa de decaimento.
- Intensidade da observação.
- Sinal positivo ou negativo.
- Limites mínimo e máximo da proficiência.
- Precisão numérica adotada.

O mecanismo deverá oferecer uma função conceitualmente semelhante a:

`alteracao = sinal × intensidade × peso_temporal`

A fórmula final poderá ser refinada conforme a implementação, desde que preserve os requisitos de recorrência, decaimento temporal e limites da escala.

Defina claramente o que significa uma observação realizada no mesmo dia, no dia seguinte, após uma semana e após intervalos maiores.

Evite que a alteração se torne negativa em módulo, inverta seu sinal ou apresente resultados indefinidos devido a datas inválidas.

### 5.4. Escopo da recorrência

A recorrência deverá ser calculada para o mesmo aluno e a mesma habilidade.

Observações de habilidades diferentes não poderão alterar diretamente o peso temporal umas das outras.

Avalie se observações anuladas ou corrigidas devem participar do cálculo temporal. O padrão recomendado é que apenas observações válidas e relevantes participem do cálculo.

Defina como o sistema se comportará ao inserir uma observação com data retroativa. Não permita que a inserção de uma avaliação antiga corrompa silenciosamente a proficiência atual ou a sequência cronológica do histórico.

Caso seja necessário recalcular observações posteriores, implemente um procedimento explícito, transacional e auditável.

### 5.5. Aplicação dos limites

Antes de persistir o resultado, garanta que o valor permaneça entre `0` e `10`.

Exemplos:

- `5 + 1 = 6`.
- `9.5 + 1` resulta em `10`.
- `0.5 - 1` resulta em `0`.

Registre a diferença entre a alteração teórica e a alteração efetivamente aplicada, caso os limites tenham reduzido o efeito.

Não arredonde prematuramente os valores intermediários do cálculo.

---

## 6. Correção e auditoria

O histórico deverá permitir corrigir avaliações incorretas sem apagar a rastreabilidade das operações.

Inspecione as permissões existentes e implemente uma estratégia compatível com o projeto.

Considere:

- Correção da justificativa ou dos dados descritivos.
- Anulação de uma observação lançada incorretamente.
- Registro de quem realizou a correção.
- Data e hora da correção.
- Motivo da correção ou anulação.
- Preservação dos valores anteriores e posteriores.
- Reprocessamento da proficiência atual, quando necessário.

Se uma observação for anulada, não deixe a proficiência atual refletindo permanentemente uma alteração que já não deveria ser considerada.

A solução deverá definir como reconstruir o valor atual a partir das observações válidas ou como registrar ajustes compensatórios auditáveis.

Não implemente exclusões físicas do histórico como mecanismo padrão de correção.

Utilize transações de banco de dados para garantir que a atualização da proficiência, o registro histórico e os efeitos agregados sejam consistentes.

Proteja a operação contra atualizações concorrentes, observações duplicadas e falhas parciais.

---

## 7. Cálculo das médias das salas

Implemente a atualização das médias de proficiência associadas a `ClassroomPerformance`.

### 7.1. Média atual

Para cada sala e habilidade, calcule a média aritmética das proficiências válidas dos alunos elegíveis pertencentes à sala.

A fórmula conceitual é:

`media = soma_das_proficiências / quantidade_de_alunos_incluídos`

Considere apenas os alunos que atendam às regras de elegibilidade definidas pelo sistema.

Se não houver alunos elegíveis, trate a média como inexistente (`null`) ou utilize outra representação coerente com os padrões existentes. Não divida por zero nem apresente `0` como se fosse uma média real.

Registre ou disponibilize a quantidade de alunos considerados para que os gráficos possam ser interpretados corretamente.

### 7.2. Atualização das médias

As médias deverão ser atualizadas quando ocorrerem eventos que afetem o resultado, incluindo:

- Criação de uma observação válida.
- Correção ou anulação de uma observação.
- Alteração da proficiência atual.
- Entrada ou saída de um aluno da sala.
- Mudança na elegibilidade de um aluno para o cálculo.

Escolha o mecanismo apropriado conforme a arquitetura existente: serviços de domínio, eventos, listeners, observers ou outra solução consistente.

Evite duplicar a mesma lógica em diferentes pontos de entrada.

### 7.3. Snapshots históricos

Além da média atual, o sistema deverá permitir consultar a evolução histórica das salas.

Crie snapshots por data ou período, de forma que seja possível comparar as médias ao longo do tempo.

Cada snapshot deverá registrar informações suficientes para identificar:

- A sala.
- A habilidade.
- A média calculada.
- A quantidade de alunos considerados.
- A data de referência.
- O momento em que o snapshot foi gerado.

Defina uma estratégia de geração dos snapshots compatível com o projeto. Não gere registros históricos duplicados a cada atualização de uma página.

Snapshots devem representar o estado na data de referência e não ser alterados silenciosamente quando as proficiências atuais mudarem.

Caso seja necessário corrigir dados históricos, utilize um procedimento explícito e auditável.

### 7.4. Integridade da média

Considere mudanças de sala, transferências, matrículas e alterações retroativas.

Se o projeto não possuir histórico de pertencimento às salas, não invente informações históricas que não podem ser reconstruídas. Documente as limitações e utilize a regra de elegibilidade que for possível sustentar com os dados existentes.

---

## 8. Cadastro e gerenciamento de habilidades

Implemente as funcionalidades administrativas necessárias para cadastrar e gerenciar as habilidades.

Respeite os padrões de interface e autorização existentes no Zuni.

As funcionalidades deverão permitir, conforme as permissões disponíveis:

- Listar habilidades.
- Consultar detalhes.
- Cadastrar uma habilidade.
- Editar nome e descrição.
- Ativar ou inativar uma habilidade.

Valide os dados recebidos no servidor.

Não permita que a inativação de uma habilidade apague as proficiências ou observações associadas a ela.

Não crie funcionalidades administrativas redundantes se já existir uma estrutura de gerenciamento equivalente no projeto.

---

## 9. Interface do professor

Integre o registro de observações às páginas de alunos já existentes.

Na página de um aluno, o professor deverá conseguir:

1. Consultar as habilidades cadastradas.
2. Visualizar o valor atual de cada habilidade.
3. Consultar o histórico de observações.
4. Selecionar a habilidade que deseja avaliar.
5. Informar se a observação é positiva ou negativa.
6. Informar a intensidade.
7. Adicionar uma justificativa.
8. Registrar a observação.
9. Visualizar o resultado atualizado.

Após o registro, a interface deverá refletir a proficiência atual e apresentar a alteração aplicada.

Sempre que possível, mostre também informações úteis para interpretar o resultado, como o valor anterior, o peso temporal aplicado e a data da última observação válida.

Não permita que o navegador determine ou envie livremente o valor final da proficiência. O cálculo deverá ser realizado no servidor.

Utilize os componentes Livewire e Blade existentes, respeitando as convenções de interface do projeto.

Não adicione dependências JavaScript desnecessárias.

---

## 10. Interface da sala e gráficos

Integre a visualização das proficiências às páginas de salas existentes.

A interface deverá permitir:

- Consultar as médias atuais por habilidade.
- Visualizar a evolução das médias ao longo do tempo.
- Selecionar uma habilidade para análise.
- Consultar a data e o período dos dados exibidos.
- Identificar a quantidade de alunos incluídos na média.

Reutilize a biblioteca de gráficos existente, se houver.

Caso não exista, avalie uma solução compatível com a stack atual antes de adicionar dependências.

Os gráficos deverão distinguir claramente valores atuais de snapshots históricos.

Não utilize dados fictícios na interface final. Dados de demonstração poderão ser usados exclusivamente em testes ou ambientes de desenvolvimento identificados como tal.

---

## 11. Segurança e permissões

Utilize o sistema de autenticação e os papéis de usuário existentes.

Inspecione as Policies, Gates, middleware e regras de autorização já implementadas antes de definir as permissões finais.

Como requisito inicial:

- Professores autorizados poderão registrar observações nos alunos sob sua responsabilidade.
- Professores não poderão avaliar alunos fora de seu escopo de autorização.
- Coordenadores poderão consultar os dados necessários para acompanhar o desenvolvimento dos alunos e das salas.
- Operações de correção, anulação e administração deverão respeitar permissões específicas.
- Responsáveis não poderão alterar diretamente as proficiências.
- Outros papéis não deverão receber permissões implícitas para registrar ou modificar avaliações.

Se as regras existentes definirem permissões mais específicas, preserve-as e documente eventuais conflitos.

Toda validação e autorização deverá ocorrer no servidor, inclusive nas ações Livewire.

Não confie apenas na visibilidade de botões ou links para proteger operações.

---

## 12. Banco de dados e migrations

Crie as migrations necessárias conforme a modelagem definida após a inspeção.

Garanta:

- Chaves estrangeiras coerentes.
- Índices para consultas frequentes.
- Restrições de unicidade apropriadas.
- Precisão numérica adequada para proficiências e médias.
- Regras de exclusão e integridade referencial que preservem o histórico.
- Índices para consultas por aluno, habilidade, sala, professor e data, conforme necessário.

Não utilize tipos inteiros para armazenar valores que precisam admitir casas decimais.

Evite usar `float` de forma que possa introduzir erros de precisão. Prefira tipos decimais adequados ao banco de dados e casts coerentes no Laravel.

Defina o comportamento das chaves estrangeiras para evitar a perda acidental de observações históricas.

Não execute migrations destrutivas nem operações que apaguem dados existentes.

---

## 13. Arquitetura e organização do código

Respeite a arquitetura já utilizada pelo projeto.

Como orientação, mantenha responsabilidades separadas entre:

- Models e relacionamentos.
- Validação de entradas.
- Autorização.
- Registro de observações.
- Cálculo temporal.
- Atualização das proficiências.
- Correção e anulação.
- Cálculo das médias.
- Geração de snapshots.
- Interface de alunos e salas.

Se houver uma camada de Services, Actions ou outra abstração de domínio já adotada, utilize-a de maneira consistente.

Evite:

- Controllers excessivamente complexos.
- Fórmulas de negócio dentro de views.
- Cálculos duplicados em vários componentes Livewire.
- Acesso indiscriminado ao banco dentro de componentes.
- Consultas repetidas desnecessárias.
- Dependências circulares.
- Lógica temporal baseada diretamente no relógio do sistema sem possibilidade de testes controlados.
- Criação automática de tabelas ou arquivos duplicados.
- Alterações em módulos não relacionados à tarefa.

Não introduza padrões arquiteturais novos apenas por preferência pessoal se a arquitetura existente já resolver adequadamente o problema.

---

## 14. Testes automatizados obrigatórios

Crie testes compatíveis com o framework de testes já utilizado no projeto.

### 14.1. Testes da escala

Verifique:

- Inicialização da proficiência em `5`.
- Incrementos positivos.
- Decrementos negativos.
- Valores fracionários.
- Limite inferior `0`.
- Limite superior `10`.
- Comportamento quando a alteração calculada ultrapassa os limites.

### 14.2. Testes temporais

Verifique:

- Primeira observação de uma habilidade.
- Observações repetidas em intervalos curtos.
- Observações realizadas após uma semana.
- Intervalos maiores.
- Intensidades diferentes.
- Observações positivas e negativas.
- Independência entre habilidades diferentes.
- Observações anuladas.
- Inserção de observações retroativas.
- Reprocessamento histórico, quando aplicável.

Utilize datas controladas nos testes. Não dependa do tempo real para verificar os resultados.

### 14.3. Testes de integridade

Verifique:

- Ausência de registros duplicados para aluno + habilidade.
- Integridade entre observação, aluno e habilidade.
- Atualização consistente da proficiência e do histórico.
- Correção e anulação auditáveis.
- Proteção contra atualizações concorrentes, conforme os mecanismos disponíveis.
- Ausência de duplicação de snapshots.
- Recalculo adequado após transferência ou alteração de elegibilidade de alunos.

### 14.4. Testes de médias

Verifique:

- Média correta entre vários alunos.
- Média individual por habilidade.
- Sala sem alunos elegíveis.
- Inclusão e saída de alunos.
- Atualização após nova observação.
- Atualização após correção ou anulação.
- Criação e consulta de snapshots históricos.
- Preservação dos valores históricos após mudanças futuras.

### 14.5. Testes de autorização

Verifique:

- Professor autorizado avaliando um aluno sob sua responsabilidade.
- Professor tentando avaliar um aluno fora de seu escopo.
- Responsável tentando registrar uma observação.
- Coordenador executando ações administrativas autorizadas.
- Usuário sem permissão tentando corrigir ou anular uma avaliação.

Não considere a implementação concluída se os testes falharem sem uma justificativa documentada.

---

## 15. Critérios de conclusão

A implementação somente será considerada concluída quando:

1. As habilidades forem cadastráveis no banco.
2. As proficiências iniciais forem criadas corretamente com valor `5`.
3. O registro de observações atualizar as proficiências de acordo com a fórmula implementada.
4. O decaimento temporal estiver centralizado, configurável e coberto por testes.
5. Os valores permanecerem no intervalo `0–10`.
6. Cada alteração possuir histórico suficiente para auditoria.
7. Correções e anulações preservarem a rastreabilidade e a consistência dos valores atuais.
8. As médias das salas forem calculadas corretamente por habilidade.
9. Os snapshots históricos puderem ser consultados sem serem confundidos com valores atuais.
10. A interface estiver integrada às páginas existentes de alunos e salas.
11. As permissões forem verificadas no servidor.
12. Os testes automatizados relevantes forem executados.
13. As migrations forem compatíveis com a estrutura atual do banco.
14. A implementação não quebrar funcionalidades existentes.

---

## 16. Entrega final

Após concluir o trabalho, apresente um relatório objetivo contendo:

### A. Arquitetura implementada
Liste as entidades, os relacionamentos e as responsabilidades de cada uma.

### B. Arquivos criados ou modificados
Informe os caminhos dos arquivos e explique brevemente suas funções.

### C. Regra matemática
Documente a fórmula temporal efetivamente implementada, os parâmetros de configuração, os limites e exemplos numéricos de cálculo.

### D. Fluxo de execução
Explique o caminho percorrido desde o registro da observação pelo professor até a atualização da proficiência, da média atual da sala e dos snapshots.

### E. Banco de dados
Liste as migrations e explique como executá-las sem destruir dados existentes.

### F. Testes
Informe quais testes foram executados, quais passaram e quais falharam. Não declare testes como aprovados se eles não foram realmente executados.

### G. Decisões e limitações
Registre as decisões arquiteturais tomadas, as limitações encontradas e os pontos que dependem de futuras definições.

### H. Como utilizar
Explique como cadastrar habilidades, registrar observações, consultar o histórico e visualizar o desempenho das salas.

**Instrução final:** trabalhe diretamente no projeto existente. Primeiro inspecione, depois planeje e então implemente. Priorize integridade dos dados, rastreabilidade, consistência temporal, segurança e compatibilidade com a arquitetura atual. Não substitua código existente sem necessidade e não declare como concluída nenhuma funcionalidade que não tenha sido implementada e verificada.