<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TeacherSheet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['username' => 'teacher01'],
            [
                'name' => 'Professor',
                'password' => Hash::make('123456'),
                'role' => 'teacher',
            ]
        );

        TeacherSheet::firstOrCreate(
            ['teacher_id' => $user->id],
            [
                'status' => 'active',

                'name' => $user->name,
                'birth_date' => '1990-06-20',
                'gender' => 'M',

                'cpf' => '123.456.789-00',
                'rg' => '12.345.678-9',

                'phone' => '(11) 99999-9999',
                'email' => 'teacher01@email.com',

                'formation' => 'Licenciatura em Pedagogia',
                'specialization' => 'Educação Infantil',

                'registration' => 'PROF001',
                'hire_date' => '2020-02-03',

                'street' => 'Rua das Flores',
                'number' => '100',
                'district' => 'Centro',
                'city' => 'São Paulo',
                'state' => 'SP',

                'notes' => null,
            ]
        );
    }
}