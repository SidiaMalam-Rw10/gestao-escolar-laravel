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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique()->nullable(); // Número de aluno/funcionário
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'diretor', 'financeiro', 'professor', 'aluno', 'auxiliar'])->default('aluno');
            $table->string('telefone')->nullable();
            $table->text('endereco')->nullable();
            $table->enum('genero', ['M', 'F'])->nullable();
            $table->string('foto')->nullable();
            $table->string('disciplina')->nullable(); // Para professores
            $table->unsignedBigInteger('turma_id')->nullable(); // Para alunos - FK será adicionada depois
            $table->unsignedBigInteger('encarregado_id')->nullable(); // Para alunos - FK será adicionada depois
            $table->string('nivel')->nullable(); // Para alunos
            $table->year('ano_lectivo')->nullable(); // Para alunos
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
