<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class LoginTest extends Command
{
    protected $signature = 'login:test {username} {password}';

    protected $description = 'Testa um login contra a base de dados atual (a que o pedido iria usar no domínio raiz).';

    public function handle(): int
    {
        $username = $this->argument('username');
        $password = $this->argument('password');

        $conn = config('database.default');
        $db = config("database.connections.{$conn}.database");

        $user = User::where('username', $username)->first();

        $this->info("BD atual: {$db}");

        if (! $user) {
            $this->error("Não existe o utilizador «{$username}» nesta BD.");

            return self::FAILURE;
        }

        if (! Hash::check($password, $user->password)) {
            $this->error("Palavra-passe incorreta para «{$user->name}».");

            return self::FAILURE;
        }

        $this->info("Login OK: {$user->name} (role: {$user->role})");

        return self::SUCCESS;
    }
}