@extends('layouts.app')

@section('title', 'Relatórios Financeiros')
@section('page-title', 'Relatórios Financeiros')

@section('content')
<style>
    .fn-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:20px}
    .fn-stat{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:18px}
    .fn-stat-header{font-size:10px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.8px;font-weight:600;margin-bottom:8px}
    .fn-stat-value{font-size:24px;font-weight:700;line-height:1}
    .fn-toolbar{display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:16px;margin-bottom:20px}
    .fn-field{display:flex;flex-direction:column;gap:6px}
    .fn-field label{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--text-secondary)}
    .fn-select{padding:9px 14px;background:var(--bg-input,#151D19);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;font-family:inherit}
    .fn-btn{background:var(--accent-green);color:#000;border:none;padding:9px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer}
    .panel{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:hidden;margin-bottom:20px}
    .panel-head{display:flex;align-items:center;gap:10px;padding:14px 20px;border-bottom:1px solid var(--border-color)}
    .panel-head i{color:var(--accent-green);font-size:15px}
    .panel-head .title{font-weight:600;font-size:13px}
    .panel-body{padding:20px}
    .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px}
    @media(max-width:800px){.grid-2{grid-template-columns:1fr}}
    .table-wrap{overflow-x:auto}
    .pg-table{width:100%;border-collapse:collapse;font-size:13px;min-width:640px}
    .pg-table th{text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-secondary);font-weight:600;padding:10px 12px;border-bottom:1px solid var(--border-color)}
    .pg-table td{padding:12px;border-bottom:1px solid rgba(255,255,255,.03)}
    .pg-table tr:last-child td{border-bottom:none}
    .bar-row{display:flex;align-items:center;gap:12px;margin-bottom:12px}
    .bar-row .month{width:90px;font-size:12px;text-align:right;flex-shrink:0}
    .bar-row .track{flex:1;height:14px;background:var(--bg-input,#151D19);border-radius:6px;overflow:hidden}
    .bar-row .fill{height:100%;background:linear-gradient(90deg,#1EA34E,#34D399);border-radius:6px;min-width:2px}
    .bar-row .val{width:130px;font-size:11px;color:var(--text-secondary);flex-shrink:0}
    .progress{display:inline-grid;place-items:center;width:44px;height:44px;border-radius:50%;background:conic-gradient(var(--accent-green,#22C55E) var(--p,0%), var(--bg-input,#151D19) 0);position:relative}
    .progress::after{content:attr(data-pct) '%';position:absolute;width:32px;height:32px;background:var(--bg-card);border-radius:50%;display:grid;place-items:center;font-size:9px;font-weight:700}
    .tag-quite{display:inline-flex;align-items:center;gap:5px;background:rgba(34,197,94,.12);color:var(--accent-green);padding:2px 9px;border-radius:12px;font-size:10.5px;font-weight:600}
    .tag-debt{display:inline-flex;align-items:center;gap:5px;background:rgba(251,146,60,.12);color:#FB923C;padding:2px 9px;border-radius:12px;font-size:10.5px;font-weight:600}
</style>

<div class="fn-stats">
    <div class="fn-stat" style="border-left:3px solid var(--accent-green)">
        <div class="fn-stat-header"><i class="fas fa-hand-holding-usd"></i> Receitas do ano ({{ $ano }})</div>
        <div class="fn-stat-value" style="color:var(--accent-green)">{{ number_format($totais['receitasAno'], 2, ',', ' ') }} Xof</div>
    </div>
    <div class="fn-stat">
        <div class="fn-stat-header"><i class="fas fa-calendar-day"></i> Receitas de {{ $meses[now()->month] }}/{{ $ano }}</div>
        <div class="fn-stat-value" style="color:#60A5FA">{{ number_format($totais['receitasMes'], 2, ',', ' ') }} Xof</div>
    </div>
    <div class="fn-stat">
        <div class="fn-stat-header"><i class="fas fa-user-check"></i> Alunos em dia</div>
        <div class="fn-stat-value" style="color:var(--accent-green)">{{ $totais['alunosEmDia'] }}/{{ $totais['alunosTotal'] }}</div>
    </div>
    <div class="fn-stat">
        <div class="fn-stat-header"><i class="fas fa-users"></i> Alunos em dívida</div>
        <div class="fn-stat-value" style="color:#FB923C">{{ $totais['alunosTotal'] - $totais['alunosEmDia'] }}</div>
    </div>
</div>

<div class="fn-toolbar">
    <form method="GET" action="{{ route('financeiro.relatorios') }}" style="display:flex;gap:12px;align-items:flex-end">
        <div class="fn-field">
            <label>Ano</label>
            <select class="fn-select" name="ano">
                @foreach([now()->year, now()->year - 1] as $a)
                <option value="{{ $a }}" {{ $ano === $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
        </div>
        <div class="fn-field">
            <label>Turma</label>
            <select class="fn-select" name="turma_id">
                <option value="">Todas</option>
                @foreach($turmas as $t)
                <option value="{{ $t->id }}" {{ (int) $turmaId === $t->id ? 'selected' : '' }}>{{ $t->nome_turma }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="fn-btn"><i class="fas fa-filter"></i> Filtrar</button>
    </form>
</div>

<div class="grid-2">
    <div class="panel">
        <div class="panel-head">
            <i class="fas fa-chart-bar"></i>
            <div class="title">Receitas por mês ({{ $ano }})</div>
        </div>
        <div class="panel-body">
            @if($receitasPorMes->isNotEmpty())
            @php $max = $receitasPorMes->max('total') ?? 0; @endphp
            @foreach($meses as $k => $m)
            <div class="bar-row">
                <div class="month">{{ $m }}</div>
                <div class="track">
                    <div class="fill" style="width:{{ $max > 0 ? ($receitasPorMes[$k]->total ?? 0) / $max * 100 : 0 }}%"></div>
                </div>
                <div class="val">
                    @if(isset($receitasPorMes[$k]))
                    {{ number_format($receitasPorMes[$k]->total, 0, ',', ' ') }} Xof ({{ $receitasPorMes[$k]->quantidade }})
                    @else
                    0 Xof
                    @endif
                </div>
            </div>
            @endforeach
            @else
            <div style="font-size:12px;color:var(--text-secondary);text-align:center;padding:30px 0">Sem dados para este ano.</div>
            @endif
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <i class="fas fa-credit-card"></i>
            <div class="title">Receitas por método ({{ $ano }})</div>
        </div>
        <div class="panel-body">
            @if($receitasPorMetodo->isNotEmpty())
            <div class="table-wrap">
                <table class="pg-table">
                    <thead>
                        <tr><th>Método</th><th>Pagamentos</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                        @foreach($receitasPorMetodo as $r)
                        <tr>
                            <td style="font-weight:500">{{ $r->metodo_pagamento ?? '—' }}</td>
                            <td>{{ $r->quantidade }}</td>
                            <td style="font-weight:600">{{ number_format($r->total, 2, ',', ' ') }} Xof</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="font-size:12px;color:var(--text-secondary);text-align:center;padding:30px 0">Sem dados para este ano.</div>
            @endif
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <i class="fas fa-user-graduate"></i>
        <div class="title">Situação das propinas por aluno ({{ $ano }})</div>
    </div>
    <div class="panel-body">
        <div class="table-wrap">
            @if($alunos->isNotEmpty())
            <table class="pg-table">
                <thead>
                    <tr>
                        <th>Aluno</th>
                        <th>Turma</th>
                        <th>Progresso</th>
                        <th>Pago</th>
                        <th>Anual</th>
                        <th>Restante</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alunos as $l)
                    <tr>
                        <td style="font-weight:500">{{ $l['aluno']->name }}</td>
                        <td>{{ $l['aluno']->turma?->nome_turma ?? '—' }}</td>
                        <td>
                            <div class="progress" data-pct="{{ $l['percentagem'] }}" style="--p:{{ $l['percentagem'] }}"></div>
                        </td>
                        <td style="font-weight:600;color:var(--accent-green)">{{ number_format($l['pago'], 2, ',', ' ') }} Xof</td>
                        <td style="color:var(--text-secondary)">{{ number_format($l['totalAno'], 2, ',', ' ') }} Xof</td>
                        <td style="font-weight:600;color:{{ $l['restante'] > 0 ? '#FB923C' : 'var(--accent-green)' }}">{{ number_format($l['restante'], 2, ',', ' ') }} Xof</td>
                        <td>
                            @if($l['restante'] <= 0)
                            <span class="tag-quite"><i class="fas fa-check-circle"></i> Em dia</span>
                            @else
                            <span class="tag-debt"><i class="fas fa-exclamation-circle"></i> Em dívida</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div style="font-size:12px;color:var(--text-secondary);text-align:center;padding:30px 0">Sem alunos com esta filtragem.</div>
            @endif
        </div>
    </div>
</div>

@endsection