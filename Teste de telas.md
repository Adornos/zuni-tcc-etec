# Telas do Sistema

## Geral

- [ ] `home.blade.php` // não está adaptada para celular // *Sobre Nós Companhia Parceiros ▼ Ajuda* não funcionam (e a navbar em geral)
- [ ] `welcome.blade.php`?

## Autenticação

- [V] `auth/login.blade.php` mas não adaptada ao mobile
- [V] `auth/register.blade.php` //deve apresentar um erro ao cadastrrar um aluno com cpf igual

### Painéis

- [ ] `components/panel/coordinator.blade.php` //"ver mais" não funciona, "ir para a programação" não funciona, rendimento e relatório igualmente, não adaptado ao mobile
- [V] `components/panel/director.blade.php`


- [ ] `components/panel/guardian.blade.php` //design provavelmente incompleto, mas funcional, e talvez seja necessário exibir mais informações sobre a criança, cadastro de criança não adaptado ao mobile, erro SQLSTATE[42S22]: Column not found: 1054 Unknown column 'classrooms.sheet_id' in 'where clause' (Connection: mysql, Host: 127.0.0.1, Port: 3306, Database: zuni_web, SQL: select * from `classrooms` where `classrooms`.`sheet_id` = 1 and `classrooms`.`sheet_id` is not null limit 1) ao tentar criar uma criança


- [ ] `components/panel/layout.blade.php`
- [ ] `components/panel/teacher.blade.php` //maior parte dos botões nao funcionam, sem cores, não adaptado ao mobile

### Busca

- [V] `components/search/employee-list.blade.php` seria ideal conseguir alterar os status dos funcionarios
- [V] `components/search/student-list.blade.php` verificar com mais calma se todas as variáveis são exibidas
- [ ] `components/search/teacher-list.blade.php` //apresenta falhas

## Coordenador

- [X] `coordinator/chat.php`
- [X] `coordinator/forum.php`
- [ ] `coordinator/panel.blade.php` nao adaptado ao mobile
- [ ] `coordinator/profile.blade.php` não exibe o CSS
- [V] `coordinator/schedules.blade.php` não adaptado ao mobile + precisa ser funcional e não apenas visual

### Relatórios

- [ ] `coordinator/reports/index.blade.php` ? tela inexistente

### Alunos

- [ ] `coordinator/student/index.blade.php` ?
- [ ] `coordinator/student/show.blade.php` ?

### Professores

- [ ] `coordinator/teacher/index.blade.php` ?
- [V] `coordinator/teacher/register.blade.php` 
- [ ] `coordinator/teacher/show.blade.php` // não parece ser possível excluir um professor, endereço e algumas outras informações não são exibidos, crasha quando coloca telefone/matrícula e registro iguais

## Diretor

- [V] `director/index.blade.php`

PROFILE NAO FUNCIONA!!

### Funcionários

- [V] `director/employee/index.blade.php`
- [ ] `director/employee/register.blade.php` //vale a pena dar uma olhada mais a fundo mas não parece registrar novos usuários
- [V] `director/employee/show.blade.php`

## Responsável

- [X] `guardian/chat.blade.php`
- [X] `guardian/forum.blade.php`
- [V] `guardian/panel.blade.php` parece design incompleto e adaptar ao celular
- [V] `guardian/profile.blade.php`

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