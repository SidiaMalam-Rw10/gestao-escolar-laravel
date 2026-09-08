@extends('layouts.app')

@section('title', 'O meu Perfil')
@section('page-title', 'O meu Perfil')

@section('content')
<style>
    .pf-wrap{display:grid;grid-template-columns:340px 1fr;gap:20px;align-items:start}
    .pf-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:24px}
    .pf-avatar-box{display:flex;flex-direction:column;align-items:center;gap:14px;text-align:center}
    .pf-avatar{width:120px;height:120px;border-radius:50%;background:var(--accent-green);color:#000;display:grid;place-items:center;font-weight:800;font-size:44px;overflow:hidden;border:3px solid var(--border-color)}
    .pf-avatar img{width:100%;height:100%;object-fit:cover}
    .pf-nome{font-size:15px;font-weight:700}
    .pf-funcao{font-size:12px;color:var(--text-secondary)}
    .pf-roles{display:flex;flex-wrap:wrap;gap:6px;justify-content:center;margin-top:4px}
    .pf-card h3{font-size:14px;font-weight:700;margin:0 0 16px;display:flex;align-items:center;gap:8px}
    .form-group{margin-bottom:16px}
    .form-label{display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:var(--text-secondary)}
    .form-input,.form-select,.form-textarea{width:100%;padding:10px 14px;background:var(--bg-input,var(--bg-card));border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s}
    .form-input:focus,.form-select:focus,.form-textarea:focus{outline:none;border-color:var(--accent-green)}
    .form-error{color:#FCA5A5;font-size:11px;margin-top:4px}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn-primary:hover{background:#1ea34e}
    .btn-danger{background:transparent;border:1px solid rgba(239,68,68,.5);color:#FCA5A5;padding:8px 14px;border-radius:6px;font-size:12px;cursor:pointer;transition:all .15s;text-decoration:none;display:inline-flex;align-items:center;gap:6px}
    .btn-danger:hover{background:rgba(239,68,68,.12)}
    .avatar-actions{display:flex;gap:8px;flex-wrap:wrap;justify-content:center}
    .file-pick{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:8px 14px;border-radius:6px;font-size:12px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:all .15s}
    .file-pick:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .pf-file-hidden{display:none}
    .pf-divider{border:none;border-top:1px solid var(--border-color);margin:22px 0}
    @media(max-width:920px){.pf-wrap{grid-template-columns:1fr}}
</style>

<div class="pf-wrap">
    {{-- Coluna do avatar --}}
    <aside class="pf-card">
        <div class="pf-avatar-box">
            <div class="pf-avatar" id="pf-avatar">
                @if(auth()->user()->fotoUrl())
                <img id="pf-avatar-img" src="{{ auth()->user()->fotoUrl() }}" alt="{{ auth()->user()->name }}">
                @else
                <span id="pf-avatar-inicial">{{ auth()->user()->inicial() }}</span>
                @endif
            </div>
            <div>
                <div class="pf-nome">{{ auth()->user()->name }}</div>
                <div class="pf-funcao">{{ auth()->user()->username }}@if(auth()->user()->email) · {{ auth()->user()->email }}@endif</div>
            </div>
            <div class="pf-roles">
                @foreach(array_merge([auth()->user()->role], auth()->user()->roles ?? []) as $r)
                <span class="tag tag-{{ $r }}">{{ ucfirst($r) }}</span>
                @endforeach
            </div>
            <form id="pf-foto-form" method="POST" action="{{ route('perfil.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="name" value="{{ auth()->user()->name }}">
                <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                <input type="hidden" name="telefone" value="{{ auth()->user()->telefone }}">
                <input type="file" name="foto" id="pf-foto-input" class="pf-file-hidden" accept="image/jpeg,image/png,image/webp">
                <div class="avatar-actions">
                    <label for="pf-foto-input" class="file-pick"><i class="fas fa-camera"></i> Trocar foto</label>
                    @if(auth()->user()->foto)
                    <button type="button" class="btn-danger" onclick="event.preventDefault(); if(confirm('Remover a foto de perfil?')){document.getElementById('pf-remover-foto').submit();}"><i class="fas fa-trash"></i> Remover</button>
                    @endif
                </div>
                <div style="display:none" id="pf-foto-preview-wrap"><img id="pf-foto-preview" src="" alt=""></div>
            </form>
            @if(auth()->user()->foto)
            <form id="pf-remover-foto" method="POST" action="{{ route('perfil.foto.remover') }}" style="display:none">@csrf @method('DELETE')</form>
            @endif
        </div>
    </aside>

    {{-- Coluna dos formulários --}}
    <div style="display:grid;gap:20px">
        <section class="pf-card">
            <h3><i class="fas fa-user-edit"></i> Informações pessoais</h3>
            <form method="POST" action="{{ route('perfil.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label" for="pf-name">Nome completo</label>
                    <input type="text" id="pf-name" name="name" class="form-input" value="{{ old('name', auth()->user()->name) }}" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="pf-email">E-mail</label>
                    <input type="email" id="pf-email" name="email" class="form-input" value="{{ old('email', auth()->user()->email) }}">
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="pf-telefone">Telefone</label>
                    <input type="text" id="pf-telefone" name="telefone" class="form-input" value="{{ old('telefone', auth()->user()->telefone) }}">
                    @error('telefone')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Guardar alterações</button>
            </form>
        </section>

        <section class="pf-card">
            <h3><i class="fas fa-shield-alt"></i> Segurança</h3>
            <form method="POST" action="{{ route('perfil.password') }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label" for="pf-pw-atual">Palavra-passe atual</label>
                    <input type="password" id="pf-pw-atual" name="password_atual" class="form-input" required autocomplete="current-password">
                    @error('password_atual')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="pf-pw-nova">Nova palavra-passe</label>
                    <input type="password" id="pf-pw-nova" name="password" class="form-input" required autocomplete="new-password">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="pf-pw-conf">Confirmar nova palavra-passe</label>
                    <input type="password" id="pf-pw-conf" name="password_confirmation" class="form-input" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn-primary"><i class="fas fa-key"></i> Alterar palavra-passe</button>
            </form>
        </section>
    </div>
</div>

<script>
    const fotoInput = document.getElementById('pf-foto-input');
    if (fotoInput) {
        fotoInput.addEventListener('change', function () {
            const f = this.files && this.files[0];
            if (!f) return;
            const url = URL.createObjectURL(f);
            const wrap = document.getElementById('pf-foto-preview-wrap');
            const preview = document.getElementById('pf-foto-preview');
            const avatar = document.getElementById('pf-avatar');
            const imgExistente = document.getElementById('pf-avatar-img');
            if (imgExistente) {
                imgExistente.src = url;
            } else {
                const img = document.createElement('img');
                img.id = 'pf-avatar-img';
                img.src = url;
                avatar.innerHTML = '';
                avatar.appendChild(img);
            }
            document.getElementById('pf-foto-form').submit();
        });
    }
</script>
@endsection