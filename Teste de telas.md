# Telas do Sistema

## Geral

- [ ] `home.blade.php` // não está adaptada para celular // *Sobre Nós Companhia Parceiros ▼ Ajuda* não funcionam (e a navbar em geral)
- [ ] `welcome.blade.php`?

## Autenticação

- [V] `auth/login.blade.php` 
- [V] `auth/register.blade.php` //deve apresentar um erro ao cadastrrar um aluno com cpf igual

### Painéis

- [ ] `components/panel/coordinator.blade.php` //"ver mais" não funciona, "ir para a programação" não funciona, rendimento e relatório igualmente, não adaptado ao mobile
- [V] `components/panel/director.blade.php`
- [ ] `components/panel/guardian.blade.php` //design provavelmente incompleto, mas funcional, e talvez seja necessário exibir mais informações sobre a criança
- [ ] `components/panel/layout.blade.php`
- [ ] `components/panel/teacher.blade.php` //maior parte dos botões nao funcionam, sem cores

### Busca

- [ ] `components/search/employee-list.blade.php`
- [V] `components/search/student-list.blade.php` verificar com mais calma se todas as variáveis são exibidas
- [ ] `components/search/teacher-list.blade.php` //apresenta falhas

## Coordenador

- [X] `coordinator/chat.php`
- [X] `coordinator/forum.php`
- [ ] `coordinator/panel.blade.php`
- [ ] `coordinator/profile.blade.php` não exibe o CSS
- [V] `coordinator/schedules.blade.php`

### Relatórios

- [ ] `coordinator/reports/index.blade.php` ? tela inexistente

### Alunos

- [ ] `coordinator/student/index.blade.php` ?
- [ ] `coordinator/student/show.blade.php` ?

### Professores

- [ ] `coordinator/teacher/index.blade.php`
- [ ] `coordinator/teacher/register.blade.php`
- [ ] `coordinator/teacher/show.blade.php`

## Diretor

- [ ] `director/index.blade.php`

### Funcionários

- [ ] `director/employee/index.blade.php`
- [ ] `director/employee/register.blade.php`
- [ ] `director/employee/show.blade.php`

## Responsável

- [ ] `guardian/chat.blade.php`
- [ ] `guardian/forum.blade.php`
- [ ] `guardian/panel.blade.php`
- [ ] `guardian/profile.blade.php`

### Alunos

- [ ] `guardian/student/index.blade.php`
- [ ] `guardian/student/register.blade.php`

## Professor

- [X] `teacher/chat.blade.php` 
- [X] `teacher/forum.blade.php`
- [ ] `teacher/panel.blade.php` ?
- [V] `teacher/profile.blade.php`
- [V] `teacher/schedule.blade.php` visualmente pronto

## Progresso

**Total de telas/arquivos:** 35

- [ ] Geral — 2
- [ ] Autenticação — 2
- [ ] Componentes — 10
- [ ] Coordenador — 10
- [ ] Diretor — 4
- [ ] Responsável — 6
- [ ] Professor — 5