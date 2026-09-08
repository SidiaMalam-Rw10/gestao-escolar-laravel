@extends('layouts.app')

@section('title', 'Editar Turma')
@section('page-title', 'Editar Turma')

@section('content')
<style>
    .form-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:28px;max-width:600px}
    .form-section-title{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:var(--text-secondary);margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border-color)}
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px}
    .form-group{margin-bottom:0}.form-group.full-width{grid-column:span 2}
    .form-label{display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:var(--text-primary)}
    .form-label .required{color:#FCA5A5}
    .form-input,.form-select{width:100%;padding:10px 14px;background:var(--bg-input,#151D19);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s}
    .form-input:focus,.form-select:focus{outline:none;border-color:var(--accent-green)}
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
    <form method="POST" action="{{ route('admin.turmas.update', $turma) }}">
        @csrf @method('PUT')
        <div class="form-section-title">Dados da Turma</div>
        <div class="form-grid">
            <div class="form-group full-width"><label class="form-label">Nome da Turma <span class="required">*</span></label>
                <input type="text" name="nome_turma" value="{{ old('nome_turma', $turma->nome_turma) }}" class="form-input @error('nome_turma') is-invalid @enderror" required>
                @error('nome_turma')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label class="form-label">Nível <span class="required">*</span></label>
                <input type="text" name="nivel" value="{{ old('nivel', $turma->nivel) }}" class="form-input @error('nivel') is-invalid @enderror" required>
                @error('nivel')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label class="form-label">Período <span class="required">*</span></label>
                <select name="periodo" class="form-select" required>
                    <option value="Manhã" {{ old('periodo', $turma->periodo) === 'Manhã' ? 'selected' : '' }}>Manhã</option>
                    <option value="Tarde" {{ old('periodo', $turma->periodo) === 'Tarde' ? 'selected' : '' }}>Tarde</option>
                    <option value="Noite" {{ old('periodo', $turma->periodo) === 'Noite' ? 'selected' : '' }}>Noite</option>
                </select></div>
            <div class="form-group"><label class="form-label">Ano Lectivo <span class="required">*</span></label>
                <input type="number" name="ano_lectivo" value="{{ old('ano_lectivo', $turma->ano_lectivo) }}" min="2020" max="2030" class="form-input" required>
            </div>
            <div class="form-group"><label class="form-label">Capacidade</label>
                <input type="number" name="capacidade" value="{{ old('capacidade', $turma->capacidade) }}" min="1" max="100" class="form-input"></div>
            <div class="form-group full-width"><label class="form-label">Professor Responsável</label>
                <select name="professor_responsavel_id" class="form-select">
                    <option value="">Selecionar professor...</option>
                    @foreach($professores as $prof)
                    <option value="{{ $prof->id }}" {{ old('professor_responsavel_id', $turma->professor_responsavel_id) == $prof->id ? 'selected' : '' }}>{{ $prof->name }}</option>
                    @endforeach
                </select></div>
            <div class="form-group full-width"><label class="form-label">Propina Mensal (Xof)</label>
                <input type="number" name="propina_mensal" value="{{ old('propina_mensal', $turma->propina_mensal) }}" min="0" step="0.01" class="form-input">
            </div>
            <div class="form-group full-width"><label class="form-label">Meses de pagamento por ano</label>
                <select name="meses_pagamento" class="form-select">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ old('meses_pagamento', $turma->meses_pagamento) == $m ? 'selected' : '' }}>{{ $m }} {{ $m === 1 ? 'mês' : 'meses' }}</option>
                    @endfor
                </select>
                <small style="color:var(--text-secondary)">Nº de mensalidades cobradas num ano letivo (ex.: 9, 10 ou 12).</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Guardar Alterações</button>
            <a href="{{ route('admin.turmas.index') }}" class="btn"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </form>
</div>
@endsection
