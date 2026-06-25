<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            
            $table->id();

            // Guardian (User relation)
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Student data
            $table->string('name', 100);
            $table->date('birth_date')->nullable();

            $table->enum('gender', ['M', 'F', 'O'])->nullable();

            $table->string('class', 50)->nullable(); // e.g. "3rd Grade A"
            $table->integer('age')->nullable();

            // Address
            $table->string('street', 100)->nullable();
            $table->string('number', 10)->nullable();
            $table->string('district', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 50)->nullable();

            // Special conditions
            $table->boolean('neurodivergent')->nullable();
            $table->boolean('allergy')->nullable();
            $table->boolean('food_restriction')->nullable();
            $table->boolean('special_care')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};