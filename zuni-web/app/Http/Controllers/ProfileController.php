<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Perfil do usuário autenticado, igual para todos os roles (responsável, professor,
 * coordenador e diretor). O usuário só enxerga e edita o PRÓPRIO perfil: não há id na
 * rota, e campos sensíveis (role, status, matrícula) nunca são aceitos pelo formulário.
 * Dados específicos do role (ficha profissional, alunos vinculados) aparecem na tela
 * conforme o tipo de usuário.
 */
class ProfileController extends Controller
{
    public function show()
    {
        /** @var User $user */
        $user = Auth::user();

        $user->load(['students.user', 'students.classroom']);

        // Ficha profissional (professor, coordenador ou diretor); null para responsável.
        $sheet = $user->roleSheet()?->first();

        return view('pages.profile.show', [
            'profile' => $user,
            'sheet' => $sheet,
            'students' => $user->isGuardian() ? $user->students : collect(),
        ]);
    }

    public function edit()
    {
        return view('pages.profile.edit', ['profile' => Auth::user()]);
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            // Dados pessoais
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'min:5', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'cpf' => ['nullable', 'string', 'min:11', 'max:14', Rule::unique('users', 'cpf')->ignore($user->id)],
            'rg' => ['nullable', 'string', 'min:9', 'max:20', Rule::unique('users', 'rg')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'min:10', 'max:20'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'gender' => ['nullable', 'in:M,F,O'],

            // Senha (opcional): o Model aplica o hash automaticamente.
            'password' => [
                'nullable',
                'string',
                Rule::when(config('auth.password_strict_validation'), ['min:8']),
                'confirmed',
            ],

            // Endereço
            'street' => ['nullable', 'string', 'max:100'],
            'number' => ['nullable', 'string', 'max:10'],
            'district' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:50'],
            'state' => ['nullable', 'string', 'max:50'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        unset($validated['password_confirmation']);

        $user->update($validated);

        return redirect()
            ->route($user->role->value.'.profile')
            ->with('success', 'Perfil atualizado com sucesso!');
    }
}
