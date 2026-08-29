<?php

namespace App\Http\Controllers;


use App\Models\TeacherSheet;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('teacher.dashboard', [

            'dashboardInfo' => view('teacher.panel')
            
        ]);
    }
    public function profile()
    {
        $user = Auth::user();
        return view('teacher.dashboard', [
            'dashboardInfo' => view('teacher.profile', ['profile' => $user])
        ]);
    }

    public function schedule()
    {
        $user = Auth::user();
        return view('teacher.dashboard', [
            'dashboardInfo' => view('teacher.schedule', ['profile' => $user])
        ]);
    }

    public function forum()
    {
        return view('teacher.dashboard', [
            'dashboardInfo' => view('teacher.forum')
        ]);
    }
    public function chat()
    {
        return view('teacher.dashboard', [
            'dashboardInfo' => view('teacher.chat')
        ]);
    }

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

    private function registerTeacher(array $validated)
    {

        $validated['password'] = '123456';

        $user = User::create([
            'name' => $validated['name'],
            'username' => self::generateUsername($validated['name']),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::TEACHER,
        ]);

        return $user;
    }

    private function linkTeacherSheet(User $teacher, array $data): TeacherSheet
    {
        return $teacher->teacherSheet()->create([
            'status' => 'pending',

            'name' => $teacher->name,

            'birth_date' => $data['birth_date'] ?? null,
            'gender' => $data['gender'] ?? null,

            'cpf' => $data['cpf'] ?? null,
            'rg' => $data['rg'] ?? null,

            'phone' => $data['phone'] ?? null,

            'formation' => $data['formation'] ?? null,
            'specialization' => $data['specialization'] ?? null,
            'registration' => $data['registration'] ?? null,
            'hire_date' => $data['hire_date'] ?? null,

            'street' => $data['street'] ?? null,
            'number' => $data['number'] ?? null,
            'district' => $data['district'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,

            'notes' => $data['notes'] ?? null,
        ]);
    }


    public function store(Request $request)
    {

        $user = auth()->user();

        abort_unless($user->isCoordinator(), 403);
        
        $validated = $request->validate([
            // Dados pessoais
            'name' => ['required','string','max:100'],
            'birth_date' => ['nullable','date'],
            'gender' => ['nullable','in:M,F,O'],
            
            // Documentos
            'cpf' => ['nullable','string','max:14','unique:teacher_sheets,cpf'],
            'rg' => ['nullable','string','max:20'],
            
            // Contato
            'email' => ['required','email','max:150','unique:users,email'],
            'phone' => ['nullable','string','max:20'],

            // Formação profissional
            'formation' => ['nullable','string','max:150'],
            'specialization' => ['nullable','string','max:150'],
            'registration' => ['nullable','string','max:50','unique:teacher_sheets,registration'],
            'hire_date' => ['nullable','date'],

            // Endereço
            'street' => ['nullable','string','max:100'],
            'number' => ['nullable','string','max:10'],
            'district' => ['nullable','string','max:50'],
            'city' => ['nullable','string','max:50'],
            'state' => ['nullable','string','max:50'],

            // Informações adicionais
            'notes' => ['nullable','string'],
        ]);


        DB::transaction(function () use ($validated) {
            
            try{

            $teacherUser = $this->registerTeacher($validated);
                        
            $this->linkTeacherSheet($teacherUser, $validated);
            
            }catch (\Throwable $e){
                dd($e->getMessage());
            }


            });
            

        return redirect()
            ->route('coordinator.teacher.index')
            ->with('success', 'Teacher created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TeacherSheet $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeacherSheet $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeacherSheet $teacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeacherSheet $teacher)
    {
        //
    }
}
