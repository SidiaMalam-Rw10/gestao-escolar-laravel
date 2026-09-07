<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresencaAlunoMarcacao extends Model
{
    protected $table = 'presenca_aluno_marcacoes';

    protected $fillable = [
        'aluno_id',
        'professor_id',
        'turma_id',
        'estado',
        'data',
        'hora',
        'mes',
        'ano',
        'observacao',
    ];

    public const ESTADOS = [
        'presente' => 'Presente',
        'falta' => 'Falta',
        'justificada' => 'Falta Justificada',
    ];

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aluno_id');
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }
}