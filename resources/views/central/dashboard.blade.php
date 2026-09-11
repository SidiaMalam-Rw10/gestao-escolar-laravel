@extends('layouts.app')

@section('title', 'Painel ' . \App\Models\Configuracao::plataformaNome())
@section('page-title', 'Painel ' . \App\Models\Configuracao::plataformaNome() . ' — Visão geral')

@section('content')
<style>
    .welcome-banner{position:relative;border-radius:12px;overflow:hidden;margin-bottom:24px;border:1px solid var(--border-color);min-height:200px;display:flex;align-items:center}
    .welcome-banner .welcome-bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
    .welcome-banner .welcome-overlay{position:absolute;inset:0;background:linear-gradient(90deg,rgba(11,15,13,.92) 0%,rgba(11,15,13,.55) 60%,rgba(11,15,13,.15) 100%)}
    .welcome-banner .welcome-content{position:relative;z-index:2;padding:32px}
    .date-header{color:var(--text-secondary);font-size:10px;text-transform:uppercase;letter-spacing:1px;font-weight:600;margin-bottom:8px}
    .welcome-title{font-size:36px;font-weight:700;margin-bottom:6px;line-height:1.2;text-shadow:0 2px 12px rgba(0,0,0,.5)}
    .welcome-subtitle{color:var(--text-secondary);font-size:13px}
    .grid-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px}
    .stat-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:12px;padding:20px;text-decoration:none;transition:all .15s;display:block}
    .stat-card:hover{border-color:rgba(255,255,255,.18);background:var(--bg-hover)}
    .stat-icon{width:40px;height:40px;border-radius:10px;display:grid;place-items:center;margin-bottom:14px;font-size:16px}
    .stat-icon.green{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .stat-icon.purple{background:rgba(168,85,247,.14);color:#D8B4FE}
    .stat-num{font-size:28px;font-weight:700}
    .stat-label{font-size:12px;color:var(--text-secondary);margin-top:4px}
    .card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:visible;margin-bottom:16px}
    .card-title{font-size:14px;font-weight:600;padding:16px 20px;border-bottom:1px solid var(--border-color)}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:9px 16px;border-radius:6px;cursor:pointer;font-size:12px;text-decoration:none;display:inline-flex;align-items:center;gap:7px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .btn .fa-plus{color:var(--accent-green)}
    .quick{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:24px}
    .eso-item{display:flex;justify-content:space-between;align-items:center;padding:12px 20px;border-bottom:1px solid var(--border-color);font-size:13px}
    .eso-item:last-child{border-bottom:none}
    .eso-nome{font-weight:600}
    .eso-sub{font-size:11px;color:var(--text-secondary);margin-top:2px}
    .mono{font-family:monospace;font-size:12px;color:#60A5FA}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap}
    .tag-ativa{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .tag-inativa{background:rgba(107,114,128,.15);color:var(--text-secondary)}
    .empty{text-align:center;padding:36px 20px;color:var(--text-secondary);font-size:12px}
    .chart-wrap{position:relative;min-height:300px;padding:6px 6px 0}
    .chart-legend{display:flex;gap:18px;flex-wrap:wrap;padding:14px 20px;border-top:1px solid var(--border-color);font-size:12px;color:var(--text-secondary)}
    .legend-item{display:flex;align-items:center;gap:7px}
    .legend-dot{width:10px;height:10px;border-radius:3px}
</style>

@php
    $fundoPlataforma = \App\Models\Configuracao::plataformaFundoUrl();
@endphp
<div class="welcome-banner">
    <img src="{{ $fundoPlataforma ?: asset('Image.jpeg') }}" alt="" class="welcome-bg">
    <div class="welcome-overlay"></div>
    <div class="welcome-content">
        <div class="date-header">{{ strtoupper(\Carbon\Carbon::now()->locale('pt')->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}</div>
        <h1 class="welcome-title">Olá, {{ explode(' ', auth()->user()->name)[0] }}</h1>
        <p class="welcome-subtitle">Aqui está a visão geral da plataforma {{ \App\Models\Configuracao::plataformaNome() }}.</p>
    </div>
</div>

<div class="grid-cards">
    <a href="{{ route('central.escolas.index') }}" class="stat-card">
        <div class="stat-icon green"><i class="fas fa-school"></i></div>
        <div class="stat-num">{{ $totalEscolas }}</div>
        <div class="stat-label">Escolas registadas</div>
    </a>
    <a href="{{ route('central.escolas.index') }}" class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-num">{{ $escolasAtivas }}</div>
        <div class="stat-label">Escolas ativas</div>
    </a>
    <a href="{{ route('central.usuarios.index') }}" class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-users"></i></div>
        <div class="stat-num">{{ $totalUsuariosPlataforma }}</div>
        <div class="stat-label">Utilizadores da plataforma</div>
    </a>
</div>

<div class="quick">
    <a href="{{ route('central.escolas.index') }}" class="btn"><i class="fas fa-school"></i> Escolas registadas</a>
    <a href="{{ route('central.escolas.create') }}" class="btn"><i class="fas fa-plus"></i> Adicionar escola</a>
    <a href="{{ route('central.usuarios.create') }}" class="btn"><i class="fas fa-plus"></i> Novo utilizador</a>
</div>

<div class="card">
    <div class="card-title"><i class="fas fa-chart-bar" style="margin-right:8px"></i>Registos por mês (últimos 12 meses)</div>
    <div class="chart-wrap">
        <canvas id="graficoResumo"></canvas>
    </div>
    <div class="chart-legend">
        <span class="legend-item"><span class="legend-dot" style="background:rgba(34,197,94,.8)"></span> Escolas registadas</span>
        <span class="legend-item"><span class="legend-dot" style="background:rgba(168,85,247,.85)"></span> Utilizadores da plataforma</span>
    </div>
</div>

<!--<div class="card">
    <div class="card-title">Escolas recentes</div>
    @if($escolasRecentes->isEmpty())
        <div class="empty">
            <i class="fas fa-school" style="font-size:30px;margin-bottom:10px;display:block;opacity:.3"></i>
            Ainda não há escolas registadas. Adicione a primeira escola.
        </div>
    @else
        @foreach($escolasRecentes as $escola)
            @php
                $host = parse_url(config('app.url'), PHP_URL_HOST);
                $porta = parse_url(config('app.url'), PHP_URL_PORT);
                $urlEscola = 'http://' . $escola->slug . '.' . $host . ($porta ? ':' . $porta : '');
            @endphp
            <div class="eso-item">
                <div>
                    <div class="eso-nome">{{ $escola->nome }}</div>
                    <div class="eso-sub">{{ $urlEscola }}</div>
                </div>
                <div style="display:flex;align-items:center;gap:12px">
                    <span class="mono">{{ $escola->nome_bd }}</span>
                    <span class="tag {{ $escola->ativa ? 'tag-ativa' : 'tag-inativa' }}">{{ $escola->ativa ? 'Ativa' : 'Inativa' }}</span>
                </div>
            </div>
        @endforeach
    @endif
</div>-->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const labels = @json($labelsGrafico);
    const dadosEscolas = @json($dadosEscolasGrafico);
    const dadosUsuarios = @json($dadosUsuariosGrafico);
    const ctx = document.getElementById('graficoResumo').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Escolas registadas',
                    data: dadosEscolas,
                    backgroundColor: 'rgba(34,197,94,.75)',
                    hoverBackgroundColor: 'rgba(34,197,94,1)',
                    borderRadius: 6,
                    maxBarThickness: 24
                },
                {
                    label: 'Utilizadores da plataforma',
                    data: dadosUsuarios,
                    backgroundColor: 'rgba(168,85,247,.8)',
                    hoverBackgroundColor: 'rgba(168,85,247,1)',
                    borderRadius: 6,
                    maxBarThickness: 24
                }
            ]
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
