<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DirectorSheet;
use Illuminate\Support\Facades\Hash;

class DirectorUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['username' => 'director01'],
            [
                'name' => 'Diretor',
                'password' => Hash::make('123456'),
                'role' => 'director',
            ]
        );

        DirectorSheet::firstOrCreate(
            ['director_id' => $user->id],
            [
                'name' => $user->name,
                'birth_date' => '1980-03-15',
                'gender' => 'Masculino',
                'formation' => 'Pedagogia',
                'street' => 'Rua das Flores',
                'number' => '100',
                'district' => 'Centro',
                'city' => 'São Paulo',
                'state' => 'SP',
                'registration' => 'DIR001',
                'hire_date' => '2010-02-01',
                'status' => 'active',
            ]
        );
    }
}