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
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 150);
            $table->text('descricao')->nullable();
            $table->enum('tipo', ['reuniao', 'ferias', 'feriado', 'prova', 'atividade', 'outro'])->default('atividade');
            $table->dateTime('data_inicio');
            $table->dateTime('data_fim')->nullable();
            $table->string('local', 120)->nullable();
            $table->string('cor', 7)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
