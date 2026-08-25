<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['internal', 'external']); // internal = docentes | external = responsáveis
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete(); // director ou coordinator
            $table->foreignId('student_id')->nullable()->constrained('studentSheet')->cascadeOnDelete(); // usado em relatórios externos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }

};
