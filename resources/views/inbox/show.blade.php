@extends('layouts.app')

@section('title', $mensagem->assunto)
@section('page-title', 'Mensagem')

@section('content')
<style>
    .card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:hidden;margin-bottom:16px}
    .card-header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--border-color);gap:12px;flex-wrap:wrap}
    .card-title{font-size:15px;font-weight:700}
    .msg-corpo{padding:22px 20px;font-size:13px;color:var(--text-primary);line-height:1.7;white-space:pre-wrap}
    .msg-meta{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:6px}
    .msg-avatar{width:36px;height:36px;border-radius:50%;background:var(--bg-hover);color:var(--accent-green);display:grid;place-items:center;font-size:13px;font-weight:700}
    .msg-quem{font-size:13px;font-weight:600}
    .msg-data{font-size:11px;color:var(--text-secondary)}
    .msg-para{font-size:11px;color:var(--text-secondary);padding:12px 20px;border-top:1px solid var(--border-color);background:var(--bg-hover);display:flex;flex-wrap:wrap;gap:4px}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .btn-danger{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5}
    .btn-danger:hover{background:rgba(239,68,68,.18);border-color:rgba(239,68,68,.5);color:#FCA5A5}
</style>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="far fa-envelope-open" style="color:#FCD34D;margin-right:8px"></i>{{ $mensagem->assunto }}</div>
        <div style="display:flex;gap:10px">
            @if(!$eRemetente)
            <a href="{{ route('inbox.create', ['destinatario' => $mensagem->remetente_id, 'assunto' => 'Re: ' . $mensagem->assunto]) }}" class="btn-primary"><i class="fas fa-reply"></i> Responder</a>
            @endif
            <a href="{{ route('inbox.index') }}" class="btn"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="msg-corpo">
        <div class="msg-meta">
            <div class="msg-avatar">{{ strtoupper(substr($mensagem->remetente->name ?? '?', 0, 1)) }}</div>
            <div>
                <div class="msg-quem">{{ $mensagem->remetente->name ?? 'Utilizador' }} <span style="font-weight:400;color:var(--text-secondary)">· {{ $mensagem->remetente->role ?? '' }}</span></div>
                <div class="msg-data"><i class="far fa-clock" style="margin-right:4px"></i>{{ $mensagem->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
        <div style="border-top:1px solid var(--border-color);margin:18px 0;padding-top:18px">
            {{ $mensagem->corpo }}
        </div>
    </div>
    <div class="msg-para"><i class="far fa-user-circle" style="margin-right:6px"></i>Para: {{ $mensagem->destinatarios->pluck('name')->implode(', ') }}</div>
</div>

@if($eRemetente)
<form method="POST" action="{{ route('inbox.destroy', $mensagem) }}" onsubmit="return confirm('Eliminar esta mensagem?');">@csrf @method('DELETE')
    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Eliminar mensagem</button>
</form>
@endif
@endsection