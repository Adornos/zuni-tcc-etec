<?php

namespace App\Http\Controllers;

use App\Models\TeacherSheet;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class GuardianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('guardian.panel');
    }
    public function show(User $guardian)
    {   
        
        if(empty($guardian->name)){
            $guardian = Auth::user();
            abort_unless($guardian->role === UserRole::GUARDIAN, 403, 'Usuário não permitido');
        } else {

        }

        return view('pages.guardian.show', ['guardianInfo' => $guardian]);    

    }
    
    public function edit()
    {
        $user = Auth::user();
        return view('pages.guardian.edit', ['profile' => $user]);
    }

    public function update(Request $request)
    {

        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->role === UserRole::GUARDIAN, 403, 'Acesso negado.');

        try{
            $validated = $request->validate([
                // User
                'name' => ['nullable', 'string', 'min:3', 'max:255'],
                'email' => ['nullable', 'email', 'min:5', 'max:255', 'unique:users,email,' . $user->id],
                'cpf' => ['nullable', 'string', 'min:11', 'max:14', 'unique:users,cpf,' . $user->id],
                'rg' => ['nullable', 'string', 'min:9', 'max:20', 'unique:users,rg,' . $user->id],
                'phone' => ['nullable', 'string', 'min:10', 'max:20'],
                'birth_date' => ['nullable', 'date'],
                'gender' => ['nullable', 'in:M,F,O'],
                'password' => [
                                'nullable', 
                                'string', 
                                Rule::when(
                                    config('auth.password_strict_validation'), 
                                    ['min:8']
                                    ), 
                                'confirmed'
                            ],

                // Endereço
                'street' => ['nullable', 'string', 'max:100'],
                'number' => ['nullable', 'string', 'max:10'],
                'district' => ['nullable', 'string', 'max:50'],
                'city' => ['nullable', 'string', 'max:50'],
                'state' => ['nullable', 'string', 'max:50'],
            ]);
        }
        catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro ao atualizar o usuário: ' . $e->getMessage());
        }

        if(!empty($validated['password'])){
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
            
        $user->update($validated);

        return redirect()->route('guardian.profile')->with('status', 'Perfil atualizado com sucesso!');

    }

    public function forum()
    {
        return view('pages.forum.index');
    }

    public function chat()
    {
        return view('pages.chat.index');
    }
}
