<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentSheet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
     * Gera o Username / Nº Matricula do Student
     *
     * @return string
     */
    private function generateUsername(): string
    {
        $year = now()->year;
        $schoolNumber = '01';

        $lastStudent = User::where('role', 'student')
            ->where('username', 'like', "{$year}{$schoolNumber}%")
            ->orderByDesc('username')
            ->first();

        $nextSequence = $lastStudent
            ? ((int) substr($lastStudent->username, -4)) + 1
            : 1;

        return $year
            . $schoolNumber
            . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Registra o Usuario Student que pertence a um Usuario Guardian
     */
    private function registerStudent(Request $request): User
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated['password'] = 'zuni2026';

        return User::create([
            'name' => $validated['name'],
            'username' => $this->generateUsername(),
            'password' => Hash::make($validated['password']),
            'role' => 'student',
        ]);
    }
    /**
     * Faz o link entre o usuário Student e sua StudentSheet
     */
    private function linkStudentSheet(Request $request, User $studentUser){

        $user = auth()->user();

        $studentSheet_validated = $request->validate([
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


        $response = $studentUser->studentSheet()->create([
            ...$studentSheet_validated,
            'guardian_id' => $user->id,
            'name' => $studentUser->name,
        ]);

        
    }
    /**
     * Faz o link entre o usuário Student e sua Enrollment
     */
    private function linkStudentEnroll(Request $request, User $studentUser){

        $user = auth()->user();

        $studentEnroll_validated = $request->validate([
            'student_id'    => 'integer',
            'guardian_id'   => 'integer',
        ]);

        $studentUser->Enrollments()->create([
            'guardian_id' => $user->id,
            'student_id' => $studentUser->id,
        ]);
        
    }

    /**
     * Armazena novo student vinculado ao guardian logado e cria a sheet do student
     */
    public function store(Request $request)
    {

        $user = auth()->user();

        abort_unless($user->isGuardian(), 403);
        
        
        
        DB::transaction(function () use ($request) {
            
            try{

            $studentUser = $this->registerStudent($request);
                        
            $this->linkStudentSheet($request, $studentUser);
            $this->linkStudentEnroll($request, $studentUser);
            
            }catch (\Throwable $e){
                dd($e->getMessage());
            }


            });
            

        return redirect()
            ->route('guardian.registered')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Mostrar student específico (somente do guardian)
     */
    public function show(User $student)
    {
        $user = auth()->user();

        abort_unless($user->isGuardian(), 403);

        abort_unless($student->user_id === $user->id, 403);

        return view('students.show', compact('student'));
    }

    /**
     * Form edição
     */
    public function edit(User $student)
    {
        $user = auth()->user();

        abort_unless($user->isGuardian(), 403);

        abort_unless($student->user_id === $user->id, 403);

        return view('students.edit', compact('student'));
    }

    /**
     * Atualizar student
     */
    public function update(Request $request, User $student)
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
    public function destroy(User $student)
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