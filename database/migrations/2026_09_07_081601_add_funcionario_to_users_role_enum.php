<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

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

    public function down(): void
    {
        \App\Models\User::where('role', 'funcionario')->update(['role' => 'aluno']);

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'diretor', 'financeiro', 'professor', 'aluno', 'auxiliar', 'pctp', 'encarregado') NOT NULL DEFAULT 'aluno'");
        } elseif ($driver === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'diretor', 'financeiro', 'professor', 'aluno', 'auxiliar', 'pctp', 'encarregado'])->default('aluno')->after('password');
            });
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");
            DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'aluno'");
        }
    }
};
