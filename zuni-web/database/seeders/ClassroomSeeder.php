<?php

namespace Database\Seeders;

use App\Enums\ClassroomGrade;
use App\Enums\ClassroomShift;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * 5 turmas (1º ao 5º Ano), uma para cada professor. Sem turmas os alunos não teriam professor
 * responsável, e a avaliação e os relatórios dependem dessa ligação.
 */
class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            ClassroomGrade::FIRST_YEAR,
            ClassroomGrade::SECOND_YEAR,
            ClassroomGrade::THIRD_YEAR,
            ClassroomGrade::FOURTH_YEAR,
            ClassroomGrade::FIFTH_YEAR,
        ];

        foreach ($grades as $index => $grade) {
            $n = $index + 1;

            $classroom = Classroom::updateOrCreate(
                ['name' => $grade->label().' A'],
                [
                    'grade' => $grade,
                    'shift' => $n % 2 === 1 ? ClassroomShift::MORNING : ClassroomShift::AFTERNOON,
                    'capacity' => 25,
                    'status' => 'active',
                ]
            );

            $teacher = User::where('username', sprintf('teacher%02d', $n))->firstOrFail();

            $classroom->teachers()->syncWithoutDetaching([$teacher->id]);
        }
    }
}
