<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'proprietario' to the enum (Proprietário tem o mesmo nível de acesso que o diretor)
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'diretor', 'financeiro', 'professor', 'aluno', 'auxiliar', 'pctp', 'encarregado', 'funcionario', 'proprietario') NOT NULL DEFAULT 'aluno'");
        } elseif ($driver === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'diretor', 'financeiro', 'professor', 'aluno', 'auxiliar', 'pctp', 'encarregado', 'funcionario', 'proprietario'])->default('aluno')->after('password');
            });
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");
            DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'aluno'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        // Multi-role users: existing rows may hold 'proprietario'; ensure they have a valid value first
        if ($driver === 'mysql' || $driver === 'sqlite' || $driver === 'pgsql') {
            \App\Models\User::where('role', 'proprietario')->update(['role' => 'diretor']);
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'diretor', 'financeiro', 'professor', 'aluno', 'auxiliar', 'pctp', 'encarregado', 'funcionario') NOT NULL DEFAULT 'aluno'");
        } elseif ($driver === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'diretor', 'financeiro', 'professor', 'aluno', 'auxiliar', 'pctp', 'encarregado', 'funcionario'])->default('aluno')->after('password');
            });
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");
            DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'aluno'");
        }
    }
};