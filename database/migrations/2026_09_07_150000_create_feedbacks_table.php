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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo', ['feedback', 'problema'])->default('feedback');
            $table->string('assunto', 150);
            $table->text('mensagem');
            $table->string('pagina')->nullable();
            $table->enum('estado', ['novo', 'em_analise', 'resolvido'])->default('novo');
            $table->text('resposta')->nullable();
            $table->timestamp('respondido_em')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};