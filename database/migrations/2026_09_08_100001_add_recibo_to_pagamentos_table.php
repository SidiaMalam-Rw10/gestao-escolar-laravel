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
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->string('recibo_numero', 40)->nullable()->unique()->after('observacoes');
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete()->after('recibo_numero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropForeign(['registrado_por']);
            $table->dropColumn(['recibo_numero', 'registrado_por']);
        });
    }
};