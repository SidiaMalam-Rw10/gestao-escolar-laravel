<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nome', 'sigla', 'descricao', 'is_active'])]
class Departamento extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_departamento')
                    ->withPivot('cargo', 'regime', 'is_principal', 'ano_lectivo')
                    ->withTimestamps();
    }

    public function scopeAtivos($query)
    {
        return $query->where('is_active', true);
    }
}
