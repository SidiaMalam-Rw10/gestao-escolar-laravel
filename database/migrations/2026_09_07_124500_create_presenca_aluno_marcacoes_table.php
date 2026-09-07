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
        Schema::create('presenca_aluno_marcacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('professor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('turma_id')->nullable()->constrained('turmas')->nullOnDelete();
            $table->enum('estado', ['presente', 'falta', 'justificada'])->default('presente');
            $table->date('data');
            $table->time('hora')->nullable();
            $table->integer('mes'); // 1-12
            $table->year('ano');
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['aluno_id', 'mes', 'ano']);
            $table->index(['turma_id', 'mes', 'ano']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presenca_aluno_marcacoes');
    }
};