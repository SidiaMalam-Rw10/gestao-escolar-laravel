<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mensagem extends Model
{
    protected $table = 'mensagens';

    protected $fillable = ['remetente_id', 'assunto', 'corpo'];

    public function remetente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remetente_id');
    }

    public function destinatarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'mensagem_destinatarios', 'mensagem_id', 'destinatario_id')
            ->withPivot('lida', 'lida_em')
            ->withTimestamps();
    }

    public function foiLidaPor(User $user): bool
    {
        return $this->destinatarios()
            ->wherePivot('destinatario_id', $user->id)
            ->wherePivot('lida', true)
            ->exists();
    }
}