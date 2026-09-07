@extends('layouts.app')

@section('title', 'Alunos')
@section('page-title', 'Gestão de Alunos')

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
    .page-title{font-size:20px;font-weight:700}
    .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
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
    .user-avatar{width:32px;height:32px;border-radius:50%;background:var(--accent-yellow);color:#000;display:grid;place-items:center;font-weight:700;font-size:12px;flex-shrink:0}
    .user-name{font-weight:600;color:var(--text-primary)}
    .user-username{font-size:11px;color:var(--text-secondary)}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap}
    .tag-ativo{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .tag-inativo{background:rgba(239,68,68,.12);color:#FCA5A5}
    .tag-turma{background:rgba(59,130,246,.12);color:#60A5FA}
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
    @media(max-width:1200px){.stats-grid{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:768px){.stats-grid{grid-template-columns:1fr}.filter-bar{flex-direction:column}table{font-size:12px}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert-error"><i class="fas fa-exclamation-circle"></i>{{ session('error') }}</div>@endif

<div class="stats-grid">
    <div class="stat-card"><div class="stat-label">Total Alunos</div><div class="stat-value">{{ $stats['total'] }}</div></div>
    <div class="stat-card"><div class="stat-label">Ativos</div><div class="stat-value" style="color:var(--accent-green)">{{ $stats['ativos'] }}</div></div>
    <div class="stat-card"><div class="stat-label">Com Turma</div><div class="stat-value" style="color:#60A5FA">{{ $stats['com_turma'] }}</div></div>
    <div class="stat-card"><div class="stat-label">Sem Turma</div><div class="stat-value" style="color:var(--accent-yellow)">{{ $stats['sem_turma'] }}</div></div>
</div>

<div class="page-header">
    <div class="page-title">Todos os Alunos</div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('admin.alunos.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Novo Aluno</a>
    @endif
</div>

<form method="GET" action="{{ route('admin.alunos.index') }}">
    <div class="filter-bar">
        <input type="text" name="search" class="search-input" placeholder="Pesquisar por nome, número..." value="{{ request('search') }}">
        <select name="turma_id" class="filter-select">
            <option value="">Todas as turmas</option>
            @foreach($turmas as $turma)
            <option value="{{ $turma->id }}" {{ request('turma_id') == $turma->id ? 'selected' : '' }}>{{ $turma->nome_turma }}</option>
            @endforeach
        </select>
        <select name="status" class="filter-select">
            <option value="">Todos os status</option>
            <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
            <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
        </select>
        <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Filtrar</button>
        @if(request()->hasAny(['search','turma_id','status']))
        <a href="{{ route('admin.alunos.index') }}" class="btn"><i class="fas fa-times"></i> Limpar</a>
        @endif
    </div>
</form>

<div class="table-card">
    <div class="table-header">
        <div class="table-title">Lista de Alunos</div>
        <div class="table-count">{{ $alunos->total() }} registro(s)</div>
    </div>
    @if($alunos->count() > 0)
    <table>
        <thead><tr><th>Aluno</th><th>Número</th><th>Turma</th><th>Contacto</th><th>Status</th><th style="text-align:right">Ações</th></tr></thead>
        <tbody>
            @foreach($alunos as $aluno)
            <tr>
                <td>
                    <div class="user-cell">
                        <div class="user-avatar">{{ strtoupper(substr($aluno->name, 0, 1)) }}</div>
                        <div><div class="user-name">{{ $aluno->name }}</div><div class="user-username">{{ $aluno->username }}</div></div>
                    </div>
                </td>
                <td style="color:var(--text-secondary);font-size:12px">{{ $aluno->numero ?? '—' }}</td>
                <td>
                    @if($aluno->turma)
                    <span class="tag tag-turma">{{ $aluno->turma->nome_turma }}</span>
                    @else
                    <span style="color:var(--text-secondary);font-size:12px">—</span>
                    @endif
                </td>
                <td style="font-size:12px">
                    @if($aluno->telefone)<div><i class="fas fa-phone" style="color:var(--text-secondary);margin-right:4px"></i>{{ $aluno->telefone }}</div>@endif
                </td>
                <td><span class="tag {{ $aluno->is_active ? 'tag-ativo' : 'tag-inativo' }}">{{ $aluno->is_active ? 'Ativo' : 'Inativo' }}</span></td>
                <td>
                    <div class="actions-cell" style="justify-content:flex-end">
                        <a href="{{ route('admin.alunos.show', $aluno) }}" class="btn-icon" title="Ver"><i class="fas fa-eye"></i></a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.alunos.edit', $aluno) }}" class="btn-icon" title="Editar"><i class="fas fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.alunos.toggle-status', $aluno) }}" style="display:inline">@csrf @method('PATCH')
                            <button type="submit" class="btn-icon" title="{{ $aluno->is_active ? 'Desativar' : 'Ativar' }}"><i class="fas fa-{{ $aluno->is_active ? 'ban' : 'check' }}"></i></button>
                        </form>
                        <form method="POST" action="{{ route('admin.alunos.destroy', $aluno) }}" style="display:inline" onsubmit="return confirm('Eliminar este aluno?');">@csrf @method('DELETE')
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
    <div class="empty-state"><i class="fas fa-user-graduate"></i><div>Nenhum aluno encontrado</div></div>
    @endif
</div>

@if($alunos->hasPages())<div class="pagination">{{ $alunos->links() }}</div>@endif
@endsection
