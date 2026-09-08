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
        Schema::create('mensagem_destinatarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mensagem_id')->constrained('mensagens')->cascadeOnDelete();
            $table->foreignId('destinatario_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('lida')->default(false);
            $table->dateTime('lida_em')->nullable();
            $table->unique(['mensagem_id', 'destinatario_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensagem_destinatarios');
    }
};
