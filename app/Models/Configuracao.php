<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = ['chave', 'valor', 'grupo'];

    private static ?array $cache = null;

    /**
     * Guarda todas as configurações em memória (por pedido) para leitura rápida.
     */
    public static function todas(): array
    {
        if (self::$cache === null) {
            self::$cache = static::pluck('valor', 'chave')->toArray();
        }

        return self::$cache;
    }

    public static function obter(string $chave, mixed $padrao = null): mixed
    {
        $todas = self::todas();

        return array_key_exists($chave, $todas) && $todas[$chave] !== null && $todas[$chave] !== ''
            ? $todas[$chave]
            : $padrao;
    }

    public static function booleano(string $chave, bool $padrao = false): bool
    {
        $valor = self::obter($chave);

        return $valor === null ? $padrao : in_array((string) $valor, ['1', 'true', 'on', 'sim'], true);
    }

    public static function limparCache(): void
    {
        self::$cache = null;
    }

    // Conveniências para as views (documentos e PDFs)

    public static function nome(): string
    {
        return (string) self::obter('escola.nome', 'MiScool');
    }

    public static function logotipo(): ?string
    {
        return self::obter('escola.logotipo', null);
    }

    public static function logotipoUrl(): ?string
    {
        return self::logotipo() ? asset('storage/' . self::logotipo()) : null;
    }

    public static function contacto(): array
    {
        return [
            'nome' => self::nome(),
            'morada' => self::obter('escola.endereco', '') ?: '—',
            'telefone' => self::obter('escola.contacto', '') ?: '—',
            'email' => self::obter('escola.email', '') ?: '—',
            'moeda' => self::obter('escola.moeda', 'Xof'),
            'ano_letivo' => self::obter('escola.ano_letivo', ''),
        ];
    }
}