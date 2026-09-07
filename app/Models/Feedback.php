<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'tipo',
        'assunto',
        'mensagem',
        'pagina',
        'estado',
        'resposta',
        'respondido_em',
    ];

    protected $casts = [
        'respondido_em' => 'datetime',
    ];

    public const TIPOS = [
        'feedback' => 'Feedback',
        'problema' => 'Reportar problema',
    ];

    public const ESTADOS = [
        'novo' => 'Novo',
        'em_analise' => 'Em análise',
        'resolvido' => 'Resolvido',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? ucfirst($this->tipo);
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }
}