@extends('layouts.app')

@section('title', 'Departamentos')
@section('page-title', 'Gestão de Departamentos')

@section('content')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 20px;
        font-weight: 700;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 16px;
    }

    .stat-label {
        font-size: 10px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
    }

    .filter-bar {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .search-input {
        flex: 1;
        min-width: 200px;
        padding: 10px 14px;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        font-size: 13px;
        transition: border-color 0.15s;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--accent-green);
    }

    .search-input::placeholder {
        color: var(--text-secondary);
    }

    .filter-select {
        padding: 10px 14px;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        font-size: 13px;
        cursor: pointer;
        min-width: 140px;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--accent-green);
    }

    .btn-primary {
        background: var(--accent-green);
        color: #000;
        border: none;
        padding: 10px 18px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s;
        text-decoration: none;
    }

    .btn-primary:hover {
        background: #1ea34e;
    }

    .btn {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        padding: 10px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s;
    }

    .btn:hover {
        border-color: rgba(255, 255, 255, 0.15);
        background: var(--bg-hover);
    }

    .table-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        overflow: hidden;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .table-title {
        font-size: 14px;
        font-weight: 600;
    }

    .table-count {
        font-size: 12px;
        color: var(--text-secondary);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead th {
        text-align: left;
        padding: 12px 20px;
        font-size: 10px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 600;
        border-bottom: 1px solid var(--border-color);
        background: rgba(0, 0, 0, 0.2);
    }

    tbody tr {
        transition: background 0.15s;
    }

    tbody tr:hover {
        background: var(--bg-hover);
    }

    tbody td {
        padding: 12px 20px;
        font-size: 13px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
    }

    .dept-name {
        font-weight: 600;
        color: var(--text-primary);
    }

    .dept-sigla {
        font-size: 11px;
        color: var(--text-secondary);
        margin-left: 6px;
    }

    .tag {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .tag-ativo {
        background: rgba(34, 197, 94, 0.12);
        color: var(--accent-green);
    }

    .tag-inativo {
        background: rgba(239, 68, 68, 0.12);
        color: #FCA5A5;
    }

    .actions-cell {
        display: flex;
        gap: 6px;
    }

    .btn-icon {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        color: var(--text-secondary);
        display: grid;
        place-items: center;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
        font-size: 12px;
    }

    .btn-icon:hover {
        border-color: rgba(255, 255, 255, 0.15);
        color: var(--text-primary);
        background: var(--bg-hover);
    }

    .btn-icon.danger:hover {
        border-color: rgba(239, 68, 68, 0.3);
        color: #FCA5A5;
        background: rgba(239, 68, 68, 0.08);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-secondary);
        font-size: 12px;
    }

    .empty-state i {
        font-size: 32px;
        margin-bottom: 12px;
        display: block;
        opacity: 0.3;
    }

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

    .pagination {
        display: flex;
        justify-content: center;
        gap: 6px;
        margin-top: 20px;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.15s;
    }

    .pagination a {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
    }

    .pagination a:hover {
        border-color: rgba(255, 255, 255, 0.15);
        color: var(--text-primary);
    }

    .pagination .active {
        background: var(--accent-green);
        color: #000;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .filter-bar {
            flex-direction: column;
        }
    }
</style>

@if(session('success'))
<div class="alert-success">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Departamentos</div>
        <div class="stat-value">{{ $stats['total'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Ativos</div>
        <div class="stat-value" style="color: var(--accent-green);">{{ $stats['ativos'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Membros</div>
        <div class="stat-value" style="color: var(--accent-yellow);">{{ $stats['total_membros'] }}</div>
    </div>
</div>

<div class="page-header">
    <div class="page-title">Todos os Departamentos</div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('admin.departamentos.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> Novo Departamento
    </a>
    @endif
</div>

<form method="GET" action="{{ route('admin.departamentos.index') }}">
    <div class="filter-bar">
        <input type="text" name="search" class="search-input" placeholder="Pesquisar por nome ou sigla..." value="{{ request('search') }}">
        <select name="status" class="filter-select">
            <option value="">Todos os status</option>
            <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
            <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
        </select>
        <button type="submit" class="btn-primary">
            <i class="fas fa-search"></i> Filtrar
        </button>
    </div>
</form>

<div class="table-card">
    <div class="table-header">
        <div class="table-title">Lista de Departamentos</div>
        <div class="table-count">{{ $departamentos->total() }} registro(s)</div>
    </div>

    @if($departamentos->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Departamento</th>
                <th>Membros</th>
                <th>Descrição</th>
                <th>Status</th>
                <th style="text-align: right;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departamentos as $dept)
            <tr>
                <td>
                    <span class="dept-name">{{ $dept->nome }}</span>
                    @if($dept->sigla)
                    <span class="dept-sigla">({{ $dept->sigla }})</span>
                    @endif
                </td>
                <td>
                    <span style="color: var(--accent-yellow); font-weight: 600;">{{ $dept->users_count }}</span>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.departamentos.membros', $dept) }}" style="font-size: 11px; color: var(--text-secondary); margin-left: 6px; text-decoration: none;">
                        <i class="fas fa-users-cog"></i> gerir
                    </a>
                    @endif
                </td>
                <td style="color: var(--text-secondary); font-size: 12px; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    {{ $dept->descricao ?? '—' }}
                </td>
                <td>
                    <span class="tag {{ $dept->is_active ? 'tag-ativo' : 'tag-inativo' }}">
                        {{ $dept->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </td>
                <td>
                    <div class="actions-cell" style="justify-content: flex-end;">
                        <a href="{{ route('admin.departamentos.show', $dept) }}" class="btn-icon" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.departamentos.edit', $dept) }}" class="btn-icon" title="Editar">
                            <i class="fas fa-pen"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.departamentos.destroy', $dept) }}" style="display: inline;"
                              onsubmit="return confirm('Tem certeza que deseja eliminar este departamento?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon danger" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state">
        <i class="fas fa-building"></i>
        <div>Nenhum departamento encontrado</div>
    </div>
    @endif
</div>

@if($departamentos->hasPages())
<div class="pagination">
    {{ $departamentos->links() }}
</div>
@endif
@endsection
