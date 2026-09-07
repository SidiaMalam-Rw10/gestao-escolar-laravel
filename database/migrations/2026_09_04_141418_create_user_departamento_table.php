<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_departamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('departamento_id')->constrained()->onDelete('cascade');
            $table->string('cargo')->nullable()->comment('Ex: Professor, Chefe, Secretário, Adjunto');
            $table->string('regime')->default('regular')->comment('regular, especial, tempo_integral, parcial');
            $table->boolean('is_principal')->default(false);
            $table->year('ano_lectivo')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'departamento_id', 'ano_lectivo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_departamento');
    }
};
