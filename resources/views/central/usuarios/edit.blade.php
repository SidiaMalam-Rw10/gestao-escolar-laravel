@extends('layouts.app')

@section('title', 'Editar utilizador da plataforma')
@section('page-title', 'Painel ' . \App\Models\Configuracao::plataformaNome() . ' — Editar Utilizador')

@section('content')
<style>
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
    .checkbox-group{display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--text-primary);padding:10px 12px;background:var(--bg-input,#151D19);border:1px solid var(--border-color);border-radius:6px;transition:border-color .15s;width:fit-content}
    .checkbox-group:hover{border-color:rgba(255,255,255,.15)}
    .checkbox-group input[type="checkbox"]{width:15px;height:15px;accent-color:var(--accent-green);margin:0;flex-shrink:0}
    .form-actions{display:flex;gap:12px;padding-top:16px;border-top:1px solid var(--border-color)}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 24px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .btn-danger{background:transparent;border:1px solid rgba(239,68,68,.3);color:#FCA5A5;padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn-danger:hover{background:rgba(239,68,68,.1)}
    .alert-error{padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    @media(max-width:768px){.form-grid{grid-template-columns:1fr}}
</style>

@if($errors->any())
<div class="alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <span>Corrija os erros abaixo para continuar.</span>
</div>
@endif

<div class="form-card">
    <form method="POST" action="{{ route('central.usuarios.update', $usuario) }}">
        @csrf
        @method('PUT')

        <div class="form-section-title">Dados da Conta</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Nome completo <span class="required">*</span></label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" name="name" value="{{ old('name', $usuario->name) }}" class="form-input @error('name') is-invalid @enderror" required maxlength="150" autofocus>
                </div>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nome de utilizador <span class="required">*</span></label>
                <div class="input-icon">
                    <i class="fas fa-at"></i>
                    <input type="text" name="username" value="{{ old('username', $usuario->username) }}" class="form-input @error('username') is-invalid @enderror" required maxlength="50">
                </div>
                @error('username')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}" class="form-input @error('email') is-invalid @enderror" maxlength="150">
                </div>
                @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Número de telefone</label>
                <div class="input-icon">
                    <i class="fas fa-phone"></i>
                    <input type="text" name="telefone" value="{{ old('telefone', $usuario->telefone) }}" class="form-input @error('telefone') is-invalid @enderror" maxlength="30">
                </div>
                @error('telefone')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-section-title">Credenciais de Acesso</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Nova palavra-passe</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" class="form-input @error('password') is-invalid @enderror" minlength="8" autocomplete="new-password">
                </div>
                <div class="form-hint">Deixe em branco para manter a atual.</div>
                @error('password')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirmar nova palavra-passe</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password_confirmation" class="form-input" minlength="8" autocomplete="new-password">
                </div>
            </div>
        </div>

        <div class="form-section-title">Acesso ao Sistema</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="checkbox-group" style="gap:8px">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $usuario->is_active) ? 'checked' : '' }}>
                    <span>Conta ativa (pode iniciar sessão no Painel No Skola)</span>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Guardar</button>
            <a href="{{ route('central.usuarios.show', $usuario) }}" class="btn"><i class="fas fa-eye"></i> Ver perfil</a>
            <a href="{{ route('central.usuarios.index') }}" class="btn"><i class="fas fa-arrow-left"></i> Cancelar</a>
        </div>
    </form>
</div>
@endsection