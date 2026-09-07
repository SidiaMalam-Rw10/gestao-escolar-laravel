@extends('layouts.app')

@section('title', 'Gestão de Feedbacks')
@section('page-title', 'Gestão de Feedbacks')

@section('content')
<style>
    .fb-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }
    .fb-stat {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .fb-stat-icon {
        width: 42px; height: 42px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px;
    }
    .fb-stat-icon.g { background: rgba(34,197,94,.12); color: var(--accent-green); }
    .fb-stat-icon.b { background: rgba(59,130,246,.12); color: #93C5FD; }
    .fb-stat-icon.r { background: rgba(239,68,68,.12); color: #F87171; }
    .fb-stat-icon.y { background: rgba(234,179,8,.12); color: var(--accent-yellow); }
    .fb-stat-val { font-size: 22px; font-weight: 700; line-height: 1; }
    .fb-stat-label { font-size: 10px; text-transform: uppercase; letter-spacing: .6px; color: var(--text-secondary); margin-top: 5px; }

    .fb-toolbar {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }
    .fb-filter {
        padding: 8px 12px;
        background: var(--bg-input, #151D19);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        cursor: pointer;
    }
    .fb-filter:focus { outline: none; border-color: var(--accent-green); }

    .fb-list { display: flex; flex-direction: column; gap: 14px; }

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
        margin-bottom: 10px;
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

    .fb-user {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--text-secondary);
    }
    .fb-user i { color: var(--text-secondary); }
    .fb-user strong { color: var(--text-primary); font-weight: 500; }

    .fb-data { font-size: 11px; color: var(--text-secondary); margin-left: auto; }

    .fb-assunto { font-size: 14px; font-weight: 600; }
    .fb-mensagem { font-size: 13px; line-height: 1.55; color: var(--text-primary); margin-top: 6px; white-space: pre-wrap; }

    .fb-pagina { font-size: 11px; color: var(--text-secondary); margin-top: 8px; }
    .fb-pagina i { margin-right: 5px; }

    .fb-resposta {
        margin-top: 14px;
        padding: 12px 14px;
        border-left: 3px solid var(--accent-green);
        background: rgba(34,197,94,.05);
        border-radius: 0 8px 8px 0;
        font-size: 13px;
        line-height: 1.5;
        white-space: pre-wrap;
    }

    .fb-responder {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px dashed var(--border-color);
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }
    .fb-responder select, .fb-responder textarea {
        background: var(--bg-input, #151D19);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        font-size: 12px;
        font-family: inherit;
        padding: 8px 10px;
    }
    .fb-responder select:focus, .fb-responder textarea:focus { outline: none; border-color: var(--accent-green); }
    .fb-responder textarea { flex: 1; min-width: 260px; resize: vertical; min-height: 44px; }

    .btn-ghost {
        background: var(--accent-green);
        color: #000;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-ghost:hover { background: #1ea34e; }

    .btn-del {
        background: none;
        border: 1px solid rgba(239,68,68,.3);
        color: #F87171;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        font-family: inherit;
        transition: background .15s;
    }
    .btn-del:hover { background: rgba(239,68,68,.1); }

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

    .pagination-footer { margin-top: 20px; }
    .pagination-footer nav > div { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
    .pagination-footer nav svg { width: 16px; height: 16px; }

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

<div class="fb-stats">
    <div class="fb-stat">
        <div class="fb-stat-icon g"><i class="fas fa-inbox"></i></div>
        <div>
            <div class="fb-stat-val">{{ $stats['total'] }}</div>
            <div class="fb-stat-label">Total</div>
        </div>
    </div>
    <div class="fb-stat">
        <div class="fb-stat-icon b"><i class="fas fa-ellipsis-h"></i></div>
        <div>
            <div class="fb-stat-val">{{ $stats['novos'] }}</div>
            <div class="fb-stat-label">Novos</div>
        </div>
    </div>
    <div class="fb-stat">
        <div class="fb-stat-icon r"><i class="fas fa-bug"></i></div>
        <div>
            <div class="fb-stat-val">{{ $stats['problemas'] }}</div>
            <div class="fb-stat-label">Problemas</div>
        </div>
    </div>
    <div class="fb-stat">
        <div class="fb-stat-icon y"><i class="fas fa-check-double"></i></div>
        <div>
            <div class="fb-stat-val">{{ $stats['resolvidos'] }}</div>
            <div class="fb-stat-label">Resolvidos</div>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('admin.feedbacks.index') }}" class="fb-toolbar">
    <select name="tipo" class="fb-filter" onchange="this.form.submit()">
        <option value="">Todos os tipos</option>
        <option value="feedback" @selected(request('tipo') === 'feedback')>Feedback</option>
        <option value="problema" @selected(request('tipo') === 'problema')>Problema</option>
    </select>
    <select name="estado" class="fb-filter" onchange="this.form.submit()">
        <option value="">Todos os estados</option>
        <option value="novo" @selected(request('estado') === 'novo')>Novo</option>
        <option value="em_analise" @selected(request('estado') === 'em_analise')>Em análise</option>
        <option value="resolvido" @selected(request('estado') === 'resolvido')>Resolvido</option>
    </select>
    @if(request()->has('tipo') || request()->has('estado'))
    <a href="{{ route('admin.feedbacks.index') }}" style="font-size:12px;color:var(--text-secondary);align-self:center">
        <i class="fas fa-times" style="margin-right:4px"></i>Limpar filtros
    </a>
    @endif
</form>

<div class="fb-list">
    @forelse($feedbacks as $fb)
    <div class="fb-item">
        <div class="fb-item-head">
            <span class="fb-badge {{ $fb->tipo }}">
                <i class="fas {{ $fb->tipo === 'problema' ? 'fa-bug' : 'fa-comment-dots' }}"></i>
                {{ $fb->tipo_label }}
            </span>
            <span class="fb-user">
                <i class="fas fa-user-circle"></i>
                <strong>{{ $fb->user?->name ?? 'Utilizador removido' }}</strong>
                @if($fb->user?->isProfessor()) (Professor)@endif
                @if($fb->user?->isAluno()) (Aluno)@endif
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
            <i class="fas fa-reply" style="margin-right:6px;color:var(--accent-green)"></i>
            {{ $fb->resposta }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.feedbacks.atualizar', $fb) }}" class="fb-responder">
            @csrf
            @method('PUT')
            <select name="estado" title="Estado">
                @foreach(\App\Models\Feedback::ESTADOS as $val => $label)
                <option value="{{ $val }}" @selected($fb->estado === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <textarea name="resposta" placeholder="Escrever resposta ao utilizador...">{{ $fb->resposta }}</textarea>
            <button type="submit" class="btn-ghost">
                <i class="fas fa-reply"></i> Atualizar
            </button>
        </form>

        <form method="POST" action="{{ route('admin.feedbacks.destroy', $fb) }}" onsubmit="return confirm('Eliminar este feedback?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-del" style="margin-top:10px">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </form>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <div>Não há feedbacks ou problemas com estes critérios.</div>
    </div>
    @endforelse
</div>

<div class="pagination-footer">
    {{ $feedbacks->links() }}
</div>
@endsection