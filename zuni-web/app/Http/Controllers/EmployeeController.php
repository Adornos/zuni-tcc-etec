<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    private static function generateUsername(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));

        $ignored = ['da', 'de', 'do', 'das', 'dos', 'e'];

        $parts = array_values(array_filter($parts, function ($part) use ($ignored) {
            return !in_array(strtolower($part), $ignored);
        }));

        $firstName = strtolower($parts[0]);
        $lastName = strtolower(end($parts));

        // Remove acentos
        $firstName = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $firstName);
        $lastName = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $lastName);

        $baseUsername = "{$firstName}.{$lastName}";
        $username = $baseUsername;

        $counter = 2;

        while (User::where('username', $username)->exists()) {
            $username = "{$baseUsername}.{$counter}";
            $counter++;
        }

        return $username;
    }

    private function linkSheet(User $user, array $data): void
    {
        match ($user->role) {

            UserRole::TEACHER =>
                $user->teacherSheet()->create([
                    'formation' => $data['formation'] ?? null,
                    'specialization' => $data['specialization'] ?? null,
                    'registration' => $data['registration'] ?? null,
                    'hire_date' => $data['hire_date'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]),

            UserRole::COORDINATOR =>
                $user->coordinatorSheet()->create([
                    'formation' => $data['formation'] ?? null,
                    'specialization' => $data['specialization'] ?? null,
                    'registration' => $data['registration'] ?? null,
                    'hire_date' => $data['hire_date'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]),

            UserRole::DIRECTOR =>
                $user->directorSheet()->create([
                    'formation' => $data['formation'] ?? null,
                    'specialization' => $data['specialization'] ?? null,
                    'registration' => $data['registration'] ?? null,
                    'hire_date' => $data['hire_date'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]),

            default => abort(422, 'Cargo inválido.'),
        };
    }

    public function store(Request $request)
    {
    
        $validated = $request->validate([

            // User
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required','email','max:255','unique:users,email',],
            'role' => ['required','in:teacher,coordinator,director',],
            'cpf' => ['nullable','string','max:14','unique:users,cpf',],
            'rg' => ['nullable','string','max:20','unique:users,rg',],
            'phone' => ['nullable','string','max:20',],
            'birth_date' => ['nullable','date',],
            'gender' => ['nullable','in:M,F,O',],

            // Formação
            'formation' => ['nullable','string','max:150',],
            'specialization' => ['nullable','string','max:150',],
            'registration' => ['nullable','string','max:50',],
            'hire_date' => ['nullable','date',],

            // Endereço
            'street' => ['nullable','string','max:100',],
            'number' => ['nullable','string','max:10',],
            'district' => ['nullable','string','max:50',],
            'city' => ['nullable','string','max:50',],
            'state' => ['nullable','string','max:50',],

            // Informações adicionais
            'notes' => ['nullable','string',],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => self::generateUsername($validated['name']),
            'email' => $validated['email'],
            'password' => Hash::make('123456'),
            'role' => UserRole::from($validated['role']),
            'status' => 'active',

            'cpf' => $validated['cpf'] ?? null,
            'rg' => $validated['rg'] ?? null,
            'phone' => $validated['phone'] ?? null,

            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,

            'street' => $validated['street'] ?? null,
            'number' => $validated['number'] ?? null,
            'district' => $validated['district'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
        ]);

        $this->linkSheet($user, $validated);

        return match (auth()->user()->role) {

            UserRole::DIRECTOR => redirect()
                ->route('director.employee.index')->with('success', 'Funcionário criado com sucesso.'),

            UserRole::COORDINATOR => redirect()
                ->route('coordinator.teacher.index')->with('success', 'Professor criado com sucesso.'),
        };
        }
    

    public function update(Request $request, User $employee)
    {

        $validated = $request->validate([

            // User
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable','email','max:255','unique:users,email,' . $employee->id,],
            'role' => ['nullable','in:teacher,coordinator,director',],
            'cpf' => ['nullable','string','max:14','unique:users,cpf,' . $employee->id,],
            'rg' => ['nullable','string','max:20','unique:users,rg,' . $employee->id,],
            'phone' => ['nullable','string','max:20',],
            'birth_date' => ['nullable','date',],
            'gender' => ['nullable','in:M,F,O',],
            'password' => ['nullable','string','min:8','confirmed',],

            // Formação
            'formation' => ['nullable','string','max:150',],
            'specialization' => ['nullable','string','max:150',],
            'registration' => ['nullable','string','max:50',],
            'hire_date' => ['nullable','date',],

            // Endereço
            'street' => ['nullable','string','max:100',],
            'number' => ['nullable','string','max:10',],
            'district' => ['nullable','string','max:50',],
            'city' => ['nullable','string','max:50',],
            'state' => ['nullable','string','max:50',],

            // Informações adicionais
            'notes' => ['nullable','string',],
        ]);


        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $employee->update([
            'name' => $validated['name'] ?? NULL,
            'email' => $validated['email'] ?? NULL,
            'role' => UserRole::from($validated['role']) ?? NULL,
            'password' => Hash::make($validated['password'] ?? NULL),

            'cpf' => $validated['cpf'] ?? null,
            'rg' => $validated['rg'] ?? null,
            'phone' => $validated['phone'] ?? null,

            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,

            'street' => $validated['street'] ?? null,
            'number' => $validated['number'] ?? null,
            'district' => $validated['district'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
        ]);

        $this->linkSheet($employee, $validated);

        return match (auth()->user()->role) {

            UserRole::DIRECTOR => redirect()
                ->route('director.employee.index')->with('success', 'Funcionário atualizado com sucesso.'),

            UserRole::COORDINATOR => redirect()
                ->route('coordinator.teacher.index')->with('success', 'Professor atualizado com sucesso.'),

            UserRole::TEACHER => redirect()
                ->route('teacher.profile')->with('success', 'Perfil atualizado com sucesso.'),
        };
    }
}
