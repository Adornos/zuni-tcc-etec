<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sistema de proficiências. NÃO altera nem apaga `student_sheets` (colunas legadas
     * sociability, autonomy, ...) nem `classroom_performances`; ambas ficam como legado.
     *
     * Valores usam decimal (nunca float). Chaves para `proficiencies` são RESTRICT e
     * para usuários são SET NULL, para que o histórico nunca desapareça por cascata.
     */
    public function up(): void
    {
        Schema::create('proficiencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_proficiencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_sheet_id')->constrained('student_sheets')->cascadeOnDelete();
            $table->foreignId('proficiency_id')->constrained('proficiencies')->restrictOnDelete();

            // Valor de partida da série (5 ou valor importado do legado). Base do reprocessamento.
            $table->decimal('base_value', 6, 4)->default(5);
            $table->decimal('value', 6, 4)->default(5);
            $table->timestamp('last_observed_at')->nullable();
            $table->timestamps();

            $table->unique(['student_sheet_id', 'proficiency_id']);
            $table->index('proficiency_id');
        });

        Schema::create('proficiency_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_sheet_id')->constrained('student_sheets')->cascadeOnDelete();
            $table->foreignId('proficiency_id')->constrained('proficiencies')->restrictOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('direction', 10);               // positive | negative
            $table->decimal('intensity', 5, 2);             // informada pelo professor
            $table->text('reason')->nullable();
            $table->timestamp('observed_at');

            // Auditoria do cálculo
            $table->decimal('weight', 8, 6);                // peso temporal aplicado
            $table->decimal('requested_delta', 8, 4);       // alteração teórica (antes dos limites)
            $table->decimal('applied_delta', 8, 4);         // alteração efetiva (após limites)
            $table->decimal('value_before', 6, 4);
            $table->decimal('value_after', 6, 4);

            // Estado / anulação
            $table->string('status', 10)->default('valid'); // valid | voided
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('voided_at')->nullable();
            $table->text('void_reason')->nullable();
            $table->timestamp('recalculated_at')->nullable();

            // Proteção contra envio duplicado do mesmo formulário.
            $table->string('request_token', 64)->nullable()->unique();

            $table->timestamps();

            $table->index(['student_sheet_id', 'proficiency_id', 'observed_at'], 'prof_obs_series_index');
            $table->index(['author_id', 'observed_at']);
            $table->index('status');
        });

        Schema::create('proficiency_recalculation_logs', function (Blueprint $table) {
            $table->id();
            // Nomes explícitos: o MySQL limita identificadores a 64 caracteres e o nome automático
            // do Laravel para esta FK teria 65.
            $table->foreignId('proficiency_observation_id')
                ->constrained('proficiency_observations', 'id', 'prof_recalc_obs_fk')
                ->restrictOnDelete();
            $table->foreignId('cause_observation_id')->nullable()->constrained('proficiency_observations')->nullOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();

            $table->decimal('old_weight', 8, 6);
            $table->decimal('old_requested_delta', 8, 4);
            $table->decimal('old_applied_delta', 8, 4);
            $table->decimal('old_value_before', 6, 4);
            $table->decimal('old_value_after', 6, 4);

            $table->decimal('new_weight', 8, 6);
            $table->decimal('new_requested_delta', 8, 4);
            $table->decimal('new_applied_delta', 8, 4);
            $table->decimal('new_value_before', 6, 4);
            $table->decimal('new_value_after', 6, 4);

            $table->timestamp('created_at')->nullable();
            $table->index('proficiency_observation_id', 'prof_recalc_obs_index');
        });

        Schema::create('classroom_proficiency_averages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('proficiency_id')->constrained('proficiencies')->restrictOnDelete();
            $table->decimal('average', 6, 4)->nullable();   // null = nenhum aluno elegível
            $table->unsignedInteger('students_count')->default(0);
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['classroom_id', 'proficiency_id'], 'class_prof_avg_unique');
        });

        Schema::create('classroom_proficiency_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('proficiency_id')->constrained('proficiencies')->restrictOnDelete();
            $table->date('reference_date');
            $table->decimal('average', 6, 4)->nullable();
            $table->unsignedInteger('students_count')->default(0);
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->unique(['classroom_id', 'proficiency_id', 'reference_date'], 'class_prof_snapshot_unique');
            $table->index(['classroom_id', 'reference_date'], 'class_prof_snapshot_date_index');
        });

        $this->seedLegacySkills();
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_proficiency_snapshots');
        Schema::dropIfExists('classroom_proficiency_averages');
        Schema::dropIfExists('proficiency_recalculation_logs');
        Schema::dropIfExists('proficiency_observations');
        Schema::dropIfExists('student_proficiencies');
        Schema::dropIfExists('proficiencies');
    }

    /**
     * Cadastra as 5 habilidades que antes eram colunas fixas e copia os valores atuais
     * (alunos ativos) como valor inicial, sem perder o que já estava cadastrado.
     * As colunas antigas permanecem intactas.
     */
    private function seedLegacySkills(): void
    {
        $legacy = [
            'sociability' => ['Sociabilidade', 'sociabilidade'],
            'autonomy' => ['Autonomia', 'autonomia'],
            'engagement' => ['Engajamento', 'engajamento'],
            'communication' => ['Comunicação', 'comunicacao'],
            'motor_development' => ['Desenvolvimento motor', 'desenvolvimento-motor'],
        ];

        $now = now();
        $ids = [];

        foreach ($legacy as $column => [$name, $slug]) {
            $ids[$column] = DB::table('proficiencies')->insertGetId([
                'name' => $name,
                'slug' => $slug,
                'description' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('student_sheets')
            ->join('users', 'users.id', '=', 'student_sheets.student_id')
            ->where('users.status', 'active')
            ->select('student_sheets.id', ...array_map(fn ($c) => "student_sheets.$c", array_keys($legacy)))
            ->orderBy('student_sheets.id')
            ->chunk(500, function ($sheets) use ($legacy, $ids, $now) {
                $rows = [];

                foreach ($sheets as $sheet) {
                    foreach ($legacy as $column => $_) {
                        $value = round((float) ($sheet->{$column} ?? 5), 4);
                        $value = min(10, max(0, $value));

                        $rows[] = [
                            'student_sheet_id' => $sheet->id,
                            'proficiency_id' => $ids[$column],
                            'base_value' => $value,
                            'value' => $value,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                DB::table('student_proficiencies')->insertOrIgnore($rows);
            });
    }
};
