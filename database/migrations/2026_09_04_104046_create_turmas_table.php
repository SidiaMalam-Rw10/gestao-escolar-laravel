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
        Schema::create('turmas', function (Blueprint $table) {
            $table->id();
            $table->string('nome_turma');
            $table->string('nivel'); // Ex: 7ª Classe, 8ª Classe, etc
            $table->enum('periodo', ['Manhã', 'Tarde', 'Noite'])->default('Manhã');
            $table->year('ano_lectivo');
            $table->foreignId('professor_responsavel_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('capacidade')->default(40);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turmas');
    }
};
