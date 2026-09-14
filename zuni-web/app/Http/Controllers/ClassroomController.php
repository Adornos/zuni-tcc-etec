<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentSheet;
use App\Models\Classroom;

use App\Enums\UserRole;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('coordinator.classroom.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('coordinator.classroom.register');
    }

    /**
     * Criar nova sala
     */
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


    /**
     * Visualização de informações da sala
     */
    public function show(Classroom $classroom)
    {
        return view('coordinator.classroom.show', ['classroom' => $classroom]);
    }

    /**
     * Visualização dos Professores de uma sala
     */
    public function teachers(Classroom $classroom)
    {

        $teachers = User::where('role', 'teacher')->orderBy('name')->paginate(10);

        return view('coordinator.classroom.teachers', [
            'classroom' => $classroom,
            'teachers' => $teachers,
        ]);
    }


    /**
     * Assimilação de professores às salas
     */
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

    /**
     * Visualização dos Alunos de uma sala
     */
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

        return view('coordinator.classroom.students', [
            'classroom' => $classroom,
            'availableStudents' => $availableStudents,
        ]);
    }


    /**
     * Assimilação de alunos às salas
     */
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

        return redirect()
            ->route('coordinator.classroom.show', $classroom->id)
            ->with('success', 'Alunos da turma atualizados com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        //
    }
}
