@extends('layouts.app')

@section('title', 'Novo Aluno')
@section('page-title', 'Criar Aluno')

@section('content')
<style>
    .form-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:28px;max-width:720px}
    .form-section-title{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:var(--text-secondary);margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border-color)}
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px}
    .form-group{margin-bottom:0}.form-group.full-width{grid-column:span 2}
    .form-label{display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:var(--text-primary)}
    .form-label .required{color:#FCA5A5}
    .form-input,.form-select{width:100%;padding:10px 14px;background:var(--bg-input,#151D19);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s}
    .form-input:focus,.form-select:focus{outline:none;border-color:var(--accent-green)}
    .form-input::placeholder{color:var(--text-secondary)}
    .form-error{font-size:11px;color:#FCA5A5;margin-top:4px}
    .is-invalid{border-color:#FCA5A5 !important}
    .form-actions{display:flex;gap:12px;padding-top:16px;border-top:1px solid var(--border-color)}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 24px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .alert-error{padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    @media(max-width:768px){.form-grid{grid-template-columns:1fr}.form-group.full-width{grid-column:span 1}}
</style>

@if($errors->any())<div class="alert-error"><i class="fas fa-exclamation-circle"></i><span>Corrija os erros abaixo.</span></div>@endif

<div class="form-card">
    <form method="POST" action="{{ route('admin.alunos.store') }}">
        @csrf
        <div class="form-section-title">Dados Pessoais</div>
        <div class="form-grid">
            <div class="form-group"><label class="form-label">Nome Completo <span class="required">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-input @error('name') is-invalid @enderror" required autofocus>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label class="form-label">Username <span class="required">*</span></label>
                <input type="text" name="username" value="{{ old('username') }}" class="form-input @error('username') is-invalid @enderror" required>
                @error('username')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-input @error('email') is-invalid @enderror">
                @error('email')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label class="form-label">Número</label>
                <input type="text" name="numero" value="{{ old('numero') }}" class="form-input @error('numero') is-invalid @enderror">
                @error('numero')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label class="form-label">Telefone</label>
                <input type="text" name="telefone" value="{{ old('telefone') }}" class="form-input @error('telefone') is-invalid @enderror">
                @error('telefone')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label class="form-label">Género</label>
                <select name="genero" class="form-select @error('genero') is-invalid @enderror">
                    <option value="">Selecionar...</option>
                    <option value="M" {{ old('genero') === 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('genero') === 'F' ? 'selected' : '' }}>Feminino</option>
                </select>
                @error('genero')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-group full-width"><label class="form-label">Endereço</label>
                <input type="text" name="endereco" value="{{ old('endereco') }}" class="form-input @error('endereco') is-invalid @enderror">
                @error('endereco')<div class="form-error">{{ $message }}</div>@enderror</div>
        </div>

        <div class="form-section-title">Credenciais de Acesso</div>
        <div class="form-grid">
            <div class="form-group"><label class="form-label">Password <span class="required">*</span></label>
                <input type="password" name="password" class="form-input @error('password') is-invalid @enderror" required>
                @error('password')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label class="form-label">Confirmar Password <span class="required">*</span></label>
                <input type="password" name="password_confirmation" class="form-input" required></div>
        </div>

        <div class="form-section-title">Dados Académicos</div>
        <div class="form-grid">
            <div class="form-group"><label class="form-label">Turma</label>
                <select name="turma_id" class="form-select @error('turma_id') is-invalid @enderror">
                    <option value="">Selecionar turma...</option>
                    @foreach($turmas as $turma)
                    <option value="{{ $turma->id }}" {{ old('turma_id') == $turma->id ? 'selected' : '' }}>{{ $turma->nome_turma }} - {{ $turma->nivel }}</option>
                    @endforeach
                </select>
                @error('turma_id')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label class="form-label">Encarregado</label>
                <select name="encarregado_id" class="form-select @error('encarregado_id') is-invalid @enderror">
                    <option value="">Selecionar encarregado...</option>
                    @foreach($encarregados as $enc)
                    <option value="{{ $enc->id }}" {{ old('encarregado_id') == $enc->id ? 'selected' : '' }}>{{ $enc->nome }}</option>
                    @endforeach
                </select>
                @error('encarregado_id')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label class="form-label">Nível</label>
                <input type="text" name="nivel" value="{{ old('nivel') }}" class="form-input @error('nivel') is-invalid @enderror" placeholder="Ex: 10ª classe">
                @error('nivel')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label class="form-label">Ano Lectivo</label>
                <input type="number" name="ano_lectivo" value="{{ old('ano_lectivo', date('Y')) }}" min="2020" max="2030" class="form-input @error('ano_lectivo') is-invalid @enderror">
                @error('ano_lectivo')<div class="form-error">{{ $message }}</div>@enderror</div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Criar Aluno</button>
            <a href="{{ route('admin.alunos.index') }}" class="btn"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </form>
</div>
@endsection
