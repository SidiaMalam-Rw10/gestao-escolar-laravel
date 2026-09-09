@extends('layouts.app')

@section('title', 'Utilizador ' . $usuario->name)
@section('page-title', 'Painel MiScool — Utilizador')

@section('content')
<style>
    .page-title{font-size:20px;font-weight:700;margin-bottom:20px}
    .profile-grid{display:grid;grid-template-columns:340px 1fr;gap:18px;align-items:start}
    .card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:visible;margin-bottom:16px}
    .card-head{display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-bottom:1px solid var(--border-color)}
    .card-title{font-size:13px;font-weight:600}
    .avatar{width:72px;height:72px;border-radius:50%;display:grid;place-items:center;font-size:24px;font-weight:700;flex-shrink:0;color:#062915}
    .avatar.green{background:rgba(34,197,94,.85)}
    .avatar.gray{background:#CBD5E1}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap}
    .tag-ativa{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .tag-inativa{background:rgba(107,114,128,.15);color:var(--text-secondary)}
    .tag-perfil{background:rgba(168,85,247,.14);color:#D8B4FE}
    .info-row{display:flex;align-items:flex-start;gap:12px;padding:13px 18px;border-bottom:1px solid var(--border-color);font-size:13px}
    .info-row:last-child{border-bottom:none}
    .info-ic{width:32px;height:32px;border-radius:8px;display:grid;place-items:center;background:var(--bg-hover);color:var(--text-secondary);font-size:13px;flex-shrink:0}
    .info-label{font-size:11px;color:var(--text-secondary);margin-bottom:2px}
    .info-value{font-weight:500;word-break:break-word}
    .actions{display:flex;gap:8px;flex-wrap:wrap}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:9px 15px;border-radius:6px;cursor:pointer;font-size:12px;text-decoration:none;display:inline-flex;align-items:center;gap:7px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn-danger{background:transparent;border:1px solid rgba(239,68,68,.3);color:#FCA5A5;padding:9px 15px;border-radius:6px;cursor:pointer;font-size:12px;display:inline-flex;align-items:center;gap:7px;transition:all .15s}
    .btn-danger:hover{background:rgba(239,68,68,.1)}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .alert-error{padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .mono{font-family:monospace;font-size:12px;color:#60A5FA}
    @media(max-width:900px){.profile-grid{grid-template-columns:1fr}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert-error"><i class="fas fa-exclamation-triangle"></i>{{ session('error') }}</div>@endif

<div class="page-title">Utilizador da plataforma</div>

<div class="profile-grid">
    <div class="card">
        <div style="text-align:center;padding:26px 20px;border-bottom:1px solid var(--border-color)">
            <div class="avatar {{ $usuario->is_active ? 'green' : 'gray' }}" style="margin:0 auto 12px">
                {{ strtoupper(substr(trim($usuario->name), 0, 1)) }}{{ strtoupper(substr(trim(strrchr($usuario->name, ' ') ?: ' ' . $usuario->name), 1, 1)) }}
            </div>
            <div style="font-size:16px;font-weight:700">{{ $usuario->name }}</div>
            <div style="font-size:12px;color:var(--text-secondary);margin-top:4px">
                <span class="tag tag-perfil">Plataforma</span>
                <span class="tag {{ $usuario->is_active ? 'tag-ativa' : 'tag-inativa' }}" style="margin-left:4px">{{ $usuario->is_active ? 'Ativo' : 'Desativado' }}</span>
            </div>
        </div>
        <div style="padding:16px 18px;display:flex;flex-direction:column;gap:8px">
            <a href="{{ route('central.usuarios.edit', $usuario) }}" class="btn" style="justify-content:center"><i class="fas fa-edit"></i> Editar utilizador</a>
            @if($usuario->id !== auth()->id())
            <form method="POST" action="{{ route('central.usuarios.toggle', $usuario) }}">
                @csrf
                <button type="submit" class="btn {{ $usuario->is_active ? '' : '' }}" style="width:100%;justify-content:center">
                    <i class="fas {{ $usuario->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                    {{ $usuario->is_active ? 'Desativar conta' : 'Reativar conta' }}
                </button>
            </form>
            <form method="POST" action="{{ route('central.usuarios.destroy', $usuario) }}" onsubmit="return confirm('Eliminar o utilizador «{{ $usuario->name }}»? Esta ação é irreversível.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger" style="width:100%;justify-content:center"><i class="fas fa-trash"></i> Apagar utilizador</button>
            </form>
            @endif
            <a href="{{ route('central.usuarios.index') }}" class="btn" style="justify-content:center"><i class="fas fa-arrow-left"></i> Voltar à lista</a>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><div class="card-title"><i class="fas fa-id-card" style="margin-right:8px"></i>Informações da conta</div></div>
        <div class="info-row">
            <div class="info-ic"><i class="fas fa-user"></i></div>
            <div style="flex:1">
                <div class="info-label">Nome completo</div>
                <div class="info-value">{{ $usuario->name }}</div>
            </div>
        </div>
        <div class="info-row">
            <div class="info-ic"><i class="fas fa-at"></i></div>
            <div style="flex:1">
                <div class="info-label">Nome de utilizador</div>
                <div class="info-value"><span class="mono">{{ $usuario->username }}</span></div>
            </div>
        </div>
        <div class="info-row">
            <div class="info-ic"><i class="fas fa-envelope"></i></div>
            <div style="flex:1">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $usuario->email ?: '—' }}</div>
            </div>
        </div>
        <div class="info-row">
            <div class="info-ic"><i class="fas fa-phone"></i></div>
            <div style="flex:1">
                <div class="info-label">Número de telefone</div>
                <div class="info-value">{{ $usuario->telefone ?: '—' }}</div>
            </div>
        </div>
        <div class="info-row">
            <div class="info-ic"><i class="fas fa-user-tag"></i></div>
            <div style="flex:1">
                <div class="info-label">Perfil</div>
                <div class="info-value">Proprietário da plataforma (Painel MiScool)</div>
            </div>
        </div>
        <div class="info-row">
            <div class="info-ic"><i class="fas fa-calendar-alt"></i></div>
            <div style="flex:1">
                <div class="info-label">Conta criada em</div>
                <div class="info-value">{{ $usuario->created_at?->format('d/m/Y') }}</div>
            </div>
        </div>
        <div class="info-row">
            <div class="info-ic"><i class="fas fa-clock"></i></div>
            <div style="flex:1">
                <div class="info-label">Última atualização</div>
                <div class="info-value">{{ $usuario->updated_at?->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection