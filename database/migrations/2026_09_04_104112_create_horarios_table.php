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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turma_id')->constrained()->onDelete('cascade');
            $table->enum('dia_semana', ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado']);
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->string('disciplina');
            $table->foreignId('professor_id')->constrained('users')->onDelete('cascade');
            $table->string('sala')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
