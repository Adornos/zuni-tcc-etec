<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\TeacherSheet;
use Database\Seeders\Concerns\SeedsPeople;
use Illuminate\Database\Seeder;

/**
 * 5 professores: teacher01 a teacher05 (e-mail teacher@zuni.test para o primeiro, teacher0N@zuni.test
 * para os demais) / 123456. Cada um é responsável por uma turma (ver ClassroomSeeder).
 */
class TeacherSeeder extends Seeder
{
    use SeedsPeople;

    public function run(): void
    {
        $teachers = [
            ['Ana Paula Oliveira', 'F', 'Pedagogia', '1990-05-12'],
            ['Bruno Henrique Santos', 'M', 'Matemática', '1987-09-03'],
            ['Carolina Mendes Silva', 'F', 'Letras', '1992-01-25'],
            ['Daniel Ferreira Costa', 'M', 'História', '1985-11-30'],
            ['Eduarda Martins Souza', 'F', 'Educação Física', '1994-07-08'],
        ];

        foreach ($teachers as $index => [$name, $gender, $specialization, $birth]) {
            $n = $index + 1;
            $username = sprintf('teacher%02d', $n);

            $user = $this->upsertUser($username, [
                'name' => $name,
                'email' => $n === 1 ? 'teacher@zuni.test' : "{$username}@zuni.test",
                'role' => UserRole::TEACHER,
                'status' => UserStatus::ACTIVE,

                'cpf' => $this->cpf(10 + $n),
                'rg' => $this->rg(10 + $n),
                'phone' => sprintf('(11) 98%03d-%04d', 100 + $n, 1000 + $n),
                'birth_date' => $birth,
                'gender' => $gender,

                'street' => 'Rua Principal',
                'number' => (string) (300 + $n),
                'district' => 'Centro',
                'city' => 'São Paulo',
                'state' => 'SP',
            ]);

            TeacherSheet::updateOrCreate(['teacher_id' => $user->id], [
                'formation' => 'Licenciatura em Pedagogia',
                'specialization' => $specialization,
                'registration' => sprintf('PROF%03d', $n),
                'hire_date' => sprintf('%d-02-01', 2018 + $n),
                'notes' => null,
            ]);
        }
    }
}
