<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona o número de tempo (1-5) a cada aula do horário.
     */
    public function up(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->unsignedTinyInteger('tempo')->nullable()->after('dia_semana');
        });
    }

    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropColumn('tempo');
        });
    }
};