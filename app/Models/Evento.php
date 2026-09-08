<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evento extends Model
{
    protected $fillable = ['titulo', 'descricao', 'tipo', 'data_inicio', 'data_fim', 'local', 'cor', 'user_id'];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
    ];

    public const TIPOS = [
        'reuniao' => 'Reunião',
        'ferias' => 'Férias',
        'feriado' => 'Feriado',
        'prova' => 'Provas',
        'atividade' => 'Atividade',
        'outro' => 'Outro',
    ];

    public const CORES = [
        'reuniao' => '#3B82F6',
        'ferias' => '#22C55E',
        'feriado' => '#9CA3AF',
        'prova' => '#F59E0B',
        'atividade' => '#EAB308',
        'outro' => '#A855F7',
    ];

    public static function tipoLabel(string $tipo): string
    {
        return self::TIPOS[$tipo] ?? ucfirst($tipo);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function corEfetiva(): string
    {
        return $this->cor ?? self::CORES[$this->tipo] ?? '#6B7280';
    }

    public function multidiario(): bool
    {
        return $this->data_fim !== null
            && $this->data_fim->toDateString() !== $this->data_inicio->toDateString();
    }
}