<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentSheet;
use App\Models\Classroom;
use App\Services\Proficiency\ClassroomAverageService;
use App\Services\Proficiency\ProficiencyService;

use App\Enums\ClassroomGrade;
use App\Enums\ClassroomShift;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassroomController extends Controller
{
    public function index()
    {
        return view('pages.classroom.index');
    }

    public function create()
    {
        return view('pages.classroom.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'grade' => ['required', 'string', 'max:50'],
            'shift' => [
                'nullable',
                'in:morning,afternoon,full_time,evening'
            ],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ]);

        $classroom = Classroom::create($validated);

        //Route::current()->getPrefix()

        return redirect()
            ->route('coordinator.classroom.show', $classroom->id)
            ->with('success', 'Turma criada com sucesso.');
    }

    public function show(Classroom $classroom)
    {
        return view('pages.classroom.show', ['classroom' => $classroom]);
    }

    public function teachers(Classroom $classroom)
    {

        $teachers = User::where('role', 'teacher')->orderBy('name')->paginate(10);

        return view('pages.classroom.teachers', [
            'classroom' => $classroom,
            'teachers' => $teachers,
        ]);
    }

    public function assignTeachers(Request $request, Classroom $classroom) 
    {
        $validated = $request->validate([
            'teachers' => ['array'],
            'teachers.*' => [
                'exists:users,id'
            ],
        ]);

        $classroom->teachers()->sync($validated['teachers'] ?? []);

        return redirect()
            ->route('coordinator.classroom.show', $classroom->id)
            ->with('success', 'Professores atribuídos com sucesso.');
    }

    public function students(Classroom $classroom)
    {
        $classroom->load([
            'students.user',
            'teachers',
        ]);

        $availableStudents = StudentSheet::with('user')
            ->whereNull('classroom_id')
            ->get()
            ->sortBy('user.name');

        return view('pages.classroom.students', [
            'classroom' => $classroom,
            'availableStudents' => $availableStudents,
        ]);
    }

    public function assignStudents(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'students' => ['nullable', 'array'],

            'students.*' => [
                'integer',
                Rule::exists('users', 'id')
                    ->where('role', UserRole::STUDENT->value),
            ],
        ]);

        $studentIds = $validated['students'] ?? [];

        // Salas afetadas (a atual e as de origem dos alunos movidos) terão a média recalculada.
        $affectedClassrooms = StudentSheet::query()
            ->where('classroom_id', $classroom->id)
            ->orWhereIn('student_id', $studentIds)
            ->pluck('classroom_id')
            ->push($classroom->id);

        DB::transaction(function () use ($studentIds, $classroom) {

            /*
            * Alunos que já pertencem à turma atual,
            * mas foram desmarcados, deixam a turma.
            */
            StudentSheet::where('classroom_id', $classroom->id)
                ->when(
                    !empty($studentIds),
                    fn ($query) => $query->whereNotIn('student_id', $studentIds)
                )
                ->update([
                    'classroom_id' => null,
                ]);

            /*
            * Os alunos selecionados passam a pertencer
            * à turma atual.
            *
            * Se algum deles estava em outra turma,
            * ele será deslocado para esta.
            */
            StudentSheet::whereIn('student_id', $studentIds)
                ->update([
                    'classroom_id' => $classroom->id,
                ]);
        });

        // Alunos ativos que entraram na sala garantem suas proficiências; depois, médias.
        $proficiencies = app(ProficiencyService::class);
        StudentSheet::query()
            ->whereIn('student_id', $studentIds)
            ->whereHas('user', fn ($q) => $q->where('status', \App\Enums\UserStatus::ACTIVE->value))
            ->get()
            ->each(fn ($sheet) => $proficiencies->ensureInitialized($sheet));

        app(ClassroomAverageService::class)->refreshMany($affectedClassrooms);

        return redirect()
            ->route('coordinator.classroom.show', $classroom->id)
            ->with('success', 'Alunos da turma atualizados com sucesso.');
    }

    /**
     * Formulário de edição da turma (separado da página de visualização). Só a coordenação edita.
     */
    public function edit(Classroom $classroom)
    {
        abort_unless(Auth::user()->isCoordinator(), 403);

        return view('pages.classroom.edit', [
            'classroom' => $classroom,
            'enrolled' => $classroom->students()->count(),
        ]);
    }

    public function update(Request $request, Classroom $classroom)
    {
        abort_unless(Auth::user()->isCoordinator(), 403);

        $enrolled = $classroom->students()->count();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'grade' => ['required', Rule::enum(ClassroomGrade::class)],
            'shift' => ['nullable', Rule::enum(ClassroomShift::class)],
            // A capacidade nunca pode ficar abaixo do número de alunos já matriculados.
            'capacity' => ['nullable', 'integer', 'min:'.max(1, $enrolled)],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'capacity.min' => "A capacidade não pode ser menor que o número de alunos matriculados ({$enrolled}).",
        ], [
            'name' => 'nome da turma',
            'grade' => 'série',
            'shift' => 'turno',
            'capacity' => 'capacidade',
            'status' => 'status',
        ]);

        $classroom->update($validated);

        return redirect()
            ->route('coordinator.classroom.show', $classroom)
            ->with('success', 'Turma atualizada com sucesso.');
    }

    public function destroy(Classroom $classroom)
    {
        //
    }
}
