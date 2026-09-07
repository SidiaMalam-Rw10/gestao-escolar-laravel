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
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained('users')->onDelete('cascade');
            $table->string('disciplina');
            $table->decimal('tpi', 5, 2)->nullable(); // Trabalho Prático Individual
            $table->decimal('co', 5, 2)->nullable(); // Controlo Oral
            $table->decimal('tg', 5, 2)->nullable(); // Trabalho em Grupo
            $table->decimal('media', 5, 2)->nullable(); // Média dos 3 acima
            $table->decimal('exame', 5, 2)->nullable();
            $table->decimal('mg', 5, 2)->nullable(); // Média Geral (final)
            $table->integer('trimestre')->default(1); // 1, 2 ou 3
            $table->year('ano_lectivo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
