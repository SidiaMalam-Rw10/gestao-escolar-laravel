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
        // MySQL-specific: alter the enum column to add 'auxiliar'
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'diretor', 'financeiro', 'professor', 'aluno', 'auxiliar') NOT NULL DEFAULT 'aluno'");
        } elseif ($driver === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'diretor', 'financeiro', 'professor', 'aluno', 'auxiliar'])->default('aluno')->after('password');
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

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'diretor', 'financeiro', 'professor', 'aluno') NOT NULL DEFAULT 'aluno'");
        } elseif ($driver === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'diretor', 'financeiro', 'professor', 'aluno'])->default('aluno')->after('password');
            });
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");
            DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'aluno'");
        }
    }
};