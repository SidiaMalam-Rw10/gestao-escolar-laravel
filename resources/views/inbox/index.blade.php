@extends('layouts.app')

@section('title', 'Inbox')
@section('page-title', 'Caixa de Entrada')

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
    .page-title{font-size:20px;font-weight:700}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:hidden;margin-bottom:16px}
    .abas{display:flex;gap:6px;border-bottom:1px solid var(--border-color);padding:8px 16px 0;background:var(--bg-card)}
    .aba{padding:10px 16px;font-size:13px;color:var(--text-secondary);text-decoration:none;border-bottom:2px solid transparent;margin-bottom:-1px;display:inline-flex;align-items:center;gap:8px}
    .aba:hover{color:var(--text-primary)}
    .aba.active{color:var(--text-primary);border-bottom-color:var(--accent-green);font-weight:600}
    .badge-lida{background:rgba(239,68,68,.15);color:#FCA5A5;font-size:10px;font-weight:700;min-width:16px;height:16px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;padding:0 5px}
    .msg-linha{display:flex;align-items:center;gap:14px;padding:14px 16px;border-bottom:1px solid var(--border-color);text-decoration:none;color:var(--text-primary);transition:background .12s}
    .msg-linha:hover{background:var(--bg-hover)}
    .msg-linha.nao-lida{background:rgba(34,197,94,.04)}
    .msg-avatar{width:36px;height:36px;border-radius:50%;background:var(--bg-hover);display:grid;place-items:center;font-size:13px;font-weight:700;color:var(--accent-green);flex-shrink:0}
    .msg-corpo{flex:1;min-width:0}
    .msg-top{display:flex;align-items:center;gap:8px;width:100%}
    .msg-assunto{font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .msg-linha.nao-lida .msg-assunto{color:var(--accent-green)}
    .msg-ponto{width:8px;height:8px;border-radius:50%;background:#EF4444;flex-shrink:0}
    .msg-sub{font-size:11px;color:var(--text-secondary);margin-top:3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:520px}
    .msg-data{font-size:11px;color:var(--text-secondary);white-space:nowrap;flex-shrink:0}
    .msg-dest{font-size:11px;color:var(--text-secondary);display:flex;flex-wrap:wrap;gap:4px;margin-top:3px}
    .empty-state{text-align:center;padding:40px 20px;color:var(--text-secondary);font-size:12px}
    .empty-state i{font-size:32px;margin-bottom:12px;display:block;opacity:.3}
    .pagination{display:flex;justify-content:center;gap:6px;margin-top:20px}
    .pagination a,.pagination span{padding:8px 12px;border-radius:6px;font-size:13px;text-decoration:none;transition:all .15s}
    .pagination a{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-secondary)}
    .pagination a:hover{border-color:rgba(255,255,255,.15);color:var(--text-primary)}
    .pagination .active{background:var(--accent-green);color:#000;font-weight:600}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif

<div class="page-header">
    <div class="page-title">Inbox</div>
    <a href="{{ route('inbox.create') }}" class="btn-primary"><i class="fas fa-pen"></i> Nova Mensagem</a>
</div>

<div class="card">
    <div class="abas">
        <a href="{{ route('inbox.index', ['aba' => 'entrada']) }}" class="aba {{ $aba === 'entrada' ? 'active' : '' }}">
            <i class="fas fa-inbox"></i> Entrada
            @if($naoLidas > 0)<span class="badge-lida">{{ $naoLidas }}</span>@endif
        </a>
        <a href="{{ route('inbox.index', ['aba' => 'enviadas']) }}" class="aba {{ $aba === 'enviadas' ? 'active' : '' }}">
            <i class="far fa-paper-plane"></i> Enviadas
        </a>
    </div>

    @forelse($mensagens as $mensagem)
    @php $eNaoLida = $aba === 'entrada' && !$lidasIds->contains($mensagem->id); @endphp
    <a href="{{ route('inbox.show', $mensagem) }}" class="msg-linha {{ $eNaoLida ? 'nao-lida' : '' }}">
        @if($eNaoLida)
        <span class="msg-ponto"></span>
        @endif
        <div class="msg-avatar">{{ strtoupper(substr($mensagem->remetente->name ?? '?', 0, 1)) }}</div>
        <div class="msg-corpo">
            <div class="msg-top">
                <span class="msg-assunto">{{ $mensagem->assunto }}</span>
            </div>
            <div class="msg-sub">
                @if($aba === 'enviadas')
                Para: {{ $mensagem->destinatarios->pluck('name')->implode(', ') }}
                @else
                {{ $mensagem->remetente->name ?? 'Utilizador' }} · {{ Str::limit($mensagem->corpo, 90) }}
                @endif
            </div>
        </div>
        <div class="msg-data">
            <i class="far fa-clock" style="margin-right:4px"></i>{{ $mensagem->created_at->format('d/m/Y H:i') }}
        </div>
    </a>
    @empty
    <div class="empty-state">
        <i class="far fa-envelope-open"></i>
        <div>{{ $aba === 'enviadas' ? 'Ainda não enviou nenhuma mensagem' : 'Não tem mensagens por ler' }}</div>
    </div>
    @endforelse
</div>

@if($mensagens->hasPages())<div class="pagination">{{ $mensagens->links() }}</div>@endif
@endsection