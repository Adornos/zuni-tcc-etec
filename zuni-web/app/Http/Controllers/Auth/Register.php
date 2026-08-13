<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;

class Register extends Controller
{
    public function __invoke(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cpf' => 'required|string|min:11',
            'password' => 'required|string|min:8|confirmed',
        ]);


        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'cpf' => $validated['cpf'],
            'role' => UserRole::GUARDIAN,
        ]);

        // Log them in
        Auth::login($user);
        // Redirect to home
                return redirect()
            ->intended('/guardian')
            ->with('success', 'Bem-vindo ao sistema Zuni!');
    }
}