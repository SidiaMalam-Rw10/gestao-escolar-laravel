@extends('layouts.app')

@section('title', 'Carregar Ficheiro')
@section('page-title', 'Carregar Ficheiro na Biblioteca')

@section('content')
<style>
    .form-card{max-width:680px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:24px}
    .form-group{margin-bottom:18px}
    .form-label{display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:var(--text-secondary)}
    .form-input,.form-select,.form-textarea{width:100%;padding:10px 14px;background:var(--bg-input,var(--bg-card));border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s}
    .form-input:focus,.form-select:focus,.form-textarea:focus{outline:none;border-color:var(--accent-green)}
    .form-textarea{min-height:110px;resize:vertical}
    .form-hint{font-size:11px;color:var(--text-secondary);margin-top:5px}
    .form-error{color:#FCA5A5;font-size:11px;margin-top:4px}
    .area-envio{border:2px dashed var(--border-color);border-radius:8px;padding:28px;text-align:center;cursor:pointer;transition:all .15s;background:var(--bg-hover)}
    .area-envio:hover{border-color:var(--accent-green)}
    .area-envio.dragging{border-color:var(--accent-green);background:rgba(34,197,94,.06)}
    .area-envio i{font-size:28px;color:var(--text-secondary);margin-bottom:8px;display:block}
    .area-envio.nome-carregado i{color:var(--accent-green)}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
</style>

@if($errors->any())
<div class="alert-danger" style="padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;border-radius:8px;margin-bottom:20px;font-size:13px">
    @foreach($errors->all() as $erro)<div>{{ $erro }}</div>@endforeach
</div>
@endif

<div class="form-card">
    <form method="POST" action="{{ route('admin.ficheiros.store') }}" enctype="multipart/form-data" id="form-ficheiro">
        @csrf

        <div class="form-group">
            <label class="form-label">Ficheiro <span style="color:#FCA5A5">*</span></label>
            <label class="area-envio" id="area-envio" for="ficheiro-input">
                <i class="fas fa-cloud-upload-alt" id="icone-envio"></i>
                <div style="font-size:13px;font-weight:600" id="texto-envio">Clique para escolher um ficheiro</div>
                <div style="font-size:11px;color:var(--text-secondary);margin-top:4px">PDF, Word, Excel, PowerPoint, EPUB, TXT, imagens e ZIP · até 20 MB</div>
            </label>
            <input type="file" name="ficheiro" id="ficheiro-input" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.odt,.ods,.epub,.rtf,.jpg,.jpeg,.png,.webp,.zip" style="display:none" required>
            @error('ficheiro')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" value="{{ old('titulo') }}" class="form-input" placeholder="Ex: Manual de Matemática - 10ª Classe" required>
            @error('titulo')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Categoria</label>
            <select name="categoria" class="form-select">
                @foreach(\App\Models\Ficheiro::CATEGORIAS as $categoria => $legenda)
                <option value="{{ $categoria }}" {{ old('categoria', 'documento') === $categoria ? 'selected' : '' }}>{{ $legenda }}</option>
                @endforeach
            </select>
            @error('categoria')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Descrição <span style="font-weight:400">(opcional)</span></label>
            <textarea name="descricao" class="form-textarea" placeholder="Breve descrição do conteúdo...">{{ old('descricao') }}</textarea>
            @error('descricao')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div style="display:flex;gap:10px">
            <button type="submit" class="btn-primary"><i class="fas fa-upload"></i> Carregar</button>
            <a href="{{ route('ficheiros.index') }}" class="btn"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </form>
</div>

<script>
    const input = document.getElementById('ficheiro-input');
    const area = document.getElementById('area-envio');
    const texto = document.getElementById('texto-envio');
    const icone = document.getElementById('icone-envio');

    function mostrarNome() {
        if (input.files.length > 0) {
            texto.textContent = input.files[0].name;
            icone.classList.remove('fas', 'fa-cloud-upload-alt');
            icone.classList.add('far', 'fa-file');
            area.classList.add('nome-carregado');
        }
    }
    input.addEventListener('change', mostrarNome);
    ['dragenter', 'dragover'].forEach(e => area.addEventListener(e, (ev) => { ev.preventDefault(); area.classList.add('dragging'); }));
    ['dragleave', 'drop'].forEach(e => area.addEventListener(e, (ev) => { ev.preventDefault(); area.classList.remove('dragging'); }));
    area.addEventListener('drop', (ev) => {
        if (ev.dataTransfer.files.length > 0) {
            input.files = ev.dataTransfer.files;
            mostrarNome();
        }
    });
</script>
@endsection