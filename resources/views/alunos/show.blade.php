@extends('layouts.app')

@section('title', 'Detalhes do Aluno')
@section('page-title', 'Perfil do Aluno')

@section('content')
<style>
    .profile-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:hidden;max-width:720px}
    .profile-header{padding:28px;display:flex;align-items:center;gap:20px;border-bottom:1px solid var(--border-color)}
    .profile-avatar{width:72px;height:72px;border-radius:50%;background:var(--accent-yellow);color:#000;display:grid;place-items:center;font-weight:700;font-size:28px;flex-shrink:0}
    .profile-name{font-size:20px;font-weight:700;margin-bottom:4px}
    .profile-meta{display:flex;gap:12px;align-items:center;flex-wrap:wrap}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600}
    .tag-ativo{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .tag-inativo{background:rgba(239,68,68,.12);color:#FCA5A5}
    .tag-turma{background:rgba(59,130,246,.12);color:#60A5FA}
    .profile-section{padding:20px 28px}
    .profile-section+.profile-section{border-top:1px solid var(--border-color)}
    .section-title{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:var(--text-secondary);margin-bottom:14px}
    .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .info-item{display:flex;flex-direction:column;gap:2px}
    .info-label{font-size:11px;color:var(--text-secondary);font-weight:500}
    .info-value{font-size:13px;color:var(--text-primary)}
    .profile-actions{padding:20px 28px;border-top:1px solid var(--border-color);display:flex;gap:12px}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 24px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .btn-danger{background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.2);color:#FCA5A5}
    .btn-danger:hover{background:rgba(239,68,68,.15);border-color:rgba(239,68,68,.3)}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    @media(max-width:768px){.info-grid{grid-template-columns:1fr}.profile-header{flex-direction:column;text-align:center}.profile-meta{justify-content:center}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif

<div class="profile-card">
    <div class="profile-header">
        <div class="profile-avatar">{{ strtoupper(substr($aluno->name, 0, 1)) }}</div>
        <div>
            <div class="profile-name">{{ $aluno->name }}</div>
            <div class="profile-meta">
                <span class="tag {{ $aluno->is_active ? 'tag-ativo' : 'tag-inativo' }}">{{ $aluno->is_active ? 'Ativo' : 'Inativo' }}</span>
                @if($aluno->turma)<span class="tag tag-turma">{{ $aluno->turma->nome_turma }}</span>@endif
                @if($aluno->numero)<span style="font-size:12px;color:var(--text-secondary)">Nº {{ $aluno->numero }}</span>@endif
            </div>
        </div>
    </div>

    <div class="profile-section">
        <div class="section-title">Dados de Acesso</div>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Username</span><span class="info-value">{{ $aluno->username }}</span></div>
            <div class="info-item"><span class="info-label">Email</span><span class="info-value">{{ $aluno->email ?? '—' }}</span></div>
        </div>
    </div>

    <div class="profile-section">
        <div class="section-title">Dados Pessoais</div>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Telefone</span><span class="info-value">{{ $aluno->telefone ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Género</span><span class="info-value">{{ $aluno->genero === 'M' ? 'Masculino' : ($aluno->genero === 'F' ? 'Feminino' : '—') }}</span></div>
            <div class="info-item" style="grid-column:span 2"><span class="info-label">Endereço</span><span class="info-value">{{ $aluno->endereco ?? '—' }}</span></div>
        </div>
    </div>

    <div class="profile-section">
        <div class="section-title">Dados Académicos</div>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Nº Estudante</span><span class="info-value">{{ $aluno->numero ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Ano Lectivo</span><span class="info-value">{{ $aluno->ano_lectivo ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Turma</span><span class="info-value">{{ $aluno->turma ? $aluno->turma->nome_turma . ' - ' . $aluno->turma->nivel : '—' }}</span></div>
            <div class="info-item"><span class="info-label">Nível</span><span class="info-value">{{ $aluno->nivel ?? '—' }}</span></div>
            @if($aluno->encarregado)
            <div class="info-item"><span class="info-label">Encarregado</span><span class="info-value"><a href="{{ route('admin.encarregados.show', $aluno->encarregado) }}" style="color:var(--accent-green);text-decoration:none">{{ $aluno->encarregado->nome }}</a></span></div>
            <div class="info-item"><span class="info-label">Telefone Enc.</span><span class="info-value">{{ $aluno->encarregado->telefone ?? '—' }}</span></div>
            @endif
        </div>
    </div>

    <div class="profile-section">
        <div class="section-title">Sistema</div>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Criado em</span><span class="info-value">{{ $aluno->created_at->format('d/m/Y H:i') }}</span></div>
            <div class="info-item"><span class="info-label">Última atualização</span><span class="info-value">{{ $aluno->updated_at->format('d/m/Y H:i') }}</span></div>
        </div>
    </div>

    <div class="profile-actions">
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.alunos.edit', $aluno) }}" class="btn-primary"><i class="fas fa-pen"></i> Editar</a>
        @endif
        <a href="{{ route('admin.alunos.index') }}" class="btn"><i class="fas fa-arrow-left"></i> Voltar</a>
        @if(auth()->user()->isAdmin())
        <form method="POST" action="{{ route('admin.alunos.destroy', $aluno) }}" style="margin-left:auto" onsubmit="return confirm('Eliminar este aluno?');">@csrf @method('DELETE')
            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Eliminar</button>
        </form>
        @endif
    </div>
</div>
@endsection
