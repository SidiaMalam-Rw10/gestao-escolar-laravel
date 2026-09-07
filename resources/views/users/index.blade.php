@extends('layouts.app')

@section('title', 'Usuários')
@section('page-title', 'Gestão de Usuários')

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

    .btn-primary i {
        font-size: 11px;
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

    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--accent-green);
        color: #000;
        display: grid;
        place-items: center;
        font-weight: 700;
        font-size: 12px;
        flex-shrink: 0;
    }

    .user-info {
        line-height: 1.3;
    }

    .user-name {
        font-weight: 600;
        color: var(--text-primary);
    }

    .user-username {
        font-size: 11px;
        color: var(--text-secondary);
    }

    .tag {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .tag-admin {
        background: rgba(239, 68, 68, 0.12);
        color: #FCA5A5;
    }

    .tag-diretor {
        background: rgba(139, 92, 246, 0.12);
        color: #A78BFA;
    }

    .tag-financeiro {
        background: rgba(59, 130, 246, 0.12);
        color: #60A5FA;
    }

    .tag-professor {
        background: rgba(34, 197, 94, 0.12);
        color: var(--accent-green);
    }

    .tag-aluno {
        background: rgba(234, 179, 8, 0.12);
        color: var(--accent-yellow);
    }

    .tag-auxiliar {
        background: rgba(14, 165, 233, 0.12);
        color: #38BDF8;
    }

    .tag-pctp {
        background: rgba(236, 72, 153, 0.12);
        color: #F9A8D4;
    }

    .tag-encarregado {
        background: rgba(45, 212, 191, 0.12);
        color: #2DD4BF;
    }

    .tag-funcionario {
        background: rgba(168, 162, 158, 0.12);
        color: #A8A29E;
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

    .pagination .disabled {
        opacity: 0.4;
        pointer-events: none;
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

    .alert-error {
        padding: 12px 16px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #FCA5A5;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .filter-bar {
            flex-direction: column;
        }

        table {
            font-size: 12px;
        }

        thead th,
        tbody td {
            padding: 10px 12px;
        }
    }
</style>

@if(session('success'))
<div class="alert-success">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert-error">
    <i class="fas fa-exclamation-circle"></i>
    {{ session('error') }}
</div>
@endif

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total</div>
        <div class="stat-value">{{ $stats['total'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Ativos</div>
        <div class="stat-value" style="color: var(--accent-green);">{{ $stats['ativos'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Admins</div>
        <div class="stat-value" style="color: #FCA5A5;">{{ $stats['admin'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Professores</div>
        <div class="stat-value" style="color: var(--accent-green);">{{ $stats['professores'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Alunos</div>
        <div class="stat-value" style="color: var(--accent-yellow);">{{ $stats['alunos'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Auxiliares</div>
        <div class="stat-value" style="color: #38BDF8;">{{ $stats['auxiliares'] }}</div>
    </div>
</div>

<div class="page-header">
    <div class="page-title">Todos os Usuários</div>
    <a href="{{ route('admin.users.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> Novo Usuário
    </a>
</div>

<form method="GET" action="{{ route('admin.users.index') }}">
    <div class="filter-bar">
        <input type="text" name="search" class="search-input" placeholder="Pesquisar por nome, usuário, email..." value="{{ request('search') }}">
        <select name="role" class="filter-select">
            <option value="">Todas as funções</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="diretor" {{ request('role') === 'diretor' ? 'selected' : '' }}>Diretor</option>
            <option value="financeiro" {{ request('role') === 'financeiro' ? 'selected' : '' }}>Financeiro</option>
            <option value="professor" {{ request('role') === 'professor' ? 'selected' : '' }}>Professor</option>
            <option value="aluno" {{ request('role') === 'aluno' ? 'selected' : '' }}>Aluno</option>
            <option value="auxiliar" {{ request('role') === 'auxiliar' ? 'selected' : '' }}>Auxiliar</option>
            <option value="pctp" {{ request('role') === 'pctp' ? 'selected' : '' }}>PCTP</option>
            <option value="encarregado" {{ request('role') === 'encarregado' ? 'selected' : '' }}>Encarregado</option>
            <option value="funcionario" {{ request('role') === 'funcionario' ? 'selected' : '' }}>Funcionário</option>
        </select>
        <select name="status" class="filter-select">
            <option value="">Todos os status</option>
            <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
            <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
        </select>
        <button type="submit" class="btn-primary">
            <i class="fas fa-search"></i> Filtrar
        </button>
        @if(request()->hasAny(['search', 'role', 'status']))
        <a href="{{ route('admin.users.index') }}" class="btn" style="margin: 0;">
            <i class="fas fa-times"></i> Limpar
        </a>
        @endif
    </div>
</form>

<div class="table-card">
    <div class="table-header">
        <div class="table-title">Lista de Usuários</div>
        <div class="table-count">{{ $users->total() }} registro(s)</div>
    </div>

    @if($users->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Usuário</th>
                <th>Função</th>
                <th>Contacto</th>
                <th>Status</th>
                <th>Criado em</th>
                <th style="text-align: right;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>
                    <div class="user-cell">
                        <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                        <div class="user-info">
                            <div class="user-name">{{ $user->name }}</div>
                            <div class="user-username">{{ $user->username }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="tag tag-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                    @if(!empty(array_filter($user->roles ?? [])))
                    @foreach(array_filter($user->roles ?? []) as $extra)
                    <span class="tag tag-{{ $extra }}" style="margin-left: 4px;">{{ ucfirst($extra) }}</span>
                    @endforeach
                    @endif
                </td>
                <td>
                    <div style="font-size: 12px;">
                        @if($user->telefone)
                        <div><i class="fas fa-phone" style="color: var(--text-secondary); margin-right: 4px;"></i> {{ $user->telefone }}</div>
                        @endif
                        @if($user->email)
                        <div><i class="fas fa-envelope" style="color: var(--text-secondary); margin-right: 4px;"></i> {{ $user->email }}</div>
                        @endif
                    </div>
                </td>
                <td>
                    <span class="tag {{ $user->is_active ? 'tag-ativo' : 'tag-inativo' }}">
                        {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </td>
                <td style="color: var(--text-secondary); font-size: 12px;">
                    {{ $user->created_at->format('d/m/Y') }}
                </td>
                <td>
                    <div class="actions-cell" style="justify-content: flex-end;">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn-icon" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-icon" title="Editar">
                            <i class="fas fa-pen"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-icon" title="{{ $user->is_active ? 'Desativar' : 'Ativar' }}">
                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;"
                              onsubmit="return confirm('Tem certeza que deseja eliminar este usuário?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon danger" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <div>Nenhum usuário encontrado</div>
    </div>
    @endif
</div>

@if($users->hasPages())
<div class="pagination">
    {{ $users->links() }}
</div>
@endif
@endsection
