1. Telas gerais e autenticação
- [X] Home 
    - os botões não passam para suas respectivas paginas (ainda não existentes)
    - botão sair na home na versão mobile dando erro
- [ ] Welcome
- [X] Login 
    - botão de esqueci a senha erro 404
- [X] Cadastro
- [-] Recuperação de senha
- [-] Redefinição de senha
- [-] Verificação de e-mail
2. Coordenador
- [X] Painel do coordenador
    - Gráfico ilustrativo, não funcional
    - histórico de relatório ilustrativo, não funcional
- [X] Início
- [ ] Perfil
    - falta sistema de segurança na mudança de informações (pedir para digitar a senha novamente antes da mudança ser efetuada)
- [X] Programações
- [X] Salas de aula
- [X] Cadastro de sala de aula
    - melhorar layout mobile
- [X] Edição de sala de aula
    - melhorar layout mobile
- [X] Detalhes da sala de aula
    - melhorar layout mobile
    - tirar gap do meio da pagina
- [X] Alunos da sala
    - melhorar layout mobile
- [X] Professores da sala
- [X] Lista de alunos
    - melhorar layout mobile
    - melhorar fluxo do relatório e cadastro
- [X] Detalhes do aluno
    - não funciona na tela de lista de alunos
- [X] Lista de professores
    - botão de cadastro erro de rota
- [-] Cadastro de professor
    - botão de cadastro erro de rota
- [X] Detalhes do professor
    - falta de botão de editar informações e de voltar para a tela anterior
- [X] Relatórios
3. Diretor
- [X] Início
- [X] Perfil
- [X] Lista de funcionários
    - falta botão de voltar
- [-] Cadastro de funcionário
    - não funciona o cadastro
    - falta botão de voltar
- [X] Edição de funcionário  
- [X] Detalhes do funcionário
4. Responsável
- [X] Início
- [X] Perfil
- [-] Chat
    - falta desenvolver
    - sem menu
- [X] Mural
    - redesign 
    - não é funcional, sem post
- [X] Lista de alunos
- [X] Cadastro de aluno
- [X] Edição de aluno
    - devia apenas o coordenador poder editar o cadastro de um aluno, não o pai
- [X] Detalhes do aluno
5. Professor
- [ ] Início
- [X] Painel do professor  --falta deixar as informações funcionais (tipo proxima aula, novas mensagens...)
- [X] Perfil --a foto não carrega pelo menos no meu pc, não parece alterar o horário do "histórico" (mas isso é meio inutil sepa...) e faltam algumas coisas para serem alteradas, a nao ser que itens como formação profissional sejam propositalmente inalteráveis
- [-] Chat
- [-] Fórum --muito precário nao funcional
- [X] Programação --visualmente pronto mas não funcional creio eu
- [X] Lista de salas de aula
devido ao erro: vendor\laravel\framework\src\Illuminate\Database\Connection.php:857
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'zuni_web.classroom_proficiency_averages' doesn't exist (Connection: mysql, Host: 127.0.0.1, Port: 3306, Database: zuni_web, SQL: select * from `classroom_proficiency_averages` where `classroom_id` = 1)

não consegui atribuir professor a sala de aula e nem verificar os 3 checkmark de baixo
- [?] Detalhes da sala de aula 
- [?] Lista de alunos
- [?] Detalhes do aluno
6. Chat
- [-] Lista de conversas
- [-] Criar conversa
- [-] Conversa
- [-] Chat do coordenador
- [-] Chat do responsável
- [-] Chat do professor
7. Fórum
- [-] Fórum do coordenador
- [-] Fórum do responsável
- [-] Fórum do professor
- [-] Lista de publicações
- [-] Criar publicação
- [-] Editar publicação
- [-] Detalhes da publicação
8. Salas de aula
- [X] Lista de salas
- [X] Cadastro de sala
- [X] Edição de sala
- [X] Detalhes da sala
- [X] Alunos da sala
- [X] Professores da sala
9. Funcionários e professores
- [X] Lista de funcionários
- [-] Cadastro de funcionário --alterei todos os campos e não foi
- [ ] Edição de funcionário (não achei)
- [X] Detalhes do funcionário
- [X] Lista de professores
- [-] Cadastro de professor
- [ ] Edição de professor (nao achei)
- [X] Detalhes do professor
10. Responsáveis
- [ ] Lista de responsáveis
- [ ] Cadastro de responsável
- [ ] Edição de responsável
- [ ] Detalhes do responsável
11. Alunos
- [ ] Lista de alunos
- [ ] Cadastro de aluno
- [ ] Edição de aluno
- [ ] Detalhes do aluno
12. Solicitações de matrícula
- [ ] Lista de solicitações
- [ ] Edição da solicitação
- [ ] Detalhes da solicitação
13. Relatórios
- [ ] Lista de relatórios
- [ ] Criar relatório
- [ ] Editar relatório
- [ ] Detalhes do relatório
14. Programação e horários
- [ ] Lista de programações
- [ ] Criar programação
- [ ] Detalhes da programação
- [ ] Programação do aluno
- [ ] Programação do professor