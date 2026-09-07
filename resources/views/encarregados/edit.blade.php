@extends('layouts.app')

@section('title', 'Editar Encarregado')
@section('page-title', 'Editar Encarregado de Educação')

@section('content')
<style>
    .form-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:28px;max-width:820px;margin:0 auto}
    .form-section-title{font-size:14px;font-weight:600;color:var(--text-primary);border-bottom:1px solid var(--border-color);padding-bottom:12px;margin-bottom:20px}
    .form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:18px}
    .form-group{display:flex;flex-direction:column;gap:6px}
    .form-group.full-width{grid-column:1/-1}
    .form-label{font-size:12px;font-weight:500;color:var(--text-primary)}
    .form-label .required{color:#FCA5A5}
    .form-input,.form-select{width:100%;padding:10px 12px;background:var(--bg-input,#151D19);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s}
    .form-input:focus,.form-select:focus{outline:none;border-color:var(--accent-green)}
    .form-input::placeholder{color:var(--text-secondary)}
    .form-hint{font-size:11px;color:var(--text-secondary);margin-top:4px}
    .form-error{font-size:11px;color:#FCA5A5;margin-top:4px}
    .is-invalid{border-color:rgba(239,68,68,.5)!important}
    .form-actions{display:flex;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color)}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 20px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 20px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .alert-error{padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .checkbox-group{display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:var(--text-primary)}
    .checkbox-group input[type="checkbox"]{width:15px;height:15px;accent-color:var(--accent-green);margin:0}
    .acesso-box{display:none;margin-top:16px;padding:16px;background:rgba(59,130,246,.06);border:1px dashed rgba(59,130,246,.35);border-radius:8px}
    .acesso-box.open{display:block}
    .acesso-status{display:flex;align-items:center;gap:8px;font-size:13px;padding:10px 12px;border-radius:6px;margin-bottom:14px}
    .acesso-status.tem{background:rgba(34,197,94,.1);color:var(--accent-green)}
    .acesso-status.nao{background:rgba(107,114,128,.12);color:var(--text-secondary)}
    @media (max-width:768px){.form-grid{grid-template-columns:1fr}.form-group.full-width{grid-column:auto}}
</style>

@if($errors->any())
<div class="alert-error"><i class="fas fa-exclamation-circle"></i>{{ $errors->first() }}</div>
@endif

<form action="{{ route('admin.encarregados.update', $encarregado) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-card">
        <div class="form-section-title">Dados do Encarregado de Educação</div>
        <div class="form-grid">
            <div class="form-group full-width">
                <label class="form-label">Nome Completo <span class="required">*</span></label>
                <input type="text" name="nome" value="{{ old('nome', $encarregado->nome) }}" class="form-input @error('nome') is-invalid @enderror" placeholder="Ex: Joana Mendes" required>
                @error('nome')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Parentesco</label>
                <select name="parentesco" class="form-select @error('parentesco') is-invalid @enderror">
                    <option value="">Selecionar...</option>
                    @foreach(['Pai', 'Mãe', 'Avô', 'Avó', 'Tio', 'Tia', 'Irmão', 'Irmã', 'Outro'] as $p)
                    <option value="{{ $p }}" {{ old('parentesco', $encarregado->parentesco) === $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
                @error('parentesco')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Género</label>
                <select name="genero" class="form-select @error('genero') is-invalid @enderror">
                    <option value="">Selecionar...</option>
                    <option value="M" {{ old('genero', $encarregado->genero) === 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('genero', $encarregado->genero) === 'F' ? 'selected' : '' }}>Feminino</option>
                </select>
                @error('genero')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" value="{{ old('telefone', $encarregado->telefone) }}" class="form-input @error('telefone') is-invalid @enderror" placeholder="Ex: +245 955 000 000">
                @error('telefone')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $encarregado->email) }}" class="form-input @error('email') is-invalid @enderror" placeholder="exemplo@email.com">
                @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group full-width">
                <label class="form-label">Endereço</label>
                <input type="text" name="endereco" value="{{ old('endereco', $encarregado->endereco) }}" class="form-input @error('endereco') is-invalid @enderror" placeholder="Ex: Bairro de Quelele, Bissau">
                @error('endereco')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-section-title" style="margin-top:28px">Acesso ao Sistema</div>

        @if($encarregado->user)
        <div class="acesso-status tem"><i class="fas fa-check-circle"></i> Este encarregado já tem conta de acesso.</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Nome de Utilizador</label>
                <input type="text" name="username" value="{{ old('username', $encarregado->user->username) }}" class="form-input @error('username') is-invalid @enderror">
                @error('username')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Nova Palavra-passe</label>
                <input type="password" name="password" class="form-input @error('password') is-invalid @enderror" placeholder="Deixe vazio para manter">
                @error('password')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Confirmar Palavra-passe</label>
                <input type="password" name="password_confirmation" class="form-input" placeholder="Repita a nova palavra-passe">
            </div>
            <div class="form-group">
                <label class="checkbox-group" style="color:#FCA5A5">
                    <input type="checkbox" name="remover_acesso" id="remover_acesso" value="1">
                    <span>Remover acesso ao sistema</span>
                </label>
            </div>
        </div>
        @else
        <div class="acesso-status nao"><i class="fas fa-info-circle"></i> Este encarregado ainda não tem conta de acesso.</div>
        <div class="form-grid">
            <div class="form-group full-width">
                <label class="checkbox-group">
                    <input type="checkbox" name="criar_conta" id="criar_conta" value="1" {{ old('criar_conta') ? 'checked' : '' }} onchange="document.getElementById('acesso-box').classList.toggle('open', this.checked)">
                    <span>Criar conta de acesso para o encarregado</span>
                </label>
            </div>
        </div>
        <div class="acesso-box {{ old('criar_conta') ? 'open' : '' }}" id="acesso-box">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">Nome de Utilizador <span class="required">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}" class="form-input @error('username') is-invalid @enderror" placeholder="Ex: enc.joana">
                    @error('username')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Palavra-passe <span class="required">*</span></label>
                    <input type="password" name="password" class="form-input @error('password') is-invalid @enderror" placeholder="Mínimo 6 caracteres">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Confirmar Palavra-passe <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" class="form-input" placeholder="Repita a palavra-passe">
                </div>
            </div>
        </div>
        @endif

        <div class="form-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-check"></i> Atualizar</button>
            <a href="{{ route('admin.encarregados.show', $encarregado) }}" class="btn"><i class="fas fa-times"></i> Cancelar</a>
        </div>
    </div>
</form>
@endsection