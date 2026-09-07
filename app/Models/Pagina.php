<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagina extends Model
{
    public const TIPOS = [
        'horario' => 'Horário',
        'atividades' => 'Atividades da Escola',
        'sobre' => 'Sobre a Escola',
    ];

    protected $fillable = [
        'tipo',
        'titulo',
        'conteudo',
        'imagem',
        'video_url',
        'video_path',
        'ordem',
        'criado_por',
    ];

    protected $casts = [
        'ordem' => 'integer',
    ];

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function tipoLabel(): string
    {
        return self::TIPOS[$this->tipo] ?? ucfirst($this->tipo);
    }

    public function videoEmbedUrl(): ?string
    {
        $url = $this->video_url;
        if (!$url) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]+)/', $url, $m)) {
            return "https://www.youtube.com/embed/{$m[1]}";
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            return "https://player.vimeo.com/video/{$m[1]}";
        }

        return null;
    }

    public function hasVideo(): bool
    {
        return $this->videoEmbedUrl() !== null || $this->video_path !== null;
    }

    public function videoFonte(): ?string
    {
        if ($this->videoEmbedUrl()) {
            return $this->videoEmbedUrl();
        }

        if ($this->video_path) {
            return asset('storage/' . $this->video_path);
        }

        return null;
    }

    public function scopeDoTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo)->orderBy('ordem')->orderBy('id');
    }
}