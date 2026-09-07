<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresencaMarcacao extends Model
{
    protected $table = 'presenca_marcacoes';

    protected $fillable = [
        'user_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }
}