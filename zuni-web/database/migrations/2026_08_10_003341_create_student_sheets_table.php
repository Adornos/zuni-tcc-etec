<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_sheets', function (Blueprint $table) {

            $table->id();

            // Usuário aluno
            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->unique();

            // Responsável
            $table->foreignId('guardian_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Turma atual
            $table->foreignId('classroom_id')
                ->nullable()
                ->constrained('classrooms')
                ->nullOnDelete();

            // Número de registro
            $table->string('registration_number')->unique()->nullable();

            // Idade
            $table->integer('age')->nullable();

            // Parâmetros de desenvolvimento/desempenho
            $table->decimal('sociability', 4, 2)->nullable();
            $table->decimal('autonomy', 4, 2)->nullable();
            $table->decimal('engagement', 4, 2)->nullable();
            $table->decimal('communication', 4, 2)->nullable();
            $table->decimal('motor_development', 4, 2)->nullable();

            // Necessidades específicas
            $table->boolean('neurodivergent')->nullable();
            $table->boolean('allergy')->nullable();
            $table->boolean('food_restriction')->nullable();
            $table->boolean('special_care')->nullable();

            // Observações
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_sheets');
    }
};