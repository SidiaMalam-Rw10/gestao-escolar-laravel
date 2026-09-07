@extends('layouts.app')

@section('title', 'Turmas')
@section('page-title', 'Gestão de Turmas')

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
    .page-title{font-size:20px;font-weight:700}
    .stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px}
    .stat-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:8px;padding:16px}
    .stat-label{font-size:10px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.8px;font-weight:600;margin-bottom:8px}
    .stat-value{font-size:24px;font-weight:700}
    .filter-bar{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap}
    .search-input{flex:1;min-width:200px;padding:10px 14px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s}
    .search-input:focus{outline:none;border-color:var(--accent-green)}
    .search-input::placeholder{color:var(--text-secondary)}
    .filter-select{padding:10px 14px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;cursor:pointer;min-width:140px}
    .filter-select:focus{outline:none;border-color:var(--accent-green)}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .turmas-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px}
    .turma-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:20px;transition:border-color .15s}
    .turma-card:hover{border-color:rgba(255,255,255,.1)}
    .turma-header{display:flex;justify-content:space-between;align-items:start;margin-bottom:14px}
    .turma-name{font-size:16px;font-weight:700}
    .turma-nivel{font-size:12px;color:var(--text-secondary);margin-top:2px}
    .turma-info{display:flex;flex-direction:column;gap:8px;font-size:12px;color:var(--text-secondary)}
    .turma-info-row{display:flex;align-items:center;gap:6px}
    .turma-info-row i{width:14px;text-align:center;font-size:11px}
    .turma-actions{display:flex;gap:6px;margin-top:14px;padding-top:14px;border-top:1px solid var(--border-color)}
    .turma-btn{flex:1;padding:8px;border-radius:6px;border:1px solid var(--border-color);background:var(--bg-card);color:var(--text-secondary);font-size:12px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;transition:all .15s;text-decoration:none}
    .turma-btn:hover{border-color:rgba(255,255,255,.15);color:var(--text-primary);background:var(--bg-hover)}
    .turma-btn.danger:hover{border-color:rgba(239,68,68,.3);color:#FCA5A5;background:rgba(239,68,68,.08)}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600}
    .tag-periodo{background:rgba(59,130,246,.12);color:#60A5FA}
    .tag-cap{background:rgba(234,179,8,.12);color:var(--accent-yellow)}
    .tag-prof{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .empty-state{text-align:center;padding:40px 20px;color:var(--text-secondary);font-size:12px}
    .empty-state i{font-size:32px;margin-bottom:12px;display:block;opacity:.3}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .alert-error{padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .pagination{display:flex;justify-content:center;gap:6px;margin-top:20px}
    .pagination a,.pagination span{padding:8px 12px;border-radius:6px;font-size:13px;text-decoration:none;transition:all .15s}
    .pagination a{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-secondary)}
    .pagination a:hover{border-color:rgba(255,255,255,.15);color:var(--text-primary)}
    .pagination .active{background:var(--accent-green);color:#000;font-weight:600}
    @media(max-width:768px){.stats-grid{grid-template-columns:1fr}.filter-bar{flex-direction:column}.turmas-grid{grid-template-columns:1fr}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert-error"><i class="fas fa-exclamation-circle"></i>{{ session('error') }}</div>@endif

<div class="stats-grid">
    <div class="stat-card"><div class="stat-label">Total Turmas</div><div class="stat-value">{{ $stats['total'] }}</div></div>
    <div class="stat-card"><div class="stat-label">Total Alunos</div><div class="stat-value" style="color:var(--accent-yellow)">{{ $stats['total_alunos'] }}</div></div>
    <div class="stat-card"><div class="stat-label">Capacidade Total</div><div class="stat-value" style="color:#60A5FA">{{ $stats['capacidade_total'] }}</div></div>
</div>

<div class="page-header">
    <div class="page-title">Todas as Turmas</div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('admin.turmas.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Nova Turma</a>
    @endif
</div>

<form method="GET" action="{{ route('admin.turmas.index') }}">
    <div class="filter-bar">
        <input type="text" name="search" class="search-input" placeholder="Pesquisar por nome ou nível..." value="{{ request('search') }}">
        <select name="nivel" class="filter-select">
            <option value="">Todos os níveis</option>
            @foreach($niveis as $nivel)
            <option value="{{ $nivel }}" {{ request('nivel') === $nivel ? 'selected' : '' }}>{{ $nivel }}</option>
            @endforeach
        </select>
        <select name="periodo" class="filter-select">
            <option value="">Todos os períodos</option>
            <option value="Manhã" {{ request('periodo') === 'Manhã' ? 'selected' : '' }}>Manhã</option>
            <option value="Tarde" {{ request('periodo') === 'Tarde' ? 'selected' : '' }}>Tarde</option>
            <option value="Noite" {{ request('periodo') === 'Noite' ? 'selected' : '' }}>Noite</option>
        </select>
        <select name="ano_lectivo" class="filter-select">
            <option value="">Todos os anos</option>
            @foreach($anos as $ano)
            <option value="{{ $ano }}" {{ request('ano_lectivo') == $ano ? 'selected' : '' }}>{{ $ano }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Filtrar</button>
    </div>
</form>

@if($turmas->count() > 0)
<div class="turmas-grid">
    @foreach($turmas as $turma)
    <div class="turma-card">
        <div class="turma-header">
            <div>
                <div class="turma-name">{{ $turma->nome_turma }}</div>
                <div class="turma-nivel">{{ $turma->nivel }}</div>
            </div>
            <span class="tag tag-periodo">{{ $turma->periodo }}</span>
        </div>
        <div class="turma-info">
            <div class="turma-info-row"><i class="fas fa-calendar"></i> {{ $turma->ano_lectivo }}</div>
            <div class="turma-info-row"><i class="fas fa-users"></i> {{ $turma->alunos->count() }} / {{ $turma->capacidade }} alunos</div>
            <div class="turma-info-row">
                <i class="fas fa-chalkboard-teacher"></i>
                @if($turma->professorResponsavel)
                <span class="tag tag-prof">{{ $turma->professorResponsavel->name }}</span>
                @else
                <span>Sem professor responsável</span>
                @endif
            </div>
        </div>
        <div class="turma-actions">
            <a href="{{ route('admin.turmas.show', $turma) }}" class="turma-btn"><i class="fas fa-eye"></i> Ver</a>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.turmas.edit', $turma) }}" class="turma-btn"><i class="fas fa-pen"></i> Editar</a>
            <form method="POST" action="{{ route('admin.turmas.destroy', $turma) }}" style="display:inline" onsubmit="return confirm('Eliminar esta turma?');">@csrf @method('DELETE')
                <button type="submit" class="turma-btn danger"><i class="fas fa-trash"></i></button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@else
<div class="empty-state"><i class="fas fa-school"></i><div>Nenhuma turma encontrada</div></div>
@endif

@if($turmas->hasPages())<div class="pagination">{{ $turmas->links() }}</div>@endif
@endsection
