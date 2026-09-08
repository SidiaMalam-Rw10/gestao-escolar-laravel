<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turma extends Model
{
    protected $fillable = [
        'nome_turma',
        'nivel',
        'periodo',
        'ano_lectivo',
        'professor_responsavel_id',
        'capacidade',
        'propina_mensal',
        'meses_pagamento',
    ];

    protected $casts = [
        'propina_mensal' => 'decimal:2',
        'meses_pagamento' => 'integer',
    ];

    public function getMesesPagamentoAttribute($value): int
    {
        return (int) ($value ?: 12);
    }

    public function getPropinaAnualAttribute(): float
    {
        return (float) $this->propina_mensal * $this->meses_pagamento;
    }

    public function professorResponsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_responsavel_id');
    }

    public function alunos(): HasMany
    {
        return $this->hasMany(User::class, 'turma_id')->alunos();
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }
}
