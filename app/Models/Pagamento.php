<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    protected $fillable = [
        'aluno_id',
        'mes',
        'ano',
        'valor',
        'data_pagamento',
        'status',
        'metodo_pagamento',
        'observacoes',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_pagamento' => 'datetime',
    ];

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aluno_id');
    }
}
