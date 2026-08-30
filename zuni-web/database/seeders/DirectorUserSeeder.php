<?php

namespace Database\Seeders;

use App\Models\DirectorSheet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DirectorUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['username' => 'director01'],
            [
                'name' => 'Diretor',
                'email' => 'director@zuni.test',
                'password' => Hash::make('123456'),
                'role' => 'director',
                'status' => 'active',

                'cpf' => '456.789.123-00',
                'rg' => '21.345.678-0',
                'phone' => '(11) 96666-5555',

                'birth_date' => '1980-03-15',
                'gender' => 'M',

                'street' => 'Avenida Central',
                'number' => '500',
                'district' => 'Centro',
                'city' => 'São Paulo',
                'state' => 'SP',
            ]
        );

        DirectorSheet::firstOrCreate(
            ['director_id' => $user->id],
            [
                'formation' => 'Licenciatura em Pedagogia',
                'specialization' => 'Gestão e Administração Escolar',
                'registration' => 'DIR001',
                'hire_date' => '2018-02-01',
                'notes' => 'Diretor padrão do sistema.',
            ]
        );
    }
}