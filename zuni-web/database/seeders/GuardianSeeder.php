<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Database\Seeders\Concerns\SeedsPeople;
use Illuminate\Database\Seeder;

/** 5 responsáveis: guardian01 a guardian05 (guardian0N@zuni.test) / 123456. Cada um tem 5 alunos (StudentSeeder). */
class GuardianSeeder extends Seeder
{
    use SeedsPeople;

    public function run(): void
    {
        $guardians = [
            ['Marcos Vinícius Almeida', 'M', '1982-06-14', 'Rua das Flores', 'Jardim Paulista'],
            ['Fernanda Lima Rocha', 'F', '1985-02-20', 'Avenida Brasil', 'Vila Nova'],
            ['Ricardo Gomes Barbosa', 'M', '1979-10-05', 'Rua dos Ipês', 'Bela Vista'],
            ['Juliana Castro Ribeiro', 'F', '1987-12-01', 'Rua do Comércio', 'Santa Cecília'],
            ['Paulo Roberto Nogueira', 'M', '1976-08-27', 'Alameda Santos', 'Centro'],
        ];

        foreach ($guardians as $index => [$name, $gender, $birth, $street, $district]) {
            $n = $index + 1;
            $username = sprintf('guardian%02d', $n);

            $this->upsertUser($username, [
                'name' => $name,
                'email' => "{$username}@zuni.test",
                'role' => UserRole::GUARDIAN,
                'status' => UserStatus::ACTIVE,

                'cpf' => $this->cpf(20 + $n),
                'rg' => $this->rg(20 + $n),
                'phone' => sprintf('(11) 99%03d-%04d', 200 + $n, 2000 + $n),
                'birth_date' => $birth,
                'gender' => $gender,

                'street' => $street,
                'number' => (string) (10 * $n + 5),
                'district' => $district,
                'city' => 'São Paulo',
                'state' => 'SP',
            ]);
        }
    }
}
