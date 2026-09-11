<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    protected $fillable = [
        'aluno_id',
        'mes',
        'quantidade_meses',
        'mes_fim',
        'ano_fim',
        'ano',
        'valor',
        'data_pagamento',
        'status',
        'metodo_pagamento',
        'observacoes',
        'recibo_numero',
        'registrado_por',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_pagamento' => 'datetime',
        'quantidade_meses' => 'integer',
        'mes_fim' => 'integer',
        'ano_fim' => 'integer',
    ];

    public function getMesesArrayAttribute(): array
    {
        $quantidade = $this->quantidade_meses ?: 1;

        if ($quantidade <= 1) {
            return [['mes' => $this->mes, 'ano' => $this->ano]];
        }

        $mes = $this->mes;
        $ano = $this->ano;
        $lista = [];

        while (count($lista) < $quantidade) {
            $lista[] = ['mes' => $mes, 'ano' => $ano];
            $mes++;
            if ($mes > 12) {
                $mes = 1;
                $ano++;
            }
        }

        return $lista;
    }

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aluno_id');
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pago' => 'Pago',
            'atrasado' => 'Atrasado',
            default => 'Pendente',
        };
    }
}
