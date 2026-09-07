<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuários administrativos
        \App\Models\User::create([
            'name' => 'Administrador',
            'username' => 'admin',
            'email' => 'admin@escola.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        \App\Models\User::create([
            'name' => 'Maria Silva - Diretora',
            'username' => 'diretor',
            'email' => 'diretor@escola.com',
            'password' => bcrypt('diretor123'),
            'role' => 'diretor',
            'genero' => 'F',
            'is_active' => true,
        ]);

        \App\Models\User::create([
            'name' => 'João Santos - Financeiro',
            'username' => 'financeiro',
            'email' => 'financeiro@escola.com',
            'password' => bcrypt('financeiro123'),
            'role' => 'financeiro',
            'genero' => 'M',
            'is_active' => true,
        ]);

        // Criar encarregado de exemplo
        $encarregado = \App\Models\Encarregado::create([
            'nome' => 'Ana Costa',
            'telefone' => '923456789',
            'email' => 'ana@email.com',
            'parentesco' => 'Mãe',
            'genero' => 'F',
        ]);

        // Criar turma de exemplo
        $turma = \App\Models\Turma::create([
            'nome_turma' => '10ª A',
            'nivel' => '10ª Classe',
            'periodo' => 'Manhã',
            'ano_lectivo' => 2024,
            'capacidade' => 40,
        ]);

        // Criar professor
        $professor = \App\Models\User::create([
            'name' => 'Prof. Carlos Mendes',
            'username' => 'prof.carlos',
            'email' => 'carlos@escola.com',
            'password' => bcrypt('professor123'),
            'role' => 'professor',
            'disciplina' => 'Matemática',
            'genero' => 'M',
            'telefone' => '923111222',
            'is_active' => true,
        ]);

        // Associar professor à turma
        $turma->update(['professor_responsavel_id' => $professor->id]);

        // Criar aluno de exemplo
        $aluno = \App\Models\User::create([
            'numero' => '2024001',
            'name' => 'Pedro Almeida',
            'username' => 'pedro.almeida',
            'email' => 'pedro@email.com',
            'password' => bcrypt('aluno123'),
            'role' => 'aluno',
            'genero' => 'M',
            'nivel' => '10ª Classe',
            'ano_lectivo' => 2024,
            'turma_id' => $turma->id,
            'encarregado_id' => $encarregado->id,
            'is_active' => true,
        ]);

        // Criar auxiliar
        \App\Models\User::create([
            'name' => 'Ana Martins - Auxiliar',
            'username' => 'auxiliar',
            'email' => 'auxiliar@escola.com',
            'password' => bcrypt('auxiliar123'),
            'role' => 'auxiliar',
            'genero' => 'F',
            'is_active' => true,
        ]);

        echo "✅ Dados iniciais criados com sucesso!\n";
        echo "📋 Credenciais de acesso:\n";
        echo "   Admin: admin / admin123\n";
        echo "   Diretor: diretor / diretor123\n";
        echo "   Financeiro: financeiro / financeiro123\n";
        echo "   Professor: prof.carlos / professor123\n";
        echo "   Aluno: pedro.almeida / aluno123\n";
        echo "   Auxiliar: auxiliar / auxiliar123\n";
    }
}
