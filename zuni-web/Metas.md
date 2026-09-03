# Finalização do TCC — ZUNI

## 🎯 Objetivo geral

Finalizar todas as funcionalidades, integrações e testes do sistema **ZUNI** em 1 mês.

### Estratégia

- **30 dias de prazo**
- **24 dias produtivos**
- **6 dias de margem/revisão**
- Até o **Dia 25:** todas as funcionalidades devem existir.
- **Dias 26–30:** integração, testes, correções, preparação para apresentação, etc.

---

# 📊 Metas semanais

| Semana | Foco                      | Meta                            |
| ------ | ------------------------- | ------------------------------- |
| **1**  | Cadastros + salas         | Estrutura pronta                |
| **2**  | Professor + aluno         | Funções pedagógicas prontas     |
| **3**  | Coordenador + comunicação | Funções administrativas prontas |
| **4**  | Integração + testes       | **100% finalizado**             |

---

# 🗓️ SEMANA 1 — Estrutura e usuários

> **Objetivo:** concluir cadastros, salas e estrutura básica do sistema.

## Dia 1 — Cadastro de professores

- [x] Cadastro de professores
- [x] Validação do cadastro
- [x] Testar login/acesso do professor

### Observações:
- [x] Finalização da edição do cadastro pelo professor
- [x] Padrõnização de diretórios (Uniformização dos nomes e importações dos components do painel)

## Dia 2 — Cadastro de coordenador

- [x] Cadastro de coordenador
- [x] Validação do cadastro
- [x] Testar permissões do coordenador

### Observações:
- [ ] Verificar permissão de criação de funcionários nos formulários (evitar injection)
- [x] Uniformizar status e outras informações em StudentSheets
- [x] Mudar cor de status conforme o próprio status nas views do aluno
- [ ] Resolver formulário que indica "Não informado" em vez de "Não"
- [x] Resolver transcrição do dado do banco para linguagem real com os métodos do enum

## Dia 3 — Criação de salas

- [x] Criação de salas
- [x] Definir relacionamento sala ↔ alunos
- [x] Definir relacionamento sala ↔ professor

### Observações:

- [ ] Testar o funcionamento de todas as telas
- [ ] Testar o funcionamento do campo de perfil de todos os usuários
- [ ] Padronizar série dos alunos e das salas

## Dia 4 — Atribuição de alunos

- [ ] Atribuição de professores às salas
- [ ] Atribuição de alunos às salas
- [ ] Visualização dos alunos por sala

## Dia 5 — Atribuição de aulas

- [ ] Atribuição de aulas
- [ ] Estrutura de horários
- [ ] Verificar conflitos/duplicidades

## Dia 6 — Tela do responsável

- [ ] Edição de cadastro
- [ ] Visualização de aluno
- [ ] Visualização de proficiências por aluno

## Dia 7 — 🔄 Revisão

- [ ] Testar cadastros
- [ ] Testar relacionamentos
- [ ] Corrigir bugs da semana
- [ ] Atualizar checklist

### 🎯 Resultado esperado

**Usuários + salas + estrutura básica funcionando.**

---

# 🗓️ SEMANA 2 — Professor e aluno

> **Objetivo:** construir a principal interação pedagógica do sistema.

## Dia 8 — Professor → Aluno

- [ ] Visualização de horários
- [ ] Visualização de sala
- [ ] Visualização de alunos por sala

## Dia 9 — Proficiências

- [ ] Atribuição de proficiência para aluno
- [ ] Visualização das proficiências
- [ ] Visualização de proficiências por aluno

## Dia 10 — Gráficos

- [ ] Gráfico de desempenho
- [ ] Gráfico de proficiências por aluno
- [ ] Testar atualização dos gráficos

## Dia 11 — Relatórios do professor

- [ ] Geração de relatórios pelo professor
- [ ] Relatórios atribuídos ao aluno
- [ ] Testar geração dos dados

## Dia 12 — Reuniões

- [ ] Implementar reuniões
- [ ] Relacionar reunião entre professor/responsável
- [ ] Visualização das reuniões

## Dia 13 — Fórum

- [ ] Geração de textos para o fórum pelo professor
- [ ] Verificação da exibição dos textos

## Dia 14 — 🔄 Revisão

- [ ] Testar fluxo Professor → Aluno
- [ ] Testar proficiências
- [ ] Testar gráficos
- [ ] Testar relatórios
- [ ] Corrigir bugs

### 🎯 Resultado esperado

**O professor deve conseguir visualizar suas salas e alunos, atribuir proficiências e gerar informações sobre os alunos.**

---

# 🗓️ SEMANA 3 — Coordenador e comunicação

> **Objetivo:** finalizar as funções administrativas e implementar a comunicação.

## Dia 15 — Matrículas

- [ ] Edição de matrículas
- [ ] Testar fluxo de aprovação → edição

## Dia 16 — Horários

- [ ] Geração de horários
- [ ] Visualização de horários pelo professor
- [ ] Testar relação professor/sala/horário

## Dia 17 — Relatórios

- [ ] Geração de relatórios atribuídos
- [ ] Visualização dos relatórios
- [ ] Testar permissões

## Dia 18 — Gráficos do coordenador

- [ ] Gráfico de proficiências da escola
- [ ] Gráfico de proficiências da sala
- [ ] Gráfico de proficiências do aluno

## Dia 19 — Comunicação

- [ ] Estabelecimento de comunicação Coordenador → Aluno
- [ ] Pedido de comunicação Professor → Responsável

## Dia 20 — Chat

- [ ] Implementação do chat
- [ ] Envio de mensagens
- [ ] Recebimento/visualização de mensagens

## Dia 21 — 🔄 Revisão

- [ ] Testar Coordenador → Aluno
- [ ] Testar Coordenador → Professor
- [ ] Testar Professor → Responsável
- [ ] Corrigir bugs

### 🎯 Resultado esperado

**Todos os principais tipos de usuários devem conseguir realizar suas funções básicas.**

---

# 🗓️ SEMANA 4 — Integração e finalização

> **Objetivo:** integrar todo o sistema, encontrar bugs e deixar o ZUNI pronto para apresentação.

## Dia 22 — Integração NativePHP

- [ ] Funcionamento do NativePHP
- [ ] Testar execução do sistema
- [ ] Verificar rotas
- [ ] Verificar autenticação

## Dia 23 — Integração do responsável

- [ ] Cadastro de aluno
- [ ] Edição de cadastro
- [ ] Visualização de aluno
- [ ] Verificar proficiências

## Dia 24 — Integração do coordenador

- [ ] Matrículas
- [ ] Aprovações
- [ ] Edições
- [ ] Relatórios
- [ ] Horários
- [ ] Fórum
- [ ] Gráficos

## Dia 25 — Integração do professor

- [ ] Horários
- [ ] Sala
- [ ] Alunos
- [ ] Proficiências
- [ ] Relatórios
- [ ] Fórum

> 🚨 **META CRÍTICA:** até o final do Dia 25, todas as funcionalidades devem existir.

---

# 🛡️ DIAS DE MARGEM

## Dia 26 — Fluxos completos

Testar o sistema como um usuário real:

- [ ] Responsável cadastra aluno
- [ ] Aluno aparece para o coordenador
- [ ] Coordenador aprova matrícula
- [ ] Coordenador atribui sala
- [ ] Professor visualiza aluno
- [ ] Professor atribui proficiência
- [ ] Responsável visualiza proficiência
- [ ] Professor gera relatório
- [ ] Comunicação funciona

## Dia 27 — 🐛 Correção de bugs

- [ ] Corrigir erros críticos
- [ ] Corrigir erros de banco de dados
- [ ] Corrigir problemas de autenticação
- [ ] Corrigir problemas de permissões
- [ ] Corrigir problemas visuais

## Dia 28 — 🧪 Teste geral

- [ ] Testar Responsável
- [ ] Testar Aluno
- [ ] Testar Professor
- [ ] Testar Coordenador
- [ ] Testar todas as rotas
- [ ] Testar formulários
- [ ] Testar gráficos
- [ ] Testar relatórios
- [ ] Testar comunicação

## Dia 29 — 🧹 Polimento

- [ ] Melhorar interface
- [ ] Padronizar textos
- [ ] Remover código desnecessário
- [ ] Verificar responsividade
- [ ] Verificar mensagens de erro
- [ ] Verificar banco de dados
- [ ] Verificar segurança/permissões

## Dia 30 — 🏁 ENTREGA

- [ ] Executar o sistema do zero
- [ ] Fazer uma demonstração completa
- [ ] Conferir todas as funcionalidades
- [ ] Conferir checklist
- [ ] Corrigir último bug crítico
- [ ] Fazer backup do projeto
- [ ] Fazer backup do banco de dados
- [ ] **ZUNI pronto para apresentação**

---