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

        $table->foreignId('director_id')->constrained('users')->cascadeOnDelete()->unique();

        $table->string('formation', 150)->nullable();
        $table->string('specialization', 150)->nullable();

        $table->string('registration', 50)->nullable()->unique();

        $table->date('hire_date')->nullable();

        $table->text('notes')->nullable();

        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('director_sheets');
    }
};