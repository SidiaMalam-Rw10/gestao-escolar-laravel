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
        Schema::create('avisos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('mensagem');
            $table->foreignId('remetente_id')->constrained('users')->onDelete('cascade');
            $table->enum('destinatario_tipo', ['todos', 'alunos', 'professores', 'turma', 'individual'])->default('todos');
            $table->foreignId('destinatario_id')->nullable()->constrained('users')->onDelete('cascade'); // Para individual
            $table->foreignId('turma_id')->nullable()->constrained()->onDelete('cascade'); // Para turma específica
            $table->boolean('lido')->default(false);
            $table->timestamp('lido_em')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avisos');
    }
};
