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
        Schema::create('ficheiros', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 150);
            $table->text('descricao')->nullable();
            $table->enum('categoria', ['documento', 'livro', 'manual', 'outro'])->default('documento');
            $table->string('caminho', 255);
            $table->string('nome_original', 255);
            $table->string('extensao', 12);
            $table->unsignedBigInteger('tamanho')->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ficheiros');
    }
};
