@extends('layouts.app')

@section('title', 'Novo Departamento')
@section('page-title', 'Criar Departamento')

@section('content')
<style>
    .form-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 28px;
        max-width: 600px;
    }

    .form-section-title {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-secondary);
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border-color);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group.full-width {
        grid-column: span 2;
    }

    .form-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 6px;
        color: var(--text-primary);
    }

    .form-label .required {
        color: #FCA5A5;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 10px 14px;
        background: var(--bg-input, #151D19);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        font-size: 13px;
        transition: border-color 0.15s;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--accent-green);
    }

    .form-input::placeholder {
        color: var(--text-secondary);
    }

    .form-textarea {
        resize: vertical;
        min-height: 80px;
        font-family: inherit;
    }

    .form-error {
        font-size: 11px;
        color: #FCA5A5;
        margin-top: 4px;
    }

    .is-invalid {
        border-color: #FCA5A5 !important;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        padding-top: 16px;
        border-top: 1px solid var(--border-color);
    }

    .btn-primary {
        background: var(--accent-green);
        color: #000;
        border: none;
        padding: 10px 24px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s;
        text-decoration: none;
    }

    .btn-primary:hover {
        background: #1ea34e;
    }

    .btn {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        padding: 10px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s;
    }

    .btn:hover {
        border-color: rgba(255, 255, 255, 0.15);
        background: var(--bg-hover);
    }

    .alert-error {
        padding: 12px 16px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #FCA5A5;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

@if($errors->any())
<div class="alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <span>Corrija os erros abaixo para continuar.</span>
</div>
@endif

<div class="form-card">
    <form method="POST" action="{{ route('admin.departamentos.store') }}">
        @csrf

        <div class="form-section-title">Dados do Departamento</div>
        <div class="form-grid">
            <div class="form-group full-width">
                <label class="form-label">Nome <span class="required">*</span></label>
                <input type="text" name="nome" value="{{ old('nome') }}" class="form-input @error('nome') is-invalid @enderror" required autofocus placeholder="Ex: Departamento de Matemática">
                @error('nome')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Sigla</label>
                <input type="text" name="sigla" value="{{ old('sigla') }}" class="form-input @error('sigla') is-invalid @enderror" placeholder="Ex: DMAT" maxlength="20">
                @error('sigla')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group full-width">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-textarea @error('descricao') is-invalid @enderror" placeholder="Breve descrição do departamento...">{{ old('descricao') }}</textarea>
                @error('descricao')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Criar Departamento
            </button>
            <a href="{{ route('admin.departamentos.index') }}" class="btn">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </form>
</div>
@endsection
