<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presenca extends Model
{
    protected $fillable = [
        'user_id',
        'tipo',
        'mes',
        'ano',
        'presencas',
        'faltas',
        'justificadas',
        'tempo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTaxaAssiduidadeAttribute(): ?float
    {
        $total = $this->presencas + $this->faltas;

        return $total > 0 ? round(($this->presencas / $total) * 100, 1) : null;
    }
}
