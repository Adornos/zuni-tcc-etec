<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Login extends Controller
{
    public function __invoke(Request $request)
    {
        // Validate input
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->login;

        // Try to find user by email OR username
        $user = User::where('email', $login)
            ->orWhere('username', $login)
            ->first();

        // If user not found or password invalid
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors([
                    'login' => 'As credenciais fornecidas não vinculam a nenhum usuário registrado.',
                ])
                ->onlyInput('login');
        }

        // Login user
        Auth::login($user, $request->boolean('remember'));

        // Security
        $request->session()->regenerate();

        return redirect()
            ->intended('/')
            ->with('success', 'Bem-vindo novamente!');
    }
}