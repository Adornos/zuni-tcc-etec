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
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete()->unique();

            // Responsável pelo aluno
            $table->foreignId('guardian_id')->constrained('users')->cascadeOnDelete();

            // Dados específicos do aluno
            $table->string('class', 50)->nullable();

            $table->integer('age')->nullable();

            // Necessidades específicas
            $table->boolean('neurodivergent')->nullable();
            $table->boolean('allergy')->nullable();
            $table->boolean('food_restriction')->nullable();
            $table->boolean('special_care')->nullable();

            // Informações adicionais
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_sheets');
    }
};