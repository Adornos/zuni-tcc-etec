<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\StudentSheet;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\Concerns\SeedsPeople;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 25 alunos: 5 para cada responsável. O filho nº j de cada responsável estuda no j-ésimo ano
 * (1º ao 5º), então cada turma fica com 5 alunos de 5 responsáveis diferentes e cada responsável
 * tem um filho em cada turma (idades de 6 a 10 anos). Todos têm matrícula aprovada (aluno ativo).
 * Login: aluno001 a aluno025 / 123456.
 */
class StudentSeeder extends Seeder
{
    use SeedsPeople;

    /** [primeiro nome, sexo] por responsável (índice 0..4) e filho (índice 0..4). */
    private const CHILDREN = [
        [['Lucas', 'M'], ['Beatriz', 'F'], ['Pedro', 'M'], ['Sofia', 'F'], ['Miguel', 'M']],
        [['Helena', 'F'], ['Arthur', 'M'], ['Laura', 'F'], ['Gabriel', 'M'], ['Alice', 'F']],
        [['Davi', 'M'], ['Manuela', 'F'], ['Heitor', 'M'], ['Valentina', 'F'], ['Enzo', 'M']],
        [['Isabella', 'F'], ['Theo', 'M'], ['Maria Clara', 'F'], ['Bernardo', 'M'], ['Lívia', 'F']],
        [['Rafael', 'M'], ['Cecília', 'F'], ['Samuel', 'M'], ['Luna', 'F'], ['Nicolas', 'M']],
    ];

    /** Necessidades específicas, por número do aluno (1..25). */
    private const ALLERGY = [3, 12, 19];
    private const FOOD_RESTRICTION = [7, 19];
    private const NEURODIVERGENT = [10, 22];
    private const SPECIAL_CARE = [10];

    private const NOTES = [
        3 => 'Alergia a amendoim: avisar a cantina.',
        6 => 'Apresenta boa interação com os colegas.',
        9 => 'Demonstra facilidade nas atividades propostas.',
        10 => 'Necessita de acompanhamento durante atividades em grupo.',
        14 => 'Necessita de estímulos para desenvolver autonomia.',
        17 => 'Aluno participativo e comunicativo.',
        21 => 'Demonstra interesse pelas atividades motoras.',
    ];

    public function run(): void
    {
        $coordinator = User::where('username', 'coordinator01')->first();

        DB::transaction(function () use ($coordinator) {
            foreach (self::CHILDREN as $g => $children) {
                $guardian = User::where('username', sprintf('guardian%02d', $g + 1))->firstOrFail();
                $surname = collect(explode(' ', $guardian->name))->last();

                foreach ($children as $j => [$firstName, $gender]) {
                    $n = $g * 5 + $j + 1;      // 1..25
                    $year = $j + 1;            // 1º ao 5º ano

                    $classroom = Classroom::where('name', $year.'º Ano A')->firstOrFail();

                    $student = $this->upsertUser(sprintf('aluno%03d', $n), [
                        'name' => "{$firstName} {$surname}",
                        'email' => sprintf('aluno%03d@zuni.test', $n),
                        'role' => UserRole::STUDENT,
                        'status' => UserStatus::ACTIVE,
                        'birth_date' => Carbon::create(now()->year - (5 + $year), (($n * 3) % 12) + 1, (($n * 7) % 27) + 1)->toDateString(),
                        'gender' => $gender,
                    ]);

                    $sheet = StudentSheet::updateOrCreate(['student_id' => $student->id], [
                        'guardian_id' => $guardian->id,
                        'classroom_id' => $classroom->id,
                        'registration_number' => sprintf('ALU-%d-%04d', now()->year, $n),

                        'neurodivergent' => in_array($n, self::NEURODIVERGENT, true),
                        'allergy' => in_array($n, self::ALLERGY, true),
                        'food_restriction' => in_array($n, self::FOOD_RESTRICTION, true),
                        'special_care' => in_array($n, self::SPECIAL_CARE, true),

                        'notes' => self::NOTES[$n] ?? null,
                    ]);

                    // Matrícula já aprovada pela coordenação.
                    Enrollment::updateOrCreate(['sheet_id' => $sheet->id], [
                        'reviewed_by' => $coordinator?->id,
                        'reviewed_at' => now(),
                    ]);
                }
            }
        });
    }
}
