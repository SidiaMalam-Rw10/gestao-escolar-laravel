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
        Schema::create('encarregados', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->text('endereco')->nullable();
            $table->enum('genero', ['M', 'F'])->nullable();
            $table->string('parentesco')->nullable(); // Pai, Mãe, Tio, etc
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encarregados');
    }
};
