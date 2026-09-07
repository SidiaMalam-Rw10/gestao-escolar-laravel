<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contato extends Model
{
    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'mensagem',
        'lido',
        'lido_em',
    ];

    protected $casts = [
        'lido' => 'boolean',
        'lido_em' => 'datetime',
    ];
}
