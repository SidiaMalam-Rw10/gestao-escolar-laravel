@extends('layouts.app')

@section('title', 'Fichiers')
@section('page-title', 'Biblioteca de Ficheiros')

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
    .page-title{font-size:20px;font-weight:700}
    .filter-bar{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap}
    .search-input{flex:1;min-width:200px;padding:10px 14px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s}
    .search-input:focus{outline:none;border-color:var(--accent-green)}
    .search-input::placeholder{color:var(--text-secondary)}
    .filter-select{padding:10px 14px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;cursor:pointer;min-width:160px}
    .filter-select:focus{outline:none;border-color:var(--accent-green)}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .stats{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap}
    .stat{background:var(--bg-card);border:1px solid var(--border-color);border-radius:8px;padding:12px 18px;display:flex;align-items:center;gap:10px}
    .stat i{color:var(--accent-green)}
    .stat span{font-size:12px;color:var(--text-secondary)}
    .stat b{font-size:16px;display:block}
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px}
    .ficheiro-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:16px;display:flex;flex-direction:column;gap:10px;transition:border-color .15s}
    .ficheiro-card:hover{border-color:rgba(255,255,255,.18)}
    .ficheiro-top{display:flex;align-items:flex-start;gap:12px}
    .ficheiro-icone{width:40px;height:40px;border-radius:8px;background:var(--bg-hover);color:var(--accent-green);display:grid;place-items:center;font-size:18px;flex-shrink:0}
    .ficheiro-titulo{font-size:13px;font-weight:600;line-height:1.35}
    .ficheiro-desc{font-size:11px;color:var(--text-secondary);line-height:1.5}
    .ficheiro-meta{display:flex;gap:12px;font-size:11px;color:var(--text-secondary);flex-wrap:wrap}
    .ficheiro-meta i{margin-right:4px}
    .tag{padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap}
    .tag-documento{background:rgba(107,114,128,.15);color:var(--text-secondary)}
    .tag-livro{background:rgba(59,130,246,.12);color:#60A5FA}
    .tag-manual{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .tag-outro{background:rgba(168,85,247,.12);color:#C084FC}
    .empty-state{text-align:center;padding:40px 20px;color:var(--text-secondary);font-size:12px;grid-column:1/-1}
    .empty-state i{font-size:32px;margin-bottom:12px;display:block;opacity:.3}
    .pagination{display:flex;justify-content:center;gap:6px;margin-top:20px}
    .pagination a,.pagination span{padding:8px 12px;border-radius:6px;font-size:13px;text-decoration:none;transition:all .15s}
    .pagination a{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-secondary)}
    .pagination a:hover{border-color:rgba(255,255,255,.15);color:var(--text-primary)}
    .pagination .active{background:var(--accent-green);color:#000;font-weight:600}
    .ficheiro-acoes{display:flex;gap:8px;margin-top:auto;padding-top:4px}
    .btn-icon{width:32px;height:32px;border-radius:6px;border:1px solid var(--border-color);background:var(--bg-card);color:var(--text-secondary);display:grid;place-items:center;cursor:pointer;transition:all .15s;text-decoration:none;font-size:12px}
    .btn-icon:hover{border-color:rgba(255,255,255,.15);color:var(--accent-green)}
    .btn-icon.danger:hover{border-color:rgba(239,68,68,.3);color:#FCA5A5;background:rgba(239,68,68,.08)}
    .btn-download{flex:1;display:inline-flex;align-items:center;justify-content:center;gap:8px;background:var(--bg-hover);border:1px solid var(--border-color);color:var(--text-primary);padding:9px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;transition:all .15s}
    .btn-download:hover{border-color:var(--accent-green);color:var(--accent-green)}
    @media(max-width:768px){.filter-bar{flex-direction:column}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif

<div class="page-header">
    <div class="page-title">Biblioteca de Ficheiros</div>
    @if($podeGerir)
    <a href="{{ route('admin.ficheiros.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Carregar Ficheiro</a>
    @endif
</div>

<div class="stats">
    <div class="stat"><i class="far fa-folder-open"></i><div><b>{{ $total }}</b><span>Ficheiros</span></div></div>
    <div class="stat"><i class="fas fa-weight-hanging"></i><div><b>{{ $totalTamanho >= 1048576 ? round($totalTamanho/1048576, 1) . ' MB' : round($totalTamanho/1024, 1) . ' KB' }}</b><span>Total</span></div></div>
</div>

<form method="GET" action="{{ route('ficheiros.index') }}">
    <div class="filter-bar">
        <input type="text" name="search" class="search-input" placeholder="Pesquisar por título, descrição ou nome do ficheiro..." value="{{ request('search') }}">
        <select name="categoria" class="filter-select">
            <option value="">Todas as categorias</option>
            @foreach(\App\Models\Ficheiro::CATEGORIAS as $categoria => $legenda)
            <option value="{{ $categoria }}" {{ request('categoria') === $categoria ? 'selected' : '' }}>{{ $legenda }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Filtrar</button>
        @if(request()->hasAny(['search','categoria']))
        <a href="{{ route('ficheiros.index') }}" class="btn"><i class="fas fa-times"></i> Limpar</a>
        @endif
    </div>
</form>

<div class="grid">
    @forelse($ficheiros as $ficheiro)
    <div class="ficheiro-card">
        <div class="ficheiro-top">
            <div class="ficheiro-icone"><i class="{{ $ficheiro->icone() }}"></i></div>
            <div style="flex:1;min-width:0">
                <div class="ficheiro-titulo">{{ $ficheiro->titulo }}</div>
                <div style="margin-top:4px"><span class="tag tag-{{ $ficheiro->categoria }}">{{ \App\Models\Ficheiro::categoriaLabel($ficheiro->categoria) }}</span></div>
            </div>
        </div>
        @if($ficheiro->descricao)<div class="ficheiro-desc">{{ Str::limit($ficheiro->descricao, 100) }}</div>@endif
        <div class="ficheiro-meta">
            <span><i class="far fa-file"></i>{{ strtoupper($ficheiro->extensao) }}</span>
            <span><i class="fas fa-weight-hanging"></i>{{ $ficheiro->tamanhoFormatado() }}</span>
            @if($ficheiro->usuario)<span><i class="far fa-user"></i>{{ $ficheiro->usuario->name }}</span>@endif
            <span><i class="far fa-calendar-alt"></i>{{ $ficheiro->created_at->format('d/m/Y') }}</span>
        </div>
        <div class="ficheiro-acoes">
            <a href="{{ route('ficheiros.download', $ficheiro) }}" class="btn-download"><i class="fas fa-download"></i> Descarregar</a>
            @if($podeGerir)
            <form method="POST" action="{{ route('admin.ficheiros.destroy', $ficheiro) }}" onsubmit="return confirm('Eliminar este ficheiro?');">@csrf @method('DELETE')
                <button type="submit" class="btn-icon danger" title="Eliminar"><i class="fas fa-trash"></i></button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="empty-state"><i class="far fa-folder-open"></i><div>Nenhum ficheiro encontrado</div></div>
    @endforelse
</div>

@if($ficheiros->hasPages())<div class="pagination">{{ $ficheiros->links() }}</div>@endif
@endsection