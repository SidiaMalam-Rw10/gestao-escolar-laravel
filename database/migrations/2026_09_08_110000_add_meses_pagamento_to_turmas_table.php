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
        Schema::table('turmas', function (Blueprint $table) {
            $table->integer('meses_pagamento')->nullable()->default(12)->after('propina_mensal');
        });

        // Mantém 12 meses como padrão para turmas existentes
        \Illuminate\Support\Facades\DB::table('turmas')->update(['meses_pagamento' => 12]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turmas', function (Blueprint $table) {
            $table->dropColumn('meses_pagamento');
        });
    }
};