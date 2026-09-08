@extends('layouts.app')

@section('title', 'Atividades')
@section('page-title', 'Registo de Atividades')

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
    .page-title{font-size:20px;font-weight:700}
    .filter-bar{display:grid;grid-template-columns:1fr 220px 220px 220px 220px auto auto;gap:12px;margin-bottom:20px;align-items:end}
    .search-input{padding:10px 14px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s}
    .search-input:focus{outline:none;border-color:var(--accent-green)}
    .search-input::placeholder{color:var(--text-secondary)}
    .filter-select{padding:10px 14px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;cursor:pointer;min-width:0}
    .filter-select:focus{outline:none;border-color:var(--accent-green)}
    .filter-label{font-size:10px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-secondary);font-weight:600;margin-bottom:6px;display:block}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 16px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 16px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15)}
    .card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:hidden}
    table{width:100%;border-collapse:collapse}
    thead th{text-align:left;padding:12px 18px;font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-secondary);border-bottom:1px solid var(--border-color);font-weight:600}
    tbody td{padding:13px 18px;border-bottom:1px solid rgba(255,255,255,.03);font-size:13px;vertical-align:middle}
    tbody tr:hover{background:rgba(255,255,255,.015)}
    tbody tr:last-child td{border-bottom:none}
    .user-badge{display:flex;align-items:center;gap:10px}
    .avatar{width:34px;height:34px;border-radius:50%;background:var(--accent-yellow);color:#000;display:grid;place-items:center;font-weight:700;font-size:13px;flex-shrink:0}
    .avatar.no-user{background:rgba(255,255,255,.08);color:var(--text-secondary)}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap}
    .tag-login{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .tag-logout{background:rgba(139,92,246,.12);color:#A78BFA}
    .tag-create{background:rgba(59,130,246,.12);color:#60A5FA}
    .tag-update{background:rgba(234,179,8,.12);color:var(--accent-yellow)}
    .tag-delete{background:rgba(239,68,68,.12);color:#FCA5A5}
    .tag-outro{background:rgba(107,114,128,.12);color:var(--text-secondary)}
    .time{color:var(--text-secondary);font-size:12px;white-space:nowrap}
    .ip{color:var(--text-secondary);font-size:11px}
    .empty-state{text-align:center;padding:50px 20px;color:var(--text-secondary);font-size:12px}
    .empty-state i{font-size:32px;margin-bottom:12px;display:block;opacity:.3}
    .pagination{display:flex;justify-content:center;gap:6px;margin-top:20px}
    .pagination a,.pagination span{padding:8px 12px;border-radius:6px;font-size:13px;text-decoration:none;transition:all .15s}
    .pagination a{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-secondary)}
    .pagination a:hover{border-color:rgba(255,255,255,.15);color:var(--text-primary)}
    .pagination .active{background:var(--accent-green);color:#000;font-weight:600}
    @media(max-width:1100px){.filter-bar{grid-template-columns:1fr 1fr}}
</style>

<div class="page-header">
    <div class="page-title">Registo de Atividades</div>
    <span style="font-size:12px;color:var(--text-secondary)"><i class="fas fa-shield-alt" style="margin-right:6px"></i>Só o administrador vê quem fez, o quê e quando.</span>
</div>

<form method="GET" action="{{ route('admin.atividades.index') }}">
    <div class="filter-bar">
        <div>
            <label class="filter-label">Pesquisar na descrição</label>
            <input type="text" name="descricao" class="search-input" placeholder="Ex.: criou, eliminou, pagamento..." value="{{ request('descricao') }}">
        </div>
        <div>
            <label class="filter-label">Utilizador</label>
            <select name="user_id" class="filter-select">
                <option value="">Todos</option>
                @foreach($utilizadores as $u)
                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="filter-label">Tipo de atividade</label>
            <select name="tipo" class="filter-select">
                <option value="">Todos</option>
                @foreach($tipos as $key => $label)
                <option value="{{ $key }}" {{ request('tipo') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="filter-label">De</label>
            <input type="date" name="data_de" class="search-input" value="{{ request('data_de') }}">
        </div>
        <div>
            <label class="filter-label">Até</label>
            <input type="date" name="data_ate" class="search-input" value="{{ request('data_ate') }}">
        </div>
        <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Filtrar</button>
        @if(request()->hasAny(['descricao','user_id','tipo','data_de','data_ate']))
        <a href="{{ route('admin.atividades.index') }}" class="btn"><i class="fas fa-times"></i> Limpar</a>
        @endif
    </div>
</form>

@if($atividades->count() > 0)
<div class="card">
    <table>
        <thead>
            <tr>
                <th>Quem</th>
                <th>Tipo</th>
                <th>Atividade</th>
                <th>Quando</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($atividades as $atividade)
            <tr>
                <td>
                    <div class="user-badge">
                        @if($atividade->user)
                        <div class="avatar">{{ mb_strtoupper(mb_substr($atividade->user->name, 0, 1)) }}</div>
                        <div>
                            <div style="font-weight:600">{{ $atividade->user->name }}</div>
                            <div style="font-size:11px;color:var(--text-secondary)">{{ $atividade->user->username }}</div>
                        </div>
                        @else
                        <div class="avatar no-user"><i class="fas fa-user-slash"></i></div>
                        <div style="color:var(--text-secondary)">Utilizador removido</div>
                        @endif
                    </div>
                </td>
                <td><span class="tag tag-{{ $atividade->tipo }}">{{ $tipos[$atividade->tipo] ?? ucfirst($atividade->tipo) }}</span></td>
                <td style="max-width:420px">{{ $atividade->descricao }}
                    @if($atividade->modelo)
                    <div style="font-size:10px;color:var(--text-secondary);margin-top:2px">{{ class_basename($atividade->modelo) }} #{{ $atividade->modelo_id }}</div>
                    @endif
                </td>
                <td class="time" title="{{ $atividade->created_at->format('d/m/Y H:i:s') }}">
                    <div>{{ $atividade->created_at->format('d/m/Y H:i') }}</div>
                    <div style="font-size:11px">{{ $atividade->created_at->diffForHumans() }}</div>
                </td>
                <td class="ip">{{ $atividade->ip ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if($atividades->hasPages())<div class="pagination">{{ $atividades->links() }}</div>@endif
@else
<div class="card">
    <div class="empty-state"><i class="fas fa-history"></i><div>Nenhuma atividade registada com estes filtros.</div></div>
</div>
@endif
@endsection