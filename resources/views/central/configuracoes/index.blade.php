@extends('layouts.app')

@section('title', 'Configuração da plataforma')
@section('page-title', 'Painel MiScool — Configuração')

@section('content')
<style>
    .page-title{font-size:20px;font-weight:700;margin-bottom:20px}
    .form-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:28px;max-width:760px}
    .form-section-title{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:var(--text-secondary);margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border-color)}
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px}
    .form-group{margin-bottom:0}
    .form-label{display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:var(--text-primary)}
    .form-label .required{color:#FCA5A5}
    .form-hint{font-size:11px;color:var(--text-secondary);margin-top:4px}
    .form-error{font-size:11px;color:#FCA5A5;margin-top:4px}
    .form-input{width:100%;padding:10px 14px;background:var(--bg-input,#151D19);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s;box-sizing:border-box}
    .form-input:focus{outline:none;border-color:var(--accent-green)}
    .form-input::placeholder{color:var(--text-secondary)}
    .form-input.is-invalid{border-color:#FCA5A5 !important}
    .input-icon{position:relative}
    .input-icon .fa{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--text-secondary);font-size:12px;pointer-events:none}
    .input-icon .form-input{padding-left:36px}
    .form-actions{display:flex;gap:12px;padding-top:16px;border-top:1px solid var(--border-color)}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 24px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .cfg-logo{display:flex;align-items:center;gap:18px;flex-wrap:wrap;margin-bottom:26px}
    .cfg-logo img{width:120px;height:120px;object-fit:contain;background:var(--bg-input,#151D19);border:1px solid var(--border-color);border-radius:10px;padding:10px}
    .file-pick{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 16px;border-radius:6px;cursor:pointer;font-size:12px;display:inline-flex;align-items:center;gap:7px;transition:all .15s}
    .file-pick:hover{border-color:rgba(255,255,255,.18);background:var(--bg-hover)}
    .file-remove{background:transparent;border:1px solid rgba(239,68,68,.25);color:#FCA5A5;padding:10px 16px;border-radius:6px;cursor:pointer;font-size:12px;display:inline-flex;align-items:center;gap:7px;transition:all .15s;text-decoration:none}
    .file-remove:hover{background:rgba(239,68,68,.1)}
    .cfg-logo-hint{font-size:11px;color:var(--text-secondary);margin-top:6px;width:100%}
    @media(max-width:768px){.form-grid{grid-template-columns:1fr}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif

<div class="page-title">Configuração da plataforma</div>

<form method="POST" action="{{ route('central.configuracoes.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-card">
        <div class="form-section-title">Logotipo da Plataforma</div>
        <div class="cfg-logo">
            <img src="{{ $valores['logotipo'] ? asset('storage/' . $valores['logotipo']) : asset('logo.png') }}" alt="Logotipo da plataforma" id="cfg-logo-preview">
            <div>
                <label class="file-pick" for="cfg-logotipo-input"><i class="fas fa-upload"></i> Carregar logotipo</label>
                <input type="file" name="logotipo" id="cfg-logotipo-input" style="display:none" accept="image/jpeg,image/png,image/webp">
                <div style="margin-top:8px">
                    @if($valores['logotipo'])
                    <label class="file-remove" style="cursor:pointer"><i class="fas fa-trash"></i> Remover logotipo<input type="checkbox" name="remover_logotipo" value="1" id="cfg-logotipo-remove" style="display:none"></label>
                    @endif
                </div>
                <div class="cfg-logo-hint">Formatos aceites: JPG, PNG ou WebP. Tamanho máximo de 5&nbsp;MB.</div>
                @error('logotipo')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-section-title">Imagem de Fundo</div>
        <div class="cfg-logo">
            <img src="{{ $valores['fundo'] ? asset('storage/' . $valores['fundo']) : asset('logo.png') }}" alt="Imagem de fundo da plataforma" id="cfg-fundo-preview" style="width:220px;height:110px;object-fit:cover">
            <div>
                <label class="file-pick" for="cfg-fundo-input"><i class="fas fa-upload"></i> Carregar imagem de fundo</label>
                <input type="file" name="fundo" id="cfg-fundo-input" style="display:none" accept="image/jpeg,image/png,image/webp">
                <div style="margin-top:8px">
                    @if($valores['fundo'])
                    <label class="file-remove" style="cursor:pointer"><i class="fas fa-trash"></i> Remover imagem de fundo<input type="checkbox" name="remover_fundo" value="1" id="cfg-fundo-remove" style="display:none"></label>
                    @endif
                </div>
                <div class="cfg-logo-hint">Carregar imagem de fundo.</div>
                @error('fundo')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-section-title">Identificação da Plataforma</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Nome da plataforma <span class="required">*</span></label>
                <div class="input-icon">
                    <i class="fas fa-asterisk"></i>
                    <input type="text" name="nome" value="{{ old('nome', $valores['nome']) }}" class="form-input @error('nome') is-invalid @enderror" required maxlength="150">
                </div>
                @error('nome')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email de contacto</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" value="{{ old('email', $valores['email']) }}" class="form-input @error('email') is-invalid @enderror" maxlength="150">
                </div>
                @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Telefone de contacto</label>
                <div class="input-icon">
                    <i class="fas fa-phone"></i>
                    <input type="text" name="telefone" value="{{ old('telefone', $valores['telefone']) }}" class="form-input @error('telefone') is-invalid @enderror" maxlength="30">
                </div>
                @error('telefone')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Endereço / Morada</label>
                <div class="input-icon">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="endereco" value="{{ old('endereco', $valores['endereco']) }}" class="form-input @error('endereco') is-invalid @enderror" maxlength="200">
                </div>
                @error('endereco')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Guardar configuração</button>
        </div>
    </div>
</form>

<script>
    const logoInput = document.getElementById('cfg-logotipo-input');
    const logoPreview = document.getElementById('cfg-logo-preview');
    const logoRemove = document.getElementById('cfg-logotipo-remove');
    const fundoInput = document.getElementById('cfg-fundo-input');
    const fundoPreview = document.getElementById('cfg-fundo-preview');
    const fundoRemove = document.getElementById('cfg-fundo-remove');

    if (logoInput) {
        logoInput.addEventListener('change', function () {
            if (logoInput.files && logoInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) { logoPreview.src = e.target.result; };
                reader.readAsDataURL(logoInput.files[0]);
                if (logoRemove) logoRemove.checked = false;
            }
        });
    }
    if (logoRemove) {
        logoRemove.addEventListener('change', function () {
            if (logoRemove.checked) {
                logoPreview.src = '{!! asset('logo.png') !!}';
                logoInput.value = '';
            }
        });
    }
    if (fundoInput) {
        fundoInput.addEventListener('change', function () {
            if (fundoInput.files && fundoInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) { fundoPreview.src = e.target.result; };
                reader.readAsDataURL(fundoInput.files[0]);
                if (fundoRemove) fundoRemove.checked = false;
            }
        });
    }
    if (fundoRemove) {
        fundoRemove.addEventListener('change', function () {
            if (fundoRemove.checked) {
                fundoPreview.src = '{!! asset('logo.png') !!}';
                fundoInput.value = '';
            }
        });
    }
</script>
@endsection