<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;

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

        Classroom::create($validated);

        //Route::current()->getPrefix()

        return redirect()
            ->back()
            ->with('success', 'Turma criada com sucesso.');
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
            ->back()
            ->with('success', 'Professores atribuídos com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        return view('coordinator.classroom.show', ['classroom' => $classroom]);
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
