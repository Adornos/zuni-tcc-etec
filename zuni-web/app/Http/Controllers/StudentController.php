<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class StudentController extends Controller
{
    /**
     * Lista apenas os alunos do guardian logado
     */
    public function index()
    {
        $user = auth()->user();

        abort_unless($user->isGuardian(), 403);

        $students = $user->students()->latest()->get();

        return view('students.index', compact('students'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        $user = auth()->user();

        abort_unless($user->isGuardian(), 403);

        return view('guardian.student.register');
    }

    /**
     * Armazena novo student vinculado ao guardian logado
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        abort_unless($user->isGuardian(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:M,F,O',
            'class' => 'nullable|string|max:50',
            'age' => 'nullable|integer',

            'street' => 'nullable|string|max:100',
            'number' => 'nullable|string|max:10',
            'district' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:50',

            'neurodivergent' => 'nullable|boolean',
            'allergy' => 'nullable|boolean',
            'food_restriction' => 'nullable|boolean',
            'special_care' => 'nullable|boolean',

            'notes' => 'nullable|string',
        ]);

        $user->students()->create($validated);

        return redirect()
            ->route('guardian.registered')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Mostrar student específico (somente do guardian)
     */
    public function show(Student $student)
    {
        $user = auth()->user();

        abort_unless($user->isGuardian(), 403);

        abort_unless($student->user_id === $user->id, 403);

        return view('students.show', compact('student'));
    }

    /**
     * Form edição
     */
    public function edit(Student $student)
    {
        $user = auth()->user();

        abort_unless($user->isGuardian(), 403);

        abort_unless($student->user_id === $user->id, 403);

        return view('students.edit', compact('student'));
    }

    /**
     * Atualizar student
     */
    public function update(Request $request, Student $student)
    {
        $user = auth()->user();

        abort_unless($user->isGuardian(), 403);

        abort_unless($student->user_id === $user->id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:M,F,O',
            'class' => 'nullable|string|max:50',
            'age' => 'nullable|integer',

            'street' => 'nullable|string|max:100',
            'number' => 'nullable|string|max:10',
            'district' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:50',

            'neurodivergent' => 'nullable|boolean',
            'allergy' => 'nullable|boolean',
            'food_restriction' => 'nullable|boolean',
            'special_care' => 'nullable|boolean',

            'notes' => 'nullable|string',
        ]);

        $student->update($validated);

        return redirect()
            ->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Deletar student
     */
    public function destroy(Student $student)
    {
        $user = auth()->user();

        abort_unless($user->isGuardian(), 403);

        abort_unless($student->user_id === $user->id, 403);

        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }
}