<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Escola extends Model
{
    protected $fillable = [
        'nome',
        'slug',
        'nome_bd',
        'contacto',
        'email',
        'endereco',
        'admin_nome',
        'admin_username',
        'ativa',
    ];

    protected function casts(): array
    {
        return [
            'ativa' => 'boolean',
        ];
    }
}