@extends('layouts.app')

@section('title', 'Nova Página')
@section('page-title', 'Nova Página')

@section('content')
<style>
    .form-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:28px;max-width:820px;margin:0 auto}
    .form-section-title{font-size:14px;font-weight:600;color:var(--text-primary);border-bottom:1px solid var(--border-color);padding-bottom:12px;margin-bottom:20px}
    .form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:18px}
    .form-group{display:flex;flex-direction:column;gap:6px}
    .form-group.full-width{grid-column:1/-1}
    .form-label{font-size:12px;font-weight:500;color:var(--text-primary)}
    .form-label .required{color:#FCA5A5}
    .form-input,.form-select,textarea.form-input{width:100%;padding:10px 12px;background:var(--bg-input,#151D19);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s;resize:vertical}
    .form-input:focus,.form-select:focus{outline:none;border-color:var(--accent-green)}
    .form-input::placeholder{color:var(--text-secondary)}
    .form-hint{font-size:11px;color:var(--text-secondary);margin-top:4px}
    .form-error{font-size:11px;color:#FCA5A5;margin-top:4px}
    .is-invalid{border-color:rgba(239,68,68,.5)!important}
    .form-actions{display:flex;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color)}
    .img-preview{margin-top:10px;max-width:260px;border-radius:8px;border:1px solid var(--border-color);display:none}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 20px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 20px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .alert-error{padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    @media (max-width:768px){.form-grid{grid-template-columns:1fr}.form-group.full-width{grid-column:auto}}
</style>

@if($errors->any())
<div class="alert-error"><i class="fas fa-exclamation-circle"></i>{{ $errors->first() }}</div>
@endif

<form action="{{ route('admin.paginas.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-card">
        <div class="form-section-title">Nova Página</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Secção <span class="required">*</span></label>
                <select name="tipo" class="form-select @error('tipo') is-invalid @enderror">
                    <option value="">Selecionar...</option>
                    @foreach(App\Models\Pagina::TIPOS as $tipo => $label)
                    <option value="{{ $tipo }}" {{ old('tipo') === $tipo ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('tipo')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Ordem de exibição</label>
                <input type="number" name="ordem" min="0" value="{{ old('ordem', 0) }}" class="form-input @error('ordem') is-invalid @enderror">
                <div class="form-hint">Números mais baixos aparecem primeiro.</div>
                @error('ordem')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group full-width">
                <label class="form-label">Título <span class="required">*</span></label>
                <input type="text" name="titulo" value="{{ old('titulo') }}" class="form-input @error('titulo') is-invalid @enderror" placeholder="Ex: Horário de Funcionamento da Escola">
                @error('titulo')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group full-width">
                <label class="form-label">Conteúdo <span class="required">*</span></label>
                <textarea name="conteudo" rows="8" class="form-input @error('conteudo') is-invalid @enderror" placeholder="Escreva o conteúdo da página...">{{ old('conteudo') }}</textarea>
                @error('conteudo')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Imagem (opcional)</label>
                <input type="file" name="imagem" id="imagem" accept="image/*" class="form-input @error('imagem') is-invalid @enderror" onchange="previewImagem(this)">
                <div class="form-hint">JPG, PNG, GIF ou WEBP. Máx. {{ ini_get('upload_max_filesize') }}.</div>
                @error('imagem')<div class="form-error">{{ $message }}</div>@enderror
                <img id="img-preview" class="img-preview" alt="Pré-visualização">
            </div>

            <div class="form-group">
                <label class="form-label">Vídeo (ficheiro, opcional)</label>
                <input type="file" name="video" accept="video/*" class="form-input @error('video') is-invalid @enderror">
                <div class="form-hint">MP4, WEBM, OGG ou MOV. Máx. {{ ini_get('upload_max_filesize') }}.</div>
                @error('video')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Link de Vídeo (opcional)</label>
                <input type="url" name="video_url" value="{{ old('video_url') }}" class="form-input @error('video_url') is-invalid @enderror" placeholder="https://youtube.com/watch?v=... ou https://vimeo.com/...">
                <div class="form-hint">Em alternativa ao ficheiro, pode colar um link do YouTube ou Vimeo.</div>
                @error('video_url')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Guardar</button>
            <a href="{{ route('admin.paginas.index') }}" class="btn"><i class="fas fa-times"></i> Cancelar</a>
        </div>
    </div>
</form>

<script>
    function previewImagem(input) {
        const preview = document.getElementById('img-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }
</script>
@endsection