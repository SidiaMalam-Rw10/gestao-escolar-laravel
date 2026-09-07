<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encarregado extends Model
{
    protected $fillable = [
        'user_id',
        'nome',
        'telefone',
        'email',
        'endereco',
        'genero',
        'parentesco',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function alunos()
    {
        return $this->hasMany(User::class, 'encarregado_id')->alunos();
    }
}
