@extends('layouts.app')

@section('title', $paginaTitulo)
@section('page-title', $titulo)

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
    .page-title{font-size:20px;font-weight:700}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .sec-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:22px 24px;margin-bottom:16px}
    .sec-title{font-size:15px;font-weight:700;color:var(--text-primary);display:flex;align-items:center;gap:10px;margin-bottom:12px}
    .sec-title i{color:var(--accent-green);font-size:15px}
    .sec-body{font-size:13px;color:var(--text-secondary);line-height:1.7;white-space:pre-wrap}
    .sec-img{width:100%;max-height:340px;object-fit:cover;border-radius:8px;border:1px solid var(--border-color);margin-bottom:14px;display:block}
    .sec-video{position:relative;width:100%;aspect-ratio:16/9;border-radius:8px;overflow:hidden;border:1px solid var(--border-color);margin-bottom:14px}
    .sec-video iframe{position:absolute;inset:0;width:100%;height:100%;border:0}
    .sec-meta{font-size:11px;color:var(--text-secondary);margin-top:14px;padding-top:12px;border-top:1px solid var(--border-color)}
    .empty-state{text-align:center;padding:60px 20px;color:var(--text-secondary);font-size:12px}
    .empty-state i{font-size:40px;margin-bottom:14px;display:block;opacity:.3}
    .empty-state .btn{margin-top:16px}
    @media(max-width:768px){.page-header{flex-direction:column;align-items:flex-start;gap:12px}}
</style>

<div class="page-header">
    <div class="page-title">{{ $titulo }}</div>
    @if(auth()->user()->isAdmin())
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.paginas.index') }}" class="btn"><i class="fas fa-cog"></i> Gerir Páginas</a>
        <a href="{{ route('admin.paginas.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Nova</a>
    </div>
    @endif
</div>

@if($secacoes->count() > 0)
@foreach($secacoes as $secacao)
<div class="sec-card">
    <div class="sec-title">
        <i class="fas {{ $tipo === 'horario' ? 'fa-clock' : ($tipo === 'atividades' ? 'fa-calendar-check' : 'fa-school') }}"></i>
        {{ $secacao->titulo }}
    </div>
    @if($secacao->imagem)
    <img src="{{ asset('storage/' . $secacao->imagem) }}" alt="{{ $secacao->titulo }}" class="sec-img">
    @endif
    @if($secacao->videoEmbedUrl())
    <div class="sec-video">
        <iframe src="{{ $secacao->videoEmbedUrl() }}" title="{{ $secacao->titulo }}" frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
    </div>
    @elseif($secacao->video_path)
    <div class="sec-video" style="background:#000">
        <video src="{{ asset('storage/' . $secacao->video_path) }}" controls style="position:absolute;inset:0;width:100%;height:100%;object-fit:contain;border:0"></video>
    </div>
    @endif
    <div class="sec-body">{{ $secacao->conteudo }}</div>
    @if($secacao->autor)
    <div class="sec-meta"><i class="fas fa-user" style="margin-right:4px"></i>Publicado por {{ $secacao->autor->name }} em {{ $secacao->created_at->format('d/m/Y') }}</div>
    @endif
</div>
@endforeach
@else
<div class="empty-state" style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px">
    <i class="fas {{ $tipo === 'horario' ? 'fa-clock' : ($tipo === 'atividades' ? 'fa-calendar-check' : 'fa-school') }}"></i>
    <div>Esta secção ainda não tem conteúdo.</div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('admin.paginas.create') }}" class="btn"><i class="fas fa-plus"></i> Adicionar conteúdo</a>
    @endif
</div>
@endif
@endsection