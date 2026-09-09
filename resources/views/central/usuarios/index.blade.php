@extends('layouts.app')

@section('title', 'Utilizadores da plataforma')
@section('page-title', 'Painel MiScool — Utilizadores da plataforma')

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;flex-wrap:wrap;gap:12px}
    .page-head-main{}
    .page-title{font-size:20px;font-weight:700}
    .page-sub{font-size:12px;color:var(--text-secondary);margin-top:3px}

    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn-icon{width:32px;height:32px;border-radius:6px;border:1px solid var(--border-color);background:var(--bg-card);color:var(--text-secondary);display:grid;place-items:center;cursor:pointer;transition:all .15s;text-decoration:none;font-size:12px}
    .btn-icon:hover{border-color:rgba(255,255,255,.18);color:var(--text-primary);background:var(--bg-hover)}
    .btn-icon.success:hover{border-color:rgba(34,197,94,.4);color:var(--accent-green);background:rgba(34,197,94,.08)}
    .btn-icon.danger:hover{border-color:rgba(239,68,68,.4);color:#FCA5A5;background:rgba(239,68,68,.08)}
    .btn-icon.warn:hover{border-color:rgba(251,191,36,.4);color:#FCD34D;background:rgba(251,191,36,.08)}

    .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:18px}
    .stat-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:16px 18px;display:flex;align-items:center;gap:14px}
    .stat-ic{width:38px;height:38px;border-radius:9px;display:grid;place-items:center;font-size:15px;flex-shrink:0}
    .stat-ic.green{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .stat-ic.purple{background:rgba(168,85,247,.14);color:#D8B4FE}
    .stat-ic.gray{background:rgba(148,163,184,.13);color:#CBD5E1}
    .stat-num{font-size:22px;font-weight:700;line-height:1}
    .stat-label{font-size:11px;color:var(--text-secondary);margin-top:4px}

    .card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:visible;margin-bottom:16px}
    .card-head{display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-bottom:1px solid var(--border-color)}
    .card-title{font-size:13px;font-weight:600}
    .table{width:100%;border-collapse:collapse}
    .table th{font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--text-secondary);text-align:left;padding:12px 16px;border-bottom:1px solid var(--border-color);white-space:nowrap}
    .table td{padding:12px 16px;border-bottom:1px solid var(--border-color);font-size:13px;vertical-align:middle}
    .table tr:last-child td{border-bottom:none}
    .table tr:hover td{background:var(--bg-hover)}

    .user-cell{display:flex;align-items:center;gap:12px}
    .avatar{width:36px;height:36px;border-radius:50%;display:grid;place-items:center;font-size:13px;font-weight:700;flex-shrink:0;color:#062915}
    .avatar.green{background:rgba(34,197,94,.85)}
    .avatar.purple{background:#D8B4FE}
    .avatar.gray{background:#CBD5E1}
    .user-nome{font-weight:600;line-height:1.3}
    .user-sub{font-size:11px;color:var(--text-secondary)}
    .mono{font-family:monospace;font-size:12px;color:#60A5FA}
    .cell-sec{color:var(--text-secondary);font-size:12px}
    .actions{display:flex;gap:6px;align-items:center;justify-content:flex-end}

    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap}
    .tag-ativa{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .tag-inativa{background:rgba(107,114,128,.15);color:var(--text-secondary)}
    .tag-você{background:rgba(168,85,247,.14);color:#D8B4FE}

    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:8px}
    .alert-error{padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;border-radius:8px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:8px}

    .chart-wrap{padding:18px;position:relative;min-height:260px}
    .pagin{margin:0;padding:14px 16px;border-top:1px solid var(--border-color)}
    @media(max-width:768px){.table{display:block;overflow-x:auto}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert-error"><i class="fas fa-exclamation-triangle"></i>{{ session('error') }}</div>@endif

<div class="page-header">
    <div class="page-head-main">
        <div class="page-title">Utilizadores da plataforma</div>
        <div class="page-sub">Equipa responsável pela manutenção da MiScool e registo de escolas.</div>
    </div>
    <a href="{{ route('central.usuarios.create') }}" class="btn-primary"><i class="fas fa-user-plus"></i> Novo Utilizador</a>
</div>

<div class="stats">
    <div class="stat-card">
        <div class="stat-ic purple"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-num">{{ $total }}</div>
            <div class="stat-label">Total de utilizadores</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ic green"><i class="fas fa-user-check"></i></div>
        <div>
            <div class="stat-num">{{ $ativos }}</div>
            <div class="stat-label">Contas ativas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-ic gray"><i class="fas fa-user-slash"></i></div>
        <div>
            <div class="stat-num">{{ $inativos }}</div>
            <div class="stat-label">Contas desativadas</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <div class="card-title"><i class="fas fa-address-book" style="margin-right:8px"></i>Lista de utilizadores</div>
    </div>
    @if($usuarios->isEmpty())
        <div style="text-align:center;padding:44px 20px;color:var(--text-secondary);font-size:12px">
            <i class="fas fa-users" style="font-size:32px;margin-bottom:12px;display:block;opacity:.3"></i>
            Ainda não há utilizadores da plataforma. Crie o primeiro para começar.
        </div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Nome completo</th>
                    <th>Nome de utilizador</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Estado</th>
                    <th>Criado em</th>
                    <th style="text-align:right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                    @php $eu = $usuario->id === auth()->id(); @endphp
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="avatar {{ $usuario->is_active ? 'green' : 'gray' }}">
                                    {{ strtoupper(substr(trim($usuario->name), 0, 1)) }}{{ strtoupper(substr(trim(strrchr($usuario->name, ' ') ?: ' ' . $usuario->name), 1, 1)) }}
                                </div>
                                <div>
<div class="user-nome">{{ $usuario->name }}</div>
                                @if($eu)
                                    <div class="user-sub"><span class="tag tag-você">sessão atual</span></div>
                                @endif
                                </div>
                            </div>
                        </td>
                        <td><span class="mono">{{ $usuario->username }}</span></td>
                        <td class="cell-sec">{{ $usuario->email ?: '—' }}</td>
                        <td class="cell-sec">{{ $usuario->telefone ?: '—' }}</td>
                        <td><span class="tag {{ $usuario->is_active ? 'tag-ativa' : 'tag-inativa' }}">{{ $usuario->is_active ? 'Ativo' : 'Desativado' }}</span></td>
                        <td class="cell-sec">{{ $usuario->created_at?->format('d/m/Y') }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('central.usuarios.show', $usuario) }}" class="btn-icon" title="Visualizar"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('central.usuarios.edit', $usuario) }}" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                                @if(!$eu)
                                    <form method="POST" action="{{ route('central.usuarios.toggle', $usuario) }}">
                                        @csrf
                                        <button type="submit" class="btn-icon {{ $usuario->is_active ? 'warn' : 'success' }}" title="{{ $usuario->is_active ? 'Desativar' : 'Reativar' }}">
                                            <i class="fas {{ $usuario->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('central.usuarios.destroy', $usuario) }}" onsubmit="return confirm('Eliminar o utilizador «{{ $usuario->name }}»? Esta ação é irreversível.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon danger" title="Apagar"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagin">{{ $usuarios->links() }}</div>
    @endif
</div>

<div class="card">
    <div class="card-head">
        <div class="card-title"><i class="fas fa-chart-bar" style="margin-right:8px"></i>Utilizadores criados por mês (últimos 12 meses)</div>
    </div>
    <div class="chart-wrap">
        <canvas id="graficoUtilizadores"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const labels = @json($labelsGrafico);
    const dados = @json($dadosGrafico);
    const ctx = document.getElementById('graficoUtilizadores').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Utilizadores criados',
                data: dados,
                backgroundColor: 'rgba(34,197,94,.75)',
                hoverBackgroundColor: 'rgba(34,197,94,1)',
                borderRadius: 6,
                maxBarThickness: 42
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: 'var(--text-secondary)' },
                    grid: { color: 'var(--border-color)' }
                },
                x: {
                    ticks: { color: 'var(--text-secondary)' },
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endsection