<?php

namespace App\Http\Controllers;

use App\Models\Escola;
use App\Models\Atividade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EscolaController extends Controller
{
    private const CONFIGURACOES_PADRAO = [
        ['chave' => 'escola.moeda', 'valor' => 'Xof', 'grupo' => 'escola'],
        ['chave' => 'sistema.presenca_limiar_faltas', 'valor' => '3', 'grupo' => 'sistema'],
        ['chave' => 'sistema.turmas_limite_alunos', 'valor' => '40', 'grupo' => 'sistema'],
        ['chave' => 'sistema.pagamentos_lembrete_dias', 'valor' => '5', 'grupo' => 'sistema'],
        ['chave' => 'sistema.avisos_obrigar_leitura', 'valor' => '0', 'grupo' => 'sistema'],
    ];

    public function index(): View
    {
        $escolas = Escola::orderBy('nome')->get();

        return view('central.escolas.index', compact('escolas'));
    }

    public function create(): View
    {
        return view('central.escolas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nome' => 'required|string|max:150',
            'contacto' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'endereco' => 'nullable|string|max:255',
            'admin_nome' => 'required|string|max:150',
            'admin_username' => 'required|string|max:50|regex:/^[a-z0-9._-]+$/i',
            'admin_password' => 'required|string|min:8',
        ]);

        $slug = $this->slugUnico($data['nome']);
        $nomeBd = $this->nomeBdUnico($slug);

        DB::statement("CREATE DATABASE `{$nomeBd}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        $conn = config('database.default');
        $connEscola = 'escola_' . $nomeBd;

        config(["database.connections.{$connEscola}" => array_merge(
            config("database.connections.{$conn}"),
            ['database' => $nomeBd]
        )]);

        try {
            Artisan::call('migrate', ['--database' => $connEscola, '--force' => true]);

            DB::connection($connEscola)->table('configuracoes')->insert(array_map(fn ($c) => $c + ['created_at' => now(), 'updated_at' => now()], self::CONFIGURACOES_PADRAO));
            DB::connection($connEscola)->table('configuracoes')->where('chave', 'escola.moeda')->update(['valor' => 'Xof', 'updated_at' => now()]);
            DB::connection($connEscola)->table('configuracoes')->updateOrInsert(['chave' => 'escola.nome'], ['valor' => $data['nome'], 'grupo' => 'escola', 'created_at' => now(), 'updated_at' => now()]);

            DB::connection($connEscola)->table('users')->insert([
                'name' => $data['admin_nome'],
                'username' => $data['admin_username'],
                'password' => Hash::make($data['admin_password']),
                'role' => 'admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            DB::statement("DROP DATABASE IF EXISTS `{$nomeBd}`");

            throw $e;
        }

        $escola = Escola::create([
            'nome' => $data['nome'],
            'slug' => $slug,
            'nome_bd' => $nomeBd,
            'contacto' => $data['contacto'] ?? null,
            'email' => $data['email'] ?? null,
            'endereco' => $data['endereco'] ?? null,
            'admin_nome' => $data['admin_nome'],
            'admin_username' => $data['admin_username'],
        ]);

        Atividade::registar(
            'escola_criada',
            "Criou a escola «{$escola->nome}» com a base de dados {$escola->nome_bd}",
            auth()->user()
        );

        return redirect()->route('central.escolas.index')
            ->with('success', "Escola «{$escola->nome}» criada com sucesso. Aceda-a em {$escola->slug}.");
    }

    public function edit(Escola $escola): View
    {
        return view('central.escolas.edit', compact('escola'));
    }

    public function update(Request $request, Escola $escola): RedirectResponse
    {
        $data = $request->validate([
            'nome' => 'required|string|max:150',
            'contacto' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'endereco' => 'nullable|string|max:255',
            'admin_nome' => 'required|string|max:150',
            'admin_username' => 'required|string|max:50|regex:/^[a-z0-9._-]+$/i',
            'ativa' => 'nullable|boolean',
        ]);

        $escola->update($data + ['ativa' => $request->boolean('ativa')]);

        Atividade::registar(
            'escola_actualizada',
            "Actualizou os dados da escola «{$escola->nome}»",
            auth()->user()
        );

        return redirect()->route('central.escolas.index')
            ->with('success', "Dados da escola «{$escola->nome}» actualizados.");
    }

    public function destroy(Escola $escola): RedirectResponse
    {
        $nome = $escola->nome;
        $nomeBd = $escola->nome_bd;

        $conn = config('database.default');
        config(["database.connections.{$conn}.database" => env('DB_DATABASE')]);
        DB::purge($conn);

        DB::statement("DROP DATABASE IF EXISTS `{$nomeBd}`");

        $escola->delete();

        Atividade::registar(
            'escola_eliminada',
            "Eliminou a escola «{$nome}» e a sua base de dados {$nomeBd}",
            auth()->user()
        );

        return redirect()->route('central.escolas.index')
            ->with('success', "Escola «{$nome}» eliminada com a sua base de dados.");
    }

    private function slugUnico(string $nome): string
    {
        $base = Str::slug($nome) ?: 'escola';
        $slug = $base;
        $i = 2;

        while (Escola::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    private function nomeBdUnico(string $slug): string
    {
        $base = 'miscool_' . str_replace('-', '_', $slug);
        $base = Str::lower($base);
        $nome = $base;
        $i = 2;

        while (Escola::where('nome_bd', $nome)->exists()) {
            $nome = $base . '_' . $i;
        }

        return $nome;
    }
}