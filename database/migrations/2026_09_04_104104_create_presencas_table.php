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
        Schema::create('presencas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pode ser professor ou aluno
            $table->enum('tipo', ['professor', 'aluno'])->default('professor');
            $table->integer('mes'); // 1-12
            $table->year('ano');
            $table->integer('presencas')->default(0);
            $table->integer('faltas')->default(0);
            $table->integer('justificadas')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presencas');
    }
};
