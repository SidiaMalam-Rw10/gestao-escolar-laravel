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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained('users')->onDelete('cascade');
            $table->integer('mes'); // 1-12
            $table->year('ano');
            $table->decimal('valor', 10, 2);
            $table->timestamp('data_pagamento')->useCurrent();
            $table->enum('status', ['pendente', 'pago', 'atrasado'])->default('pendente');
            $table->string('metodo_pagamento')->nullable(); // Dinheiro, Transferência, etc
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
