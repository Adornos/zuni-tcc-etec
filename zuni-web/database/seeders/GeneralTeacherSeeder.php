<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GeneralTeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            'Ana Paula Oliveira',
            'Bruno Henrique Santos',
            'Carolina Mendes Silva',
            'Daniel Ferreira Costa',
            'Eduarda Martins Souza',
            'Felipe Almeida Rocha',
            'Gabriela Rodrigues Lima',
            'Henrique Barbosa Alves',
            'Isabela Fernandes Pereira',
            'João Pedro Carvalho',
            'Karen Cristina Gomes',
            'Lucas Gabriel Ribeiro',
            'Mariana Alves Martins',
            'Nicolas Moreira Santos',
            'Patrícia Oliveira Castro',
            'Rafael Henrique Dias',
            'Renata Cristina Lopes',
            'Samuel Rodrigues Souza',
            'Tatiane Martins Costa',
            'Vinícius Pereira Almeida',
        ];

        foreach ($teachers as $index => $name) {

            $parts = preg_split('/\s+/', trim($name));

            $firstName = strtolower($parts[0]);
            $lastName = strtolower(end($parts));

            // Remove acentos
            $firstName = iconv(
                'UTF-8',
                'ASCII//TRANSLIT//IGNORE',
                $firstName
            );

            $lastName = iconv(
                'UTF-8',
                'ASCII//TRANSLIT//IGNORE',
                $lastName
            );

            $username = "{$firstName}.{$lastName}";

            // Garante username único
            $baseUsername = $username;
            $counter = 2;

            while (User::where('username', $username)->exists()) {
                $username = "{$baseUsername}.{$counter}";
                $counter++;
            }

            $user = User::create([
                'name' => $name,
                'username' => $username,
                'email' => "{$username}@escola.test",
                'password' => Hash::make('123456'),

                'role' => UserRole::TEACHER,
                'status' => 'active',

                'cpf' => null,
                'rg' => null,
                'phone' => '(11) 9' . str_pad($index + 1, 8, '0', STR_PAD_LEFT),

                'birth_date' => now()
                    ->subYears(rand(25, 55))
                    ->subDays(rand(1, 365))
                    ->format('Y-m-d'),

                'gender' => $index % 2 === 0 ? 'F' : 'M',

                'street' => 'Rua Principal',
                'number' => (string) ($index + 100),
                'district' => 'Centro',
                'city' => 'Registro',
                'state' => 'SP',
            ]);

            $user->teacherSheet()->create([
                'formation' => 'Licenciatura',
                'specialization' => match ($index % 5) {
                    0 => 'Pedagogia',
                    1 => 'Matemática',
                    2 => 'Letras',
                    3 => 'História',
                    4 => 'Educação Física',
                },
                'registration' => 'PROF' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'hire_date' => now()
                    ->subYears(rand(1, 10))
                    ->format('Y-m-d'),
                'notes' => 'Professor criado através do seeder.',
            ]);
        }
    }
}
