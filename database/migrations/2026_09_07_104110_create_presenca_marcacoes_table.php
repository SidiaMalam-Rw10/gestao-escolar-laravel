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
        Schema::create('presenca_marcacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('estado', ['presente', 'falta', 'justificada'])->default('presente');
            $table->date('data');
            $table->time('hora');
            $table->integer('mes');
            $table->year('ano');
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'mes', 'ano']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presenca_marcacoes');
    }
};