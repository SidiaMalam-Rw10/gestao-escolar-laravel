<?php

namespace Database\Seeders;

use App\Models\Configuracao;
use Illuminate\Database\Seeder;

class ConfiguracaoSeeder extends Seeder
{
    public function run(): void
    {
        $padroes = [
            ['chave' => 'escola.moeda', 'valor' => 'Xof', 'grupo' => 'escola'],
            ['chave' => 'sistema.presenca_limiar_faltas', 'valor' => '3', 'grupo' => 'sistema'],
            ['chave' => 'sistema.turmas_limite_alunos', 'valor' => '40', 'grupo' => 'sistema'],
            ['chave' => 'sistema.pagamentos_lembrete_dias', 'valor' => '5', 'grupo' => 'sistema'],
        ];

        foreach ($padroes as $p) {
            Configuracao::firstOrCreate(['chave' => $p['chave']], $p);
        }

        Configuracao::limparCache();
    }
}