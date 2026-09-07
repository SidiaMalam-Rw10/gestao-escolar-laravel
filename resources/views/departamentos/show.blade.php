@extends('layouts.app')

@section('title', 'Detalhes do Departamento')
@section('page-title', 'Departamento')

@section('content')
<style>
    .profile-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        overflow: hidden;
        max-width: 720px;
    }

    .profile-header {
        padding: 28px;
        border-bottom: 1px solid var(--border-color);
    }

    .profile-name {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .profile-sigla {
        font-size: 13px;
        color: var(--text-secondary);
        margin-bottom: 8px;
    }

    .profile-meta {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .tag {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .tag-ativo { background: rgba(34, 197, 94, 0.12); color: var(--accent-green); }
    .tag-inativo { background: rgba(239, 68, 68, 0.12); color: #FCA5A5; }

    .profile-section {
        padding: 20px 28px;
    }

    .profile-section + .profile-section {
        border-top: 1px solid var(--border-color);
    }

    .section-title {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-secondary);
        margin-bottom: 14px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .info-label {
        font-size: 11px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .info-value {
        font-size: 13px;
        color: var(--text-primary);
    }

    .member-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .member-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
    }

    .member-item:last-child {
        border-bottom: none;
    }

    .member-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .member-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--accent-green);
        color: #000;
        display: grid;
        place-items: center;
        font-weight: 700;
        font-size: 11px;
    }

    .member-name {
        font-size: 13px;
        font-weight: 500;
    }

    .member-role {
        font-size: 11px;
        color: var(--text-secondary);
    }

    .tag-cargo {
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 600;
        background: rgba(59, 130, 246, 0.12);
        color: #60A5FA;
    }

    .tag-principal {
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 600;
        background: rgba(34, 197, 94, 0.12);
        color: var(--accent-green);
    }

    .profile-actions {
        padding: 20px 28px;
        border-top: 1px solid var(--border-color);
        display: flex;
        gap: 12px;
    }

    .btn-primary {
        background: var(--accent-green);
        color: #000;
        border: none;
        padding: 10px 24px;
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

    .btn-danger {
        background: rgba(239, 68, 68, 0.08);
        border-color: rgba(239, 68, 68, 0.2);
        color: #FCA5A5;
    }

    .btn-danger:hover {
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.3);
    }

    .empty-state {
        text-align: center;
        padding: 20px;
        color: var(--text-secondary);
        font-size: 12px;
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
</style>

@if(session('success'))
<div class="alert-success">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

<div class="profile-card">
    <div class="profile-header">
        <div class="profile-name">{{ $departamento->nome }}</div>
        @if($departamento->sigla)
        <div class="profile-sigla">Sigla: {{ $departamento->sigla }}</div>
        @endif
        <div class="profile-meta">
            <span class="tag {{ $departamento->is_active ? 'tag-ativo' : 'tag-inativo' }}">
                {{ $departamento->is_active ? 'Ativo' : 'Inativo' }}
            </span>
            <span style="font-size: 12px; color: var(--text-secondary);">
                {{ $departamento->users->count() }} membro(s)
            </span>
        </div>
    </div>

    @if($departamento->descricao)
    <div class="profile-section">
        <div class="section-title">Descrição</div>
        <p style="font-size: 13px; color: var(--text-secondary);">{{ $departamento->descricao }}</p>
    </div>
    @endif

    <div class="profile-section">
        <div class="section-title">Membros</div>
        @if($departamento->users->count() > 0)
        <ul class="member-list">
            @foreach($departamento->users as $member)
            <li class="member-item">
                <div class="member-info">
                    <div class="member-avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                    <div>
                        <div class="member-name">{{ $member->name }}</div>
                        <div class="member-role">{{ ucfirst($member->role) }}</div>
                    </div>
                </div>
                <div style="display: flex; gap: 6px; align-items: center;">
                    @if($member->pivot->cargo)
                    <span class="tag-cargo">{{ $member->pivot->cargo }}</span>
                    @endif
                    <span style="font-size: 10px; color: var(--text-secondary); padding: 3px 8px; background: var(--bg-hover); border-radius: 10px;">
                        {{ ucfirst($member->pivot->regime) }}
                    </span>
                    @if($member->pivot->is_principal)
                    <span class="tag-principal">Principal</span>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
        @else
        <div class="empty-state">
            <i class="fas fa-user-slash" style="opacity: 0.3; font-size: 20px; margin-bottom: 8px;"></i>
            <div>Nenhum membro neste departamento</div>
        </div>
        @endif
    </div>

    <div class="profile-actions">
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.departamentos.membros', $departamento) }}" class="btn-primary">
            <i class="fas fa-user-plus"></i> Gerir Membros
        </a>
        @endif
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.departamentos.edit', $departamento) }}" class="btn">
            <i class="fas fa-pen"></i> Editar
        </a>
        @endif
        <a href="{{ route('admin.departamentos.index') }}" class="btn">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>
@endsection
