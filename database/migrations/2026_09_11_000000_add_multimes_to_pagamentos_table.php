<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->unsignedTinyInteger('quantidade_meses')->nullable()->after('mes');
            $table->unsignedTinyInteger('mes_fim')->nullable()->after('quantidade_meses');
            $table->year('ano_fim')->nullable()->after('mes_fim');
        });
    }

    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropColumn(['quantidade_meses', 'mes_fim', 'ano_fim']);
        });
    }
};