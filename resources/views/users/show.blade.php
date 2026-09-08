@extends('layouts.app')

@section('title', 'Detalhes do Usuário')
@section('page-title', 'Perfil do Usuário')

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
        display: flex;
        align-items: center;
        gap: 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .profile-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: var(--accent-green);
        color: #000;
        display: grid;
        place-items: center;
        font-weight: 700;
        font-size: 28px;
        flex-shrink: 0;
    }

    .profile-name {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .profile-meta {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .tag {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .tag-admin { background: rgba(239, 68, 68, 0.12); color: #FCA5A5; }
    .tag-diretor { background: rgba(139, 92, 246, 0.12); color: #A78BFA; }
    .tag-financeiro { background: rgba(59, 130, 246, 0.12); color: #60A5FA; }
    .tag-professor { background: rgba(34, 197, 94, 0.12); color: var(--accent-green); }
    .tag-aluno { background: rgba(234, 179, 8, 0.12); color: var(--accent-yellow); }
    .tag-auxiliar { background: rgba(14, 165, 233, 0.12); color: #38BDF8; }
    .tag-pctp { background: rgba(236, 72, 153, 0.12); color: #F9A8D4; }
    .tag-encarregado { background: rgba(45, 212, 191, 0.12); color: #2DD4BF; }
    .tag-funcionario { background: rgba(168, 162, 158, 0.12); color: #A8A29E; }
    .tag-proprietario { background: rgba(168, 85, 247, 0.12); color: #D8B4FE; }
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

    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }

        .profile-header {
            flex-direction: column;
            text-align: center;
        }

        .profile-meta {
            justify-content: center;
        }
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
        <div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        <div>
            <div class="profile-name">{{ $user->name }}</div>
            <div class="profile-meta">
                <span class="tag tag-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                @foreach(array_filter($user->roles ?? []) as $extra)
                <span class="tag tag-{{ $extra }}">{{ ucfirst($extra) }}</span>
                @endforeach
                <span class="tag {{ $user->is_active ? 'tag-ativo' : 'tag-inativo' }}">
                    {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                </span>
                @if($user->numero)
                <span style="font-size: 12px; color: var(--text-secondary);">Nº {{ $user->numero }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="profile-section">
        <div class="section-title">Dados de Acesso</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Username</span>
                <span class="info-value">{{ $user->username }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $user->email ?? '—' }}</span>
            </div>
        </div>
    </div>

    <div class="profile-section">
        <div class="section-title">Dados Pessoais</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Telefone</span>
                <span class="info-value">{{ $user->telefone ?? '—' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Género</span>
                <span class="info-value">{{ $user->genero === 'M' ? 'Masculino' : ($user->genero === 'F' ? 'Feminino' : '—') }}</span>
            </div>
            <div class="info-item" style="grid-column: span 2;">
                <span class="info-label">Endereço</span>
                <span class="info-value">{{ $user->endereco ?? '—' }}</span>
            </div>
        </div>
    </div>

    <div class="profile-section">
        <div class="section-title">Dados Académicos</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Função</span>
                <span class="info-value">{{ ucfirst($user->role) }}</span>
                @if(!empty(array_filter($user->roles ?? [])))
                <span class="info-value" style="color: var(--text-secondary); font-size: 12px;">
                    + {{ implode(', ', array_map('ucfirst', array_filter($user->roles ?? []))) }}
                </span>
                @endif
            </div>
            <div class="info-item">
                <span class="info-label">Ano Lectivo</span>
                <span class="info-value">{{ $user->ano_lectivo ?? '—' }}</span>
            </div>
            @if($user->disciplina)
            <div class="info-item">
                <span class="info-label">Disciplina</span>
                <span class="info-value">{{ $user->disciplina }}</span>
            </div>
            @endif
            @if($user->turma)
            <div class="info-item">
                <span class="info-label">Turma</span>
                <span class="info-value">{{ $user->turma->nome_turma }} - {{ $user->turma->nivel }}</span>
            </div>
            @endif
            @if($user->nivel)
            <div class="info-item">
                <span class="info-label">Nível</span>
                <span class="info-value">{{ $user->nivel }}</span>
            </div>
            @endif
            @if($user->encarregado)
            <div class="info-item">
                <span class="info-label">Encarregado</span>
                <span class="info-value">{{ $user->encarregado->nome }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="profile-section">
        <div class="section-title">Sistema</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Criado em</span>
                <span class="info-value">{{ $user->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Última atualização</span>
                <span class="info-value">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>

    <div class="profile-actions">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn-primary">
            <i class="fas fa-pen"></i> Editar
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="margin-left: auto;"
              onsubmit="return confirm('Tem certeza que deseja eliminar este usuário?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </form>
    </div>
</div>
@endsection
