<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Classroom;
use App\Models\StudentSheet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GeneralStudentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            /*
             * Procura responsáveis já existentes.
             * Caso não exista nenhum, cria alguns automaticamente
             * para que o guardian_id possa ser preenchido.
             */
            $guardians = User::where('role', UserRole::GUARDIAN->value)->get();

            if ($guardians->isEmpty()) {

                for ($i = 1; $i <= 10; $i++) {

                    $guardians->push(
                        User::create([
                            'name' => fake('pt_BR')->name(),
                            'username' => 'responsavel' . $i,
                            'email' => "responsavel{$i}@zuni.test",
                            'password' => Hash::make('123456'),
                            'role' => UserRole::GUARDIAN,
                        ])
                    );
                }
            }

            /*
             * Busca turmas de 1º ano.
             *
             * Caso ainda não existam, cria algumas turmas.
             */
            $classrooms = Classroom::where('grade', '1-ano')
                ->get();

            if ($classrooms->isEmpty()) {

                for ($i = 1; $i <= 3; $i++) {

                    $classrooms->push(
                        Classroom::create([
                            'name' => '1º Ano ' . chr(64 + $i),
                            'grade' => '1-ano',
                            'shift' => 'morning',
                            'capacity' => 25,
                            'status' => 'active',
                        ])
                    );
                }
            }

            for ($i = 1; $i <= 60; $i++) {

                $fake = fake('pt_BR');

                /*
                 * Entre 5 e 6 anos.
                 *
                 * Como o exemplo fornecido é de uma criança
                 * nascida em 2020, usamos uma faixa semelhante.
                 */
                $birthDate = $fake->dateTimeBetween(
                    '2019-01-01',
                    '2020-12-31'
                );

                $birthDate = Carbon::instance($birthDate);

                /*
                 * Seleciona um responsável existente.
                 */
                $guardian = $guardians->random();

                /*
                 * Seleciona uma turma aleatória.
                 */
                $classroom = $classrooms->random();

                /*
                 * Cria o usuário do aluno.
                 */

                $padrao = '/\b(Dr\b\.?|Dra\b\.?|Sr\b\.?|Sra\b\.?|Srta\b\.?|Prof\b\.?|Profa\b\.?)\s+/i';

                $student = User::create([

                    'name' => trim(preg_replace($padrao, '', $fake->name())),

                    'username' => 'aluno' . str_pad(
                        $i,
                        3,
                        '0',
                        STR_PAD_LEFT
                    ),

                    'email' =>
                        'aluno' .
                        str_pad($i, 3, '0', STR_PAD_LEFT) .
                        '@zuni.test',

                    'birth_date' => $birthDate,

                    'gender' => $fake->randomElement([
                        'M',
                        'F',
                    ]),

                    'password' => Hash::make('123456'),

                    'role' => UserRole::STUDENT,
                ]);

                /*
                 * Idade calculada automaticamente.
                 */
                $age = $birthDate->age;

                /*
                 * Cria a StudentSheet.
                 */
                StudentSheet::create([

                    'student_id' => $student->id,

                    'guardian_id' => $guardian->id,

                    'classroom_id' => $classroom->id,

                    'registration_number' =>
                        'ALU-' .
                        date('Y') .
                        '-' .
                        str_pad($i, 4, '0', STR_PAD_LEFT),

                    'age' => $age,

                    /*
                     * Cinco parâmetros pedagógicos.
                     *
                     * Escala de 0 a 10.
                     */
                    'sociability' => $fake->randomFloat(2, 5, 10),

                    'autonomy' => $fake->randomFloat(2, 5, 10),

                    'engagement' => $fake->randomFloat(2, 10, 10),

                    'communication' => $fake->randomFloat(2, 5, 10),

                    'motor_development' => $fake->randomFloat(2, 5, 10),

                    /*
                     * Necessidades específicas.
                     */
                    'neurodivergent' => $fake->boolean(10),

                    'allergy' => $fake->boolean(15),

                    'food_restriction' => $fake->boolean(10),

                    'special_care' => $fake->boolean(10),

                    /*
                     * Observações.
                     */
                    'notes' => $fake->optional(
                        0.4
                    )->randomElement([

                        'Aluno demonstra dificuldade de concentração.',

                        'Necessita de acompanhamento durante atividades em grupo.',

                        'Apresenta boa interação com os colegas.',

                        'Demonstra facilidade nas atividades propostas.',

                        'Necessita de estímulos para desenvolver autonomia.',

                        'Apresenta bom desenvolvimento da comunicação.',

                        'Demonstra interesse pelas atividades motoras.',

                        'Necessita de apoio adicional em algumas atividades.',

                        'Aluno participativo e comunicativo.',

                        'Apresenta evolução satisfatória ao longo das atividades.',

                    ]),
                ]);
            }
        });
    }
}