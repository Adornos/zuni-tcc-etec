<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('director_sheets', function (Blueprint $table) {

            $table->id();

            $table->foreignId('director_id')->constrained('users')->cascadeOnDelete();

            // Dados pessoais
            $table->string('name');
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();

            // Formação
            $table->string('formation')->nullable();

            // Endereço
            $table->string('street', 100)->nullable();
            $table->string('number', 10)->nullable();
            $table->string('district', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 50)->nullable();

            // Dados profissionais
            $table->string('registration')->nullable();
            $table->date('hire_date')->nullable();

            // Status
            $table->string('status')->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('director_sheets');
    }
};