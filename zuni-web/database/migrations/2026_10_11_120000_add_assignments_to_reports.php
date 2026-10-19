<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Evolui a tabela `reports` (criada em 2026_08_26_113827) em vez de reescrevê-la
     * e cria as tabelas pivot N:N para alunos (StudentSheet) e salas (Classroom).
     *
     * Exclusividade alunos OU salas:
     *  - `reports.target` guarda a categoria ('students' | 'classrooms').
     *  - Cada pivot carrega uma coluna `target` fixa e uma FK composta
     *    (report_id, target) -> reports(id, target). Assim o banco recusa uma linha
     *    de `report_classroom` para um Report cujo target seja 'students' (e vice-versa).
     *  - "Nunca sem atribuições" não é expressável como constraint entre tabelas;
     *    essa regra fica no ReportService, dentro de transação.
     *
     * A coluna legada `sheet_id` é mantida (somente leitura) para não exigir
     * recriação do banco; os dados dela são copiados para o pivot.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('type')->default('notice');
            $table->string('target')->default('students');
            $table->boolean('is_active')->default(true);

            $table->unique(['id', 'target'], 'reports_id_target_unique');
            $table->index(['is_active', 'created_at'], 'reports_active_created_index');
        });

        Schema::create('report_student_sheet', function (Blueprint $table) {
            $table->unsignedBigInteger('report_id');
            $table->string('target')->default('students');
            $table->foreignId('student_sheet_id')->constrained('student_sheets')->cascadeOnDelete();

            $table->primary(['report_id', 'student_sheet_id']);
            $table->foreign(['report_id', 'target'])
                ->references(['id', 'target'])
                ->on('reports')
                ->cascadeOnDelete();
        });

        Schema::create('report_classroom', function (Blueprint $table) {
            $table->unsignedBigInteger('report_id');
            $table->string('target')->default('classrooms');
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();

            $table->primary(['report_id', 'classroom_id']);
            $table->foreign(['report_id', 'target'])
                ->references(['id', 'target'])
                ->on('reports')
                ->cascadeOnDelete();
        });

        // Dados legados: relatórios já ligados a um aluno via sheet_id.
        DB::statement(
            "INSERT INTO report_student_sheet (report_id, target, student_sheet_id)
             SELECT id, 'students', sheet_id FROM reports WHERE sheet_id IS NOT NULL"
        );

        // Relatórios legados sem nenhuma atribuição não atendem à nova regra:
        // ficam inativos (não são apagados) até serem excluídos pelo autor.
        DB::table('reports')->whereNull('sheet_id')->update(['is_active' => false]);
    }

    public function down(): void
    {
        Schema::dropIfExists('report_classroom');
        Schema::dropIfExists('report_student_sheet');

        Schema::table('reports', function (Blueprint $table) {
            $table->dropUnique('reports_id_target_unique');
            $table->dropIndex('reports_active_created_index');
            $table->dropColumn(['type', 'target', 'is_active']);
        });
    }
};
