@extends('layouts.app')

@section('title', 'Feedback & Reportar Problema')
@section('page-title', 'Feedback & Reportar Problema')

@section('content')
<style>
    .fb-wrap { max-width: 720px; }

    .fb-tabs {
        display: flex;
        gap: 8px;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 6px;
        margin-bottom: 20px;
    }
    .fb-tab {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        background: none;
        border: none;
        cursor: pointer;
        font-family: inherit;
        transition: all .15s;
        text-decoration: none;
    }
    .fb-tab.active { background: var(--accent-green); color: #000; }
    .fb-tab:not(.active):hover { background: var(--bg-hover); color: var(--text-primary); }

    .fb-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 24px;
    }

    .fb-icon {
        width: 52px; height: 52px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; margin-bottom: 16px;
    }
    .fb-icon.feedback { background: rgba(34,197,94,.12); color: var(--accent-green); }
    .fb-icon.problema { background: rgba(239,68,68,.12); color: #F87171; }

    .fb-title { font-size: 16px; font-weight: 600; }
    .fb-sub { font-size: 12px; color: var(--text-secondary); margin-top: 4px; margin-bottom: 20px; }

    .form-group { margin-bottom: 16px; }
    .form-label {
        display: block;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .7px;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }
    .form-input, .form-textarea {
        width: 100%;
        background: var(--bg-input, #151D19);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        padding: 10px 12px;
        transition: border-color .15s;
    }
    .form-input:focus, .form-textarea:focus { outline: none; border-color: var(--accent-green); }
    .form-textarea { resize: vertical; min-height: 120px; line-height: 1.5; }

    .btn-success {
        background: var(--accent-green);
        color: #000;
        border: none;
        padding: 11px 22px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: inherit;
        transition: background .15s;
    }
    .btn-success:hover { background: #1ea34e; }
    .btn-danger {
        background: rgba(239,68,68,.15);
        color: #F87171;
        border: 1px solid rgba(239,68,68,.3);
        padding: 11px 22px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: inherit;
        transition: all .15s;
    }
    .btn-danger:hover { background: rgba(239,68,68,.25); }

    .page-hint { font-size: 11px; color: var(--text-secondary); margin-top: 6px; }

    @media (max-width: 768px) {
        .fb-tabs { flex-direction: column; }
    }
</style>

<div class="fb-wrap">
    <div class="fb-tabs">
        <a href="{{ route('feedbacks.criar', ['tipo' => 'feedback']) }}" class="fb-tab {{ $tipo === 'feedback' ? 'active' : '' }}">
            <i class="fas fa-comment-dots"></i>
            Feedback
        </a>
        <a href="{{ route('feedbacks.criar', ['tipo' => 'problema']) }}" class="fb-tab {{ $tipo === 'problema' ? 'active' : '' }}">
            <i class="fas fa-bug"></i>
            Reportar problema
        </a>
    </div>

    <div class="fb-card">
        @if($tipo === 'feedback')
        <div class="fb-icon feedback">
            <i class="fas fa-comment-dots"></i>
        </div>
        <div class="fb-title">Enviar Feedback</div>
        <div class="fb-sub">Partilhe uma sugestão, elogio ou opinião sobre o sistema de gestão escolar.</div>
        @else
        <div class="fb-icon problema">
            <i class="fas fa-bug"></i>
        </div>
        <div class="fb-title">Reportar Problema</div>
        <div class="fb-sub">Descreva o erro ou problema encontrado. A equipa vai analisar e resolver o mais rápido possível.</div>
        @endif

        <form method="POST" action="{{ route('feedbacks.guardar') }}">
            @csrf
            <input type="hidden" name="tipo" value="{{ $tipo }}">
            <input type="hidden" name="pagina" value="{{ url()->current() }}">

            <div class="form-group">
                <label class="form-label">Assunto</label>
                <input type="text" name="assunto" value="{{ old('assunto') }}" class="form-input @error('assunto') is-invalid @enderror"
                       placeholder="{{ $tipo === 'problema' ? 'Ex: Não consigo marcar presenças' : 'Ex: Sugestão de melhoria' }}" maxlength="150" required>
                @error('assunto')
                <div style="color:#F87171;font-size:11px;margin-top:4px">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Mensagem</label>
                <textarea name="mensagem" class="form-textarea @error('mensagem') is-invalid @enderror" maxlength="4000" required
                          placeholder="{{ $tipo === 'problema' ? 'Descreva o problema detalhadamente...' : 'Descreva a sua sugestão ou opinião...' }}">{{ old('mensagem') }}</textarea>
                @error('mensagem')
                <div style="color:#F87171;font-size:11px;margin-top:4px">{{ $message }}</div>
                @enderror
            </div>

            @if($tipo === 'problema')
            <div class="page-hint" style="margin-bottom:16px">
                <i class="fas fa-info-circle" style="margin-right:5px"></i>
                Página onde encontrou o problema: <strong style="color:var(--text-primary)">{{ request()->input('pagina_anterior', url()->previous()) }}</strong>
            </div>
            @endif

            <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                <button type="submit" class="{{ $tipo === 'problema' ? 'btn-danger' : 'btn-success' }}">
                    <i class="fas fa-paper-plane"></i>
                    {{ $tipo === 'problema' ? 'Reportar problema' : 'Enviar feedback' }}
                </button>
                <a href="{{ route('feedbacks.meus') }}" style="font-size:12px;color:var(--text-secondary)">
                    <i class="fas fa-list" style="margin-right:5px"></i>
                    Ver os meus envios
                </a>
            </div>
        </form>
    </div>
</div>
@endsection