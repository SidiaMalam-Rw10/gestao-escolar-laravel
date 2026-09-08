<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atividade extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'tipo', 'descricao', 'modelo', 'modelo_id', 'detalhes', 'ip', 'user_agent'];

    protected $casts = [
        'detalhes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Regista uma atividade (quem fez, o quê e quando).
     */
    public static function registar(string $tipo, string $descricao, ?\Illuminate\Contracts\Auth\Authenticatable $user = null, ?string $modelo = null, ?int $modeloId = null, array $detalhes = [])
    {
        try {
            return static::create([
                'user_id' => $user?->id ?? auth()->id(),
                'tipo' => $tipo,
                'descricao' => $descricao,
                'modelo' => $modelo,
                'modelo_id' => $modeloId,
                'detalhes' => $detalhes ?: null,
                'ip' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 500),
            ]);
        } catch (\Throwable $e) {
            return null;
        }
    }
}