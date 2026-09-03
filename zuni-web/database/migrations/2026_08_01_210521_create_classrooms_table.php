<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();

            // Identificação da turma
            $table->string('name', 50);

            // Ano/série
            $table->string('grade', 50);

            // Período
            $table->enum('shift', ['morning','afternoon','full_time','evening',])->nullable();

            // Capacidade máxima
            $table->unsignedInteger('capacity')->nullable();

            // Professor responsável
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();

            // Status da turma
            $table->enum('status', ['active','inactive'])->default('active');

            $table->timestamps();
        });

            //Tabela de relacionamento de professor e sala N : N
        Schema::create('classroom_teacher', function (Blueprint $table) {
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();

            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();

            $table->primary(['classroom_id', 'teacher_id']);
        });

        Schema::create('classroom_performances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('period');

            $table->decimal('average_grade', 4, 2)->nullable();

            $table->decimal('sociability', 4, 2)->nullable();
            $table->decimal('autonomy', 4, 2)->nullable();
            $table->decimal('engagement', 4, 2)->nullable();
            $table->decimal('communication', 4, 2)->nullable();
            $table->decimal('motor_development', 4, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['classroom_id','year','period',]);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('classroom_teacher');
        Schema::dropIfExists('classroom_performances');
        
    }
};