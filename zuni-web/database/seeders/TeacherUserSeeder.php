<?php

namespace Database\Seeders;

use App\Models\TeacherSheet;
use App\Models\User;
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
                'email' => 'teacher@zuni.test',
                'password' => Hash::make('123456'),
                'role' => 'teacher',
                'status' => 'active',

                'cpf' => '123.456.789-09',
                'rg' => '45.678.912-3',
                'phone' => '(11) 98888-7777',

                'birth_date' => '1995-03-10',
                'gender' => 'M',

                'street' => 'Rua Principal',
                'number' => '300',
                'district' => 'Centro',
                'city' => 'São Paulo',
                'state' => 'SP',
            ]
        );

        TeacherSheet::firstOrCreate(
            ['teacher_id' => $user->id],
            [
                'formation' => 'Licenciatura em Pedagogia',
                'specialization' => 'Educação Infantil',
                'registration' => 'PROF001',
                'hire_date' => '2022-02-01',
                'notes' => 'Professor padrão do sistema.',
            ]
        );
    }
}