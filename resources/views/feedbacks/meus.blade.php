@extends('layouts.app')

@section('title', 'Os Meus Envios')
@section('page-title', 'Os Meus Envios')

@section('content')
<style>
    .fb-list { display: flex; flex-direction: column; gap: 14px; max-width: 860px; }

    .fb-item {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 18px 20px;
    }

    .fb-item-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .fb-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .fb-badge.feedback { background: rgba(34,197,94,.12); color: var(--accent-green); }
    .fb-badge.problema { background: rgba(239,68,68,.12); color: #F87171; }

    .fb-estado {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .fb-estado.novo { background: rgba(59,130,246,.12); color: #93C5FD; }
    .fb-estado.em_analise { background: rgba(234,179,8,.12); color: var(--accent-yellow); }
    .fb-estado.resolvido { background: rgba(34,197,94,.12); color: var(--accent-green); }

    .fb-data { font-size: 11px; color: var(--text-secondary); margin-left: auto; }

    .fb-assunto { font-size: 14px; font-weight: 600; }
    .fb-mensagem { font-size: 13px; line-height: 1.55; color: var(--text-primary); margin-top: 6px; white-space: pre-wrap; }

    .fb-resposta {
        margin-top: 14px;
        padding: 12px 14px;
        border-left: 3px solid var(--accent-green);
        background: rgba(34,197,94,.05);
        border-radius: 0 8px 8px 0;
    }
    .fb-resposta-label { font-size: 10px; text-transform: uppercase; letter-spacing: .6px; color: var(--text-secondary); font-weight: 600; margin-bottom: 4px; }
    .fb-resposta-text { font-size: 13px; line-height: 1.5; white-space: pre-wrap; }

    .fb-pagina { font-size: 11px; color: var(--text-secondary); margin-top: 8px; }
    .fb-pagina i { margin-right: 5px; }

    .empty-state {
        text-align: center;
        padding: 56px 20px;
        color: var(--text-secondary);
        font-size: 13px;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
    }
    .empty-state i { font-size: 32px; margin-bottom: 12px; display: block; opacity: .35; }
    .empty-state a { color: var(--accent-green); text-decoration: underline; }

    .alert-success {
        padding: 12px 16px;
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.3);
        color: var(--accent-green);
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

@if(session('success'))
<div class="alert-success">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(auth()->user()->isAdmin() || auth()->user()->isDiretor())
<div style="margin-bottom:16px">
    <a href="{{ route('admin.feedbacks.index') }}" style="font-size:12px;color:var(--text-secondary)">
        <i class="fas fa-inbox" style="margin-right:5px"></i>
        Gerir feedbacks recebidos ({{ \App\Models\Feedback::where('estado', 'novo')->count() }} novos)
    </a>
</div>
@endif

<div class="fb-list">
    @forelse($meusFeedbacks as $fb)
    <div class="fb-item">
        <div class="fb-item-head">
            <span class="fb-badge {{ $fb->tipo }}">
                <i class="fas {{ $fb->tipo === 'problema' ? 'fa-bug' : 'fa-comment-dots' }}"></i>
                {{ $fb->tipo_label }}
            </span>
            <span class="fb-estado {{ $fb->estado }}">
                <i class="fas {{ $fb->estado === 'resolvido' ? 'fa-check-circle' : ($fb->estado === 'em_analise' ? 'fa-hourglass-half' : 'fa-circle') }}"></i>
                {{ $fb->estado_label }}
            </span>
            <span class="fb-data">{{ $fb->created_at->format('d/m/Y H:i') }}</span>
        </div>

        <div class="fb-assunto">{{ $fb->assunto }}</div>
        <div class="fb-mensagem">{{ $fb->mensagem }}</div>

        @if($fb->pagina)
        <div class="fb-pagina">
            <i class="fas fa-link"></i>
            {{ $fb->pagina }}
        </div>
        @endif

        @if($fb->resposta)
        <div class="fb-resposta">
            <div class="fb-resposta-label">
                <i class="fas fa-reply" style="margin-right:5px"></i>
                Resposta da equipa
            </div>
            <div class="fb-resposta-text">{{ $fb->resposta }}</div>
        </div>
        @endif
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <div>Ainda não enviou nenhum feedback ou problema.</div>
        <div style="margin-top:10px">
            <a href="{{ route('feedbacks.criar') }}">Enviar o primeiro agora</a>
        </div>
    </div>
    @endforelse
</div>
@endsection