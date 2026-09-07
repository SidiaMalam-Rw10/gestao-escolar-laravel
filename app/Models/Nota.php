<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nota extends Model
{
    protected $fillable = [
        'aluno_id',
        'disciplina',
        'tpi',
        'co',
        'tg',
        'media',
        'exame',
        'mg',
        'trimestre',
        'ano_lectivo',
    ];

    protected $casts = [
        'tpi' => 'decimal:2',
        'co' => 'decimal:2',
        'tg' => 'decimal:2',
        'media' => 'decimal:2',
        'exame' => 'decimal:2',
        'mg' => 'decimal:2',
    ];

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aluno_id');
    }
}
