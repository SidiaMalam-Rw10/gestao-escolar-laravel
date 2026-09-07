@extends('layouts.app')

@section('title', 'Detalhes do Professor')
@section('page-title', 'Perfil do Professor')

@section('content')
<style>
    .profile-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:hidden;max-width:720px}
    .profile-header{padding:28px;display:flex;align-items:center;gap:20px;border-bottom:1px solid var(--border-color)}
    .profile-avatar{width:72px;height:72px;border-radius:50%;background:var(--accent-green);color:#000;display:grid;place-items:center;font-weight:700;font-size:28px;flex-shrink:0}
    .profile-name{font-size:20px;font-weight:700;margin-bottom:4px}
    .profile-meta{display:flex;gap:12px;align-items:center;flex-wrap:wrap}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600}
    .tag-ativo{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .tag-inativo{background:rgba(239,68,68,.12);color:#FCA5A5}
    .tag-disc{background:rgba(139,92,246,.12);color:#A78BFA}
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
    .empty-state{text-align:center;padding:20px;color:var(--text-secondary);font-size:12px}
    @media(max-width:768px){.info-grid{grid-template-columns:1fr}.profile-header{flex-direction:column;text-align:center}.profile-meta{justify-content:center}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif

<div class="profile-card">
    <div class="profile-header">
        <div class="profile-avatar">{{ strtoupper(substr($professor->name, 0, 1)) }}</div>
        <div>
            <div class="profile-name">{{ $professor->name }}</div>
            <div class="profile-meta">
                <span class="tag {{ $professor->is_active ? 'tag-ativo' : 'tag-inativo' }}">{{ $professor->is_active ? 'Ativo' : 'Inativo' }}</span>
                @if($professor->disciplina)<span class="tag tag-disc">{{ $professor->disciplina }}</span>@endif
            </div>
        </div>
    </div>

    <div class="profile-section">
        <div class="section-title">Dados de Acesso</div>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Username</span><span class="info-value">{{ $professor->username }}</span></div>
            <div class="info-item"><span class="info-label">Email</span><span class="info-value">{{ $professor->email ?? '—' }}</span></div>
        </div>
    </div>

    <div class="profile-section">
        <div class="section-title">Dados Pessoais</div>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Telefone</span><span class="info-value">{{ $professor->telefone ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Género</span><span class="info-value">{{ $professor->genero === 'M' ? 'Masculino' : ($professor->genero === 'F' ? 'Feminino' : '—') }}</span></div>
            <div class="info-item" style="grid-column:span 2"><span class="info-label">Endereço</span><span class="info-value">{{ $professor->endereco ?? '—' }}</span></div>
        </div>
    </div>

    <div class="profile-section">
        <div class="section-title">Dados Profissionais</div>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Disciplina</span><span class="info-value">{{ $professor->disciplina ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Ano Lectivo</span><span class="info-value">{{ $professor->ano_lectivo ?? '—' }}</span></div>
        </div>
    </div>

    @if($professor->turmasResponsavel->count() > 0)
    <div class="profile-section">
        <div class="section-title">Turmas Responsável</div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            @foreach($professor->turmasResponsavel as $turma)
            <span class="tag tag-disc">{{ $turma->nome_turma }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <div class="profile-section">
        <div class="section-title">Sistema</div>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Criado em</span><span class="info-value">{{ $professor->created_at->format('d/m/Y H:i') }}</span></div>
            <div class="info-item"><span class="info-label">Última atualização</span><span class="info-value">{{ $professor->updated_at->format('d/m/Y H:i') }}</span></div>
        </div>
    </div>

    <div class="profile-actions">
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.professores.edit', $professor) }}" class="btn-primary"><i class="fas fa-pen"></i> Editar</a>
        @endif
        <a href="{{ route('admin.professores.index') }}" class="btn"><i class="fas fa-arrow-left"></i> Voltar</a>
        @if(auth()->user()->isAdmin())
        <form method="POST" action="{{ route('admin.professores.destroy', $professor) }}" style="margin-left:auto" onsubmit="return confirm('Eliminar este professor?');">@csrf @method('DELETE')
            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Eliminar</button>
        </form>
        @endif
    </div>
</div>
@endsection
