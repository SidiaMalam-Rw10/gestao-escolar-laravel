<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aviso extends Model
{
    protected $fillable = [
        'titulo',
        'mensagem',
        'remetente_id',
        'destinatario_tipo',
        'destinatario_id',
        'turma_id',
        'lido',
        'lido_em',
    ];

    protected $casts = [
        'lido' => 'boolean',
        'lido_em' => 'datetime',
    ];

    public function remetente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remetente_id');
    }

    public function destinatario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }

    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
    }

    public function lidosPor()
    {
        return $this->belongsToMany(User::class, 'aviso_user')->withPivot('lido', 'lido_em')->withTimestamps();
    }

    public function foiLidoPor(User $user): bool
    {
        return $this->lidosPor()->where('user_id', $user->id)->exists();
    }
}
