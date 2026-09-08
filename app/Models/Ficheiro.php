<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ficheiro extends Model
{
    protected $fillable = ['titulo', 'descricao', 'categoria', 'caminho', 'nome_original', 'extensao', 'tamanho', 'user_id'];

    public const CATEGORIAS = [
        'documento' => 'Documento',
        'livro' => 'Livro',
        'manual' => 'Manual',
        'outro' => 'Outro',
    ];

    public static function categoriaLabel(string $categoria): string
    {
        return self::CATEGORIAS[$categoria] ?? ucfirst($categoria);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tamanhoFormatado(): string
    {
        $bytes = (int) $this->tamanho;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    public function icone(): string
    {
        return match (strtolower($this->extensao)) {
            'pdf' => 'far fa-file-pdf',
            'doc', 'docx' => 'far fa-file-word',
            'xls', 'xlsx' => 'far fa-file-excel',
            'ppt', 'pptx' => 'far fa-file-powerpoint',
            'zip', 'rar' => 'far fa-file-archive',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'far fa-file-image',
            'epub' => 'fas fa-book-open',
            default => 'far fa-file',
        };
    }
}