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
            'username' => self::generateUsername($validated['name']),
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