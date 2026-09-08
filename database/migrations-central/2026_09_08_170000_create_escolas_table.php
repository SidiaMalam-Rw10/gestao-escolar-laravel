<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escolas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('slug', 100)->unique();
            $table->string('nome_bd', 64)->unique();
            $table->string('contacto', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('endereco', 255)->nullable();
            $table->string('admin_nome', 150);
            $table->string('admin_username', 50);
            $table->boolean('ativa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escolas');
    }
};