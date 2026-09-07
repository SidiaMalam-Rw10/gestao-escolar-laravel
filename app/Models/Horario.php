<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Horario extends Model
{
    protected $fillable = [
        'turma_id',
        'dia_semana',
        'hora_inicio',
        'hora_fim',
        'disciplina',
        'professor_id',
        'sala',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fim' => 'datetime:H:i',
    ];

    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_id');
    }
}
