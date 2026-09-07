<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permite que o mesmo número de aluno exista em turmas diferentes.
     * A unicidade passa a ser verificada por turma na camada de validação.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_numero_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unique('numero');
        });
    }
};