<?php

namespace Database\Seeders;

use App\Models\CoordinatorSheet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoordinatorUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['username' => 'coordinator01'],
            [
                'name' => 'Coordenadora',
                'email' => 'coordinator@zuni.test',
                'password' => Hash::make('123456'),
                'role' => 'coordinator',
                'status' => 'active',

                'cpf' => '987.654.321-00',
                'rg' => '32.145.678-9',
                'phone' => '(11) 97777-6666',

                'birth_date' => '1988-04-17',
                'gender' => 'F',

                'street' => 'Rua das Acácias',
                'number' => '245',
                'district' => 'Vila Mariana',
                'city' => 'São Paulo',
                'state' => 'SP',
            ]
        );

        CoordinatorSheet::firstOrCreate(
            ['coordinator_id' => $user->id],
            [
                'formation' => 'Licenciatura em Pedagogia',
                'specialization' => 'Gestão Escolar',
                'registration' => 'COORD001',
                'hire_date' => '2021-02-01',
                'notes' => 'Coordenadora responsável pelo acompanhamento pedagógico.',
            ]
        );
    }
}