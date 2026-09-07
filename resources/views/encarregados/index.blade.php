@extends('layouts.app')

@section('title', 'Encarregados de Educação')
@section('page-title', 'Gestão de Encarregados de Educação')

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
    .filter-select{padding:10px 14px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;cursor:pointer;min-width:180px}
    .filter-select:focus{outline:none;border-color:var(--accent-green)}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .table-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:hidden}
    .table-header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--border-color)}
    .table-title{font-size:14px;font-weight:600}
    .table-count{font-size:12px;color:var(--text-secondary)}
    table{width:100%;border-collapse:collapse}
    thead th{text-align:left;padding:12px 20px;font-size:10px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.8px;font-weight:600;border-bottom:1px solid var(--border-color);background:rgba(0,0,0,.2)}
    tbody tr{transition:background .15s}
    tbody tr:hover{background:var(--bg-hover)}
    tbody td{padding:12px 20px;font-size:13px;border-bottom:1px solid rgba(255,255,255,.03)}
    .user-cell{display:flex;align-items:center;gap:12px}
    .user-avatar{width:32px;height:32px;border-radius:50%;background:var(--accent-blue,#60A5FA);color:#000;display:grid;place-items:center;font-weight:700;font-size:12px;flex-shrink:0}
    .user-name{font-weight:600;color:var(--text-primary)}
    .user-username{font-size:11px;color:var(--text-secondary)}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap}
    .tag-alunos{background:rgba(59,130,246,.12);color:#60A5FA}
    .tag-vazio{background:rgba(107,114,128,.15);color:var(--text-secondary)}
    .actions-cell{display:flex;gap:6px}
    .btn-icon{width:30px;height:30px;border-radius:6px;border:1px solid var(--border-color);background:var(--bg-card);color:var(--text-secondary);display:grid;place-items:center;cursor:pointer;transition:all .15s;text-decoration:none;font-size:12px}
    .btn-icon:hover{border-color:rgba(255,255,255,.15);color:var(--text-primary);background:var(--bg-hover)}
    .btn-icon.danger:hover{border-color:rgba(239,68,68,.3);color:#FCA5A5;background:rgba(239,68,68,.08)}
    .empty-state{text-align:center;padding:40px 20px;color:var(--text-secondary);font-size:12px}
    .empty-state i{font-size:32px;margin-bottom:12px;display:block;opacity:.3}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .alert-error{padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .pagination{display:flex;justify-content:center;gap:6px;margin-top:20px}
    .pagination a,.pagination span{padding:8px 12px;border-radius:6px;font-size:13px;text-decoration:none;transition:all .15s}
    .pagination a{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-secondary)}
    .pagination a:hover{border-color:rgba(255,255,255,.15);color:var(--text-primary)}
    .pagination .active{background:var(--accent-green);color:#000;font-weight:600}
    @media(max-width:1200px){.stats-grid{grid-template-columns:repeat(3,1fr)}}
    @media(max-width:768px){.stats-grid{grid-template-columns:1fr}.filter-bar{flex-direction:column}table{font-size:12px}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert-error"><i class="fas fa-exclamation-circle"></i>{{ session('error') }}</div>@endif

<div class="stats-grid">
    <div class="stat-card"><div class="stat-label">Total Encarregados</div><div class="stat-value">{{ $stats['total'] }}</div></div>
    <div class="stat-card"><div class="stat-label">Com Alunos</div><div class="stat-value" style="color:var(--accent-green)">{{ $stats['com_alunos'] }}</div></div>
    <div class="stat-card"><div class="stat-label">Sem Alunos</div><div class="stat-value" style="color:var(--accent-yellow)">{{ $stats['sem_alunos'] }}</div></div>
</div>

<div class="page-header">
    <div class="page-title">Todos os Encarregados</div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('admin.encarregados.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Novo Encarregado</a>
    @endif
</div>

<form method="GET" action="{{ route('admin.encarregados.index') }}">
    <div class="filter-bar">
        <input type="text" name="search" class="search-input" placeholder="Pesquisar por nome, telefone, email..." value="{{ request('search') }}">
        <select name="status" class="filter-select">
            <option value="">Todos</option>
            <option value="com_alunos" {{ request('status') === 'com_alunos' ? 'selected' : '' }}>Com alunos associados</option>
            <option value="sem_alunos" {{ request('status') === 'sem_alunos' ? 'selected' : '' }}>Sem alunos</option>
        </select>
        <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Filtrar</button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.encarregados.index') }}" class="btn"><i class="fas fa-times"></i> Limpar</a>
        @endif
    </div>
</form>

<div class="table-card">
    <div class="table-header">
        <div class="table-title">Lista de Encarregados</div>
        <div class="table-count">{{ $encarregados->total() }} registro(s)</div>
    </div>
    @if($encarregados->count() > 0)
    <table>
        <thead><tr><th>Encarregado</th><th>Parentesco</th><th>Contacto</th><th>Endereço</th><th>Alunos</th><th style="text-align:right">Ações</th></tr></thead>
        <tbody>
            @foreach($encarregados as $encarregado)
            <tr>
                <td>
                    <div class="user-cell">
                        <div class="user-avatar">{{ strtoupper(substr($encarregado->nome, 0, 1)) }}</div>
                        <div>
                            <div class="user-name">{{ $encarregado->nome }}</div>
                            @if($encarregado->email)
                            <div class="user-username">{{ $encarregado->email }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td style="font-size:12px">
                    @if($encarregado->parentesco)
                    <span class="tag tag-alunos">{{ $encarregado->parentesco }}</span>
                    @else
                    <span style="color:var(--text-secondary);font-size:12px">—</span>
                    @endif
                </td>
                <td style="font-size:12px">
                    @if($encarregado->telefone)
                    <div><i class="fas fa-phone" style="color:var(--text-secondary);margin-right:4px"></i>{{ $encarregado->telefone }}</div>
                    @else
                    <span style="color:var(--text-secondary)">—</span>
                    @endif
                </td>
                <td style="font-size:12px;color:var(--text-secondary)">{{ $encarregado->endereco ?? '—' }}</td>
                <td>
                    <span class="tag {{ $encarregado->alunos_count > 0 ? 'tag-alunos' : 'tag-vazio' }}">{{ $encarregado->alunos_count }} aluno(s)</span>
                </td>
                <td>
                    <div class="actions-cell" style="justify-content:flex-end">
                        <a href="{{ route('admin.encarregados.show', $encarregado) }}" class="btn-icon" title="Ver"><i class="fas fa-eye"></i></a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.encarregados.edit', $encarregado) }}" class="btn-icon" title="Editar"><i class="fas fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.encarregados.destroy', $encarregado) }}" style="display:inline" onsubmit="return confirm('Eliminar este encarregado de educação? Os alunos associados serão desassociados.');">@csrf @method('DELETE')
                            <button type="submit" class="btn-icon danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state"><i class="fas fa-user-tie"></i><div>Nenhum encarregado de educação encontrado</div></div>
    @endif
</div>

@if($encarregados->hasPages())<div class="pagination">{{ $encarregados->links() }}</div>@endif
@endsection