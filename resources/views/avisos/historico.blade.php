@extends('layouts.app')

@section('title', 'Histórico de Avisos')
@section('page-title', 'Histórico de Avisos')

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
    .page-title{font-size:20px;font-weight:700}
    .card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:hidden;margin-bottom:16px}
    .card-header{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid var(--border-color)}
    .card-title{font-size:14px;font-weight:600}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap}
    .tag-todos{background:rgba(107,114,128,.15);color:var(--text-secondary)}
    .tag-alunos{background:rgba(234,179,8,.12);color:var(--accent-yellow)}
    .tag-professores{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .tag-turma{background:rgba(59,130,246,.12);color:#60A5FA}
    .tag-individual{background:rgba(236,72,153,.12);color:#F9A8D4}
    .tag-naolidas{background:rgba(239,68,68,.12);color:#FCA5A5}
    .tag-lidas{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .aviso-body{padding:16px 20px}
    .aviso-mensagem{font-size:13px;color:var(--text-secondary);line-height:1.6}
    .aviso-meta{display:flex;gap:16px;margin-top:12px;font-size:11px;color:var(--text-secondary);flex-wrap:wrap}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:7px 12px;border-radius:6px;cursor:pointer;font-size:12px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .empty-state{text-align:center;padding:40px 20px;color:var(--text-secondary);font-size:12px}
    .empty-state i{font-size:32px;margin-bottom:12px;display:block;opacity:.3}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .pagination{display:flex;justify-content:center;gap:6px;margin-top:20px}
    .pagination a,.pagination span{padding:8px 12px;border-radius:6px;font-size:13px;text-decoration:none;transition:all .15s}
    .pagination a{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-secondary)}
    .pagination a:hover{border-color:rgba(255,255,255,.15);color:var(--text-primary)}
    .pagination .active{background:var(--accent-green);color:#000;font-weight:600}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif

<div class="page-header">
    <div class="page-title">Histórico de Avisos</div>
    <span style="font-size:12px;color:var(--text-secondary)">
        {{ $avisos->total() }} aviso(s) no total
    </span>
</div>

@if($avisos->count() > 0)
@foreach($avisos as $aviso)
@php $lido = $lidosIds->contains($aviso->id); @endphp
<div class="card" style="{{ $lido ? '' : 'border-color: rgba(239,68,68,0.35);' }}">
    <div class="card-header">
        <div class="card-title">
            <i class="fas fa-bullhorn" style="color:#FCD34D;margin-right:8px"></i>{{ $aviso->titulo }}
            <span class="tag tag-{{ $aviso->destinatario_tipo }}" style="margin-left:8px">{{ ucfirst($aviso->destinatario_tipo) }}</span>
            <span class="tag {{ $lido ? 'tag-lidas' : 'tag-naolidas' }}" style="margin-left:4px">
                @if($lido)
                    <i class="fas fa-check" style="margin-right:3px"></i> Lido
                @else
                    Não lido
                @endif
            </span>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <span style="font-size:11px;color:var(--text-secondary)">{{ $aviso->created_at->format('d/m/Y H:i') }}</span>
            @if(!$lido)
            <form method="POST" action="{{ route('avisos.lido', $aviso) }}" style="display:inline">@csrf
                <button type="submit" class="btn"><i class="fas fa-check"></i> Marcar como lido</button>
            </form>
            @endif
        </div>
    </div>
    <div class="aviso-body">
        <div class="aviso-mensagem">{{ $aviso->mensagem }}</div>
        <div class="aviso-meta">
            @if($aviso->turma)<span><i class="fas fa-school" style="margin-right:4px"></i>Turma: {{ $aviso->turma->nome_turma }}</span>@endif
            @if($aviso->destinatario_tipo === 'individual' && $aviso->destinatario)<span><i class="fas fa-user" style="margin-right:4px"></i>{{ $aviso->destinatario->isEncarregado() ? 'Encarregado' : 'Aluno' }}: {{ $aviso->destinatario->name }}</span>@endif
            @if($aviso->remetente)<span><i class="fas fa-user-shield" style="margin-right:4px"></i>Por: {{ $aviso->remetente->name }}</span>@endif
        </div>
    </div>
</div>
@endforeach

@if($avisos->hasPages())<div class="pagination">{{ $avisos->links() }}</div>@endif
@else
<div class="card">
    <div class="empty-state"><i class="fas fa-bullhorn"></i><div>Sem avisos para si neste momento.</div></div>
</div>
@endif
@endsection