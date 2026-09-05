<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentSheet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:M,F,O',

            'street' => 'nullable|string|max:100',
            'number' => 'nullable|string|max:10',
            'district' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:50',
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
            'class' => 'nullable|string|max:50',

            'neurodivergent' => 'nullable|boolean',
            'allergy' => 'nullable|boolean',
            'food_restriction' => 'nullable|boolean',
            'special_care' => 'nullable|boolean',

            'notes' => 'nullable|string',
        ]);


        $response = $studentUser->studentSheet()->create([
            ...$studentSheet_validated,
            'guardian_id' => $user->id,
        ]);

        // dd($response);
        
    }
    /**
     * Faz o link entre o a StudentSheet e sua Enrollment
     */
    private function linkStudentEnroll(StudentSheet $studentSheet){

        $studentSheet->Enrollment()->create([
            'sheet_id' => $studentSheet->id,
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
            $this->linkStudentEnroll($studentUser->studentSheet);
            
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
        abort_unless($student->role->value === 'student', 404);

        $student->load([
            'studentSheet.guardian',
            'studentSheet.classroom',
        ]);

        return view('guardian.student.show', compact('student'));
    }

    /**
     * Form edição
     */
    public function edit(User $student)
    {
        abort_unless($student->role->value === 'student', 404);

        $student->load([
            'studentSheet.guardian',
            'studentSheet.classroom',
        ]);

        return view('guardian.student.edit', compact('student'));
    }

    /**
     * Atualizar student
     */
    public function update(Request $request, User $student)
    {
        abort_unless(
            $student->role->value === 'student',
            404
        );

        $validated = $request->validate([

            // User
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['M', 'F', 'O'])],
            'cpf' => ['nullable', 'string', 'max:14', Rule::unique('users', 'cpf')->ignore($student->id)],
            'rg' => ['nullable', 'string', 'max:20', Rule::unique('users', 'rg')->ignore($student->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($student->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student->id),],

            'street' => ['nullable', 'string', 'max:100'],
            'number' => ['nullable', 'string', 'max:10'],
            'district' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:50'],
            'state' => ['nullable', 'string', 'max:50'],

            // StudentSheet
            'neurodivergent' => ['nullable', 'boolean'],
            'allergy' => ['nullable', 'boolean'],
            'food_restriction' => ['nullable', 'boolean'],
            'special_care' => ['nullable', 'boolean'],

            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($student, $validated) {

            /*
            * Dados da conta/pessoa
            */
            $student->update([
                'name' => $validated['name'],
                'birth_date' => $validated['birth_date'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'cpf' => $validated['cpf'] ?? null,
                'rg' => $validated['rg'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,

                'street' => $validated['street'] ?? null,
                'number' => $validated['number'] ?? null,
                'district' => $validated['district'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
            ]);

            /*
            * Dados específicos do aluno
            */
            $student->studentSheet()->update([
                'neurodivergent' => $validated['neurodivergent'] ?? false,
                'allergy' => $validated['allergy'] ?? false,
                'food_restriction' => $validated['food_restriction'] ?? false,
                'special_care' => $validated['special_care'] ?? false,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('guardian.student.show', $student)
            ->with('success', 'Dados do aluno atualizados com sucesso.');
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