# Criação e Atribuição de Reports

- Entidades que o possuem
    - StudentSheet
    - Classroom


### Estrutura do Report
Report deve ter a mesma estrutura de um email e um tipo específicado (enum), isso é:
- Título
- Corpo
- Autor (id do usuário)
- Referencia (Aluno(s) ou Sala(s))
- Tipo de relatório:
    1. Aviso
    2. Observação
    3. ...
- Estado (ativo ou não)
- Data de criação
- Data de atualização


### Especificações

- No projeto será criado um controller específico para o report.

- O report poderá ser feito apenas pelo professor e coordenador.
- O report será atribuido a um ou mais alunos ou salas:

- Sala possui muitos Report e Report possui uma ou muitas salas.
- Aluno possui muitos Report e Report possui um ou mais alunos.
> Atribuição N:N e, a atribuição não pode ser feite entre entidades. Ou é um report de alunos, ou é um report de sala, mas não ambos.

- Se um report for desaprovado, isto é, algo foi reportado mas foi um engano; ele passará a ser inativo. Essa ação poderá ser feita somente por um coordenador


### Criação de Report
Cada pagina de cada entidade deverá conter um link que levará criação de report, bem como uma página própria, Respectivamente:

- Página de relatórios: Deverá conter apenas relatórios gerais(para escola), e links para os demais tipos de relatórios que pertencem à páginas específicas(sala tem seus reports e aluno tem seus reports).
- Página de salas: Deverá conter um checkbox para cada aluno da sala, depois de selecionar Criar relatório na pagina que lista os aluinos da sala.
- Página do aluno: Deverá conter um botão que atribui um relatório diretamente ao aluno.
- Página de listagem geral de alunos: Esquema de checkboxes
> Não pode haver report sem entidade selecionada e não existe um limite para seleção de entidades a serem atribuidas ao report.
> Se de uim report for retirado entidades na edição e por causa disso esse report passará a não ter mais entidades atribuidas o salvamento não deve ser permitido, e se o intuito for retirar o report, esse deverá ser inativado ou pelo criador ou pelo coordenador.

Para exemplificar:
Na pagina de um aluno haverá o botão [Criar Relatório], este botão levara a pagina de criação de relatório para este aluno especificado e nos parâmetros de create() deverá ser passado o aluno ou sala especificado.


### Visualização de Report

A entidade em que o report foi atribuída deverá ter uma página apenas para visualização desses(sala terá sessão de reports, aluno terá sesão de reports também. Essa poderá ser visualizada pelos usuários: professor, coordenador, etc..), bem como uma página de reports geral em seus criadores.
- Alunos: Página de relatórios atribuidos
> Alunos é uma entidade geral que pode ser visualizada por todos do sistema: Professor, Diretor, Coordenador e Responsável. Apenas muda o que cada um pode fazer nessas páginas. Responsável: visualiza; Professor: visualiza, gera reports; Coordenador: Visualiza, cria reports; Diretor: vê.
- Salas: Página de relatórios atribuidos
- Professor: Página de visualização de Reports Criados 
- Coordenador: Página de visualização de Reports Criados

Essa visualização porderá ser feita por qualquer tipo de usuário do sistema: Professor, Coordenador, Responsável, Diretor.

Reports devem ser listados do mais recente para o mais antigo e possuir paginação, Quando sem filtros.

### Edição e Exclusão

Apenas deverão poder excluir ou editar os criadores do report e um coordenador pode desabilitar e reativar:
> O Professor somente pode editar/excluir Reports que pertencem aos alunos/salas sob sua responsabilidade
> O coordenador sempre terá essa permissão pois sempre existirá um. Porém um professor por deixar de ser um (desligamento) e portanto se não houver a opção do coordenador alterar, o report estaria estagnado.
> O Coordenador não pode editar ou excluir Reports criados por outros usuários, mas pode desabilitá-los e reativa-los.
> A visualização de reports inativos será dada apenas ao criador do report e ao coordenador.

- Coordenador na página geral de relatórios
- Professor na página geral de relatórios
- No Aluno quando acessado pelo professor ou pelo Coordenador

> Na exclusão, o report será retirado por completo do sistema, desde seu texto até suas atribuições.

## Localizações:

Cada Role no sistema tem seu próprio acesso.

### Aluno

Todos os Usuários:

- Route::get('/student', [StudentController::class, 'index'])->name('student.index');
- Route::get('/students/{student}', [StudentController::class, 'show'])->name('student.show');

Apenas Responsável:

- Route::get('/student/register', [StudentController::class, 'create'])->name('student.register');
- Route::post('/student/register', [StudentController::class, 'store'])->name('student.store');
- Route::get('/student/{student}/edit', [StudentController::class, 'edit'])->name('student.edit');
- Route::put('/student/{student}', [StudentController::class, 'update'])->name('student.update');

Apenas Coordenador:

- Route::put('/students/{enrollment}/approve', [CoordinatorController::class, 'approveEnrollment'])->name('student.approve');
- Route::put('/students/{enrollment}/reject', [CoordinatorController::class, 'rejectEnrollment'])->name('student.reject');

### Sala

Todos os usuários:

- Route::get('/classroom/show/{classroom}', [ClassroomController::class, 'show'])->name('classroom.show');

Coordenadores e Professores:

- Route::get('/classrooms', [ClassroomController::class, 'index'])->name('classroom.index');
- Route::put('/classroom/show/{classroom}', [ClassroomController::class, 'update'])->name('classroom.update');
- Route::get('/classroom/show/{classroom}/students', [ClassroomController::class, 'students'])->name('classroom.students');

Apenas Coordenadores:

- Route::get('/classroom/register', [ClassroomController::class, 'create'])->name('classroom.create'); 
- Route::post('/classroom/register', [ClassroomController::class, 'store'])->name('classroom.store');
- Route::get('/classroom/show/{classroom}/teachers', [ClassroomController::class, 'teachers'])->name('classroom.teachers');
- Route::put('/classroom/show/{classroom}/teachers', [ClassroomController::class, 'assignTeachers'])->name('classroom.teachers.update');
- Route::put('/classroom/show/{classroom}/students', [ClassroomController::class, 'assignStudents'])->name('classroom.students.update');


## Controller do Report

- Criação:
    - Essa será feita pelo controller do report em create() — para mostrar a página — e store() para armazenar.
- Listagem:
    - Geral: Lista com todos os relatórios
    - Específico: Lista de reports na sala e no aluno que poderão ser visualizados por todos os usuários.
    > Importante: Talvez essa listagem seja feito fora do controller num Livewire na pasta de searches (Onde se localiza os buscadores do sistema):  zuni-web/resources/views/components/search/
- Visualização:
    - Essa será feita pelo controller do report em show()
- Edição:
    - Essa será feita pelo controller do report em edit() — para mostrar a página — e store() para armazenar.
- Exclução:
    - Essa será feita pelo controller do report em delete().

As rotas terão de ser feitas com base no que já existe:
- Route::get('/reports', [ReportController::class, 'index'])->name('report.index');
- Route::get('/reports/create', [ReportController::class, 'create'])->name('report.create');
- Route::post('/reports', [ReportController::class, 'store'])->name('report.store');
- Route::get('/reports/{report}', [ReportController::class, 'show'])->name('report.show');
- Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])->name('report.edit');
- Route::put('/reports/{report}', [ReportController::class, 'update'])->name('report.update');
- Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('report.destroy');

> A rota index levará ao livewire, que apenas mostrará os reports do criador e, caso for um coordenador, todos os reports.
> Create leva ao formulário e store armazena no banco.
> Edit leva ao formulário e update atualiza no banco.

# Observações Gerais

- As rotas Students são apenas nomes, StudentSheet é a entidade na qual os relatórios serão atribuidos.
