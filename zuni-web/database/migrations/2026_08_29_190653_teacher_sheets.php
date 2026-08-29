<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_sheets', function (Blueprint $table) {

            $table->id();


            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete()->unique();


            $table->enum('status', ['pending','active','inactive','suspended'])->default('pending');


            $table->string('name', 100);

            $table->date('birth_date')->nullable();

            $table->enum('gender', ['M','F','O'])->nullable();


            $table->string('cpf', 14)->nullable()->unique();

            $table->string('rg', 20)->nullable();


            $table->string('phone', 20)->nullable();

            $table->string('email', 150)->nullable();


            $table->string('formation', 150)->nullable();

            $table->string('specialization', 150)->nullable();

            // Matrícula/registro do professor na instituição
            $table->string('registration', 50)->nullable()->unique();

            $table->date('hire_date')->nullable();


            $table->string('street', 100)->nullable();

            $table->string('number', 10)->nullable();

            $table->string('district', 50)->nullable();

            $table->string('city', 50)->nullable();

            $table->string('state', 50)->nullable();


            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_sheets');
    }
};