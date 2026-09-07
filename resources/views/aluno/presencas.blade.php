@extends('layouts.app')

@section('title', 'Minhas Faltas e Presenças')
@section('page-title', 'Minhas Faltas & Presenças')

@section('content')
<style>
    .pr-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:20px}
    .pr-stat{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:18px}
    .pr-stat-header{font-size:10px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.8px;font-weight:600;margin-bottom:8px}
    .pr-stat-value{font-size:26px;font-weight:700;line-height:1}
    .table-wrap{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow-x:auto}
    .pr-table{width:100%;border-collapse:collapse;font-size:13px;min-width:520px}
    .pr-table th{text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-secondary);font-weight:600;padding:10px 12px;border-bottom:1px solid var(--border-color)}
    .pr-table td{padding:12px;border-bottom:1px solid rgba(255,255,255,.03)}
    .pr-table tr:last-child td{border-bottom:none}
    .pr-num{display:inline-flex;align-items:center;justify-content:center;min-width:40px;padding:3px 10px;border-radius:6px;font-weight:600;font-size:12px}
    .pr-ok{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .pr-warn{background:rgba(234,179,8,.12);color:var(--accent-yellow)}
    .pr-bad{background:rgba(239,68,68,.12);color:#FCA5A5}
    .pr-neutral{background:rgba(255,255,255,.04);color:var(--text-secondary)}
    .empty-state{text-align:center;padding:48px 20px;color:var(--text-secondary);font-size:12px}
    .progress{height:6px;background:var(--bg-hover);border-radius:3px;overflow:hidden;margin-top:8px}
    .progress-bar{height:100%;border-radius:3px;background:var(--accent-green)}
</style>

<div class="pr-stats">
    <div class="pr-stat">
        <div class="pr-stat-header">Taxa de assiduidade</div>
        <div class="pr-stat-value" style="color:{{ $taxaAssiduidade !== null && $taxaAssiduidade >= 75 ? 'var(--accent-green)' : '#FB923C' }}">
            {{ $taxaAssiduidade !== null ? number_format($taxaAssiduidade, 1, ',', ' ') . ' %' : '—' }}
        </div>
        @if($taxaAssiduidade !== null)
        <div class="progress">
            <div class="progress-bar" style="width:{{ min(100, $taxaAssiduidade) }}%"></div>
        </div>
        @endif
    </div>
    <div class="pr-stat">
        <div class="pr-stat-header">Presenças</div>
        <div class="pr-stat-value" style="color:var(--accent-green)">{{ $totalPresencas }}</div>
    </div>
    <div class="pr-stat">
        <div class="pr-stat-header">Faltas</div>
        <div class="pr-stat-value" style="color:{{ $totalFaltas > 0 ? '#FB923C' : 'var(--text-primary)' }}">{{ $totalFaltas }}</div>
        <div style="font-size:11px;color:var(--text-secondary);margin-top:4px">{{ $totalJustificadas }} justificada(s)</div>
    </div>
    <div class="pr-stat">
        <div class="pr-stat-header">Meses registados</div>
        <div class="pr-stat-value">{{ $presencas->count() }}</div>
    </div>
</div>

<div class="table-wrap">
    @if($presencas->count() > 0)
    <table class="pr-table">
        <thead>
            <tr>
                <th>Mês</th>
                <th>Ano</th>
                <th style="text-align:center">Presenças</th>
                <th style="text-align:center">Faltas</th>
                <th style="text-align:center">Justificadas</th>
                <th style="text-align:center">Taxa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($presencas as $p)
            @php
                $total = $p->presencas + $p->faltas;
                $taxaMes = $total > 0 ? round(($p->presencas / $total) * 100, 1) : 100;
            @endphp
            <tr>
                <td style="font-weight:500">{{ $meses[$p->mes] ?? $p->mes }}</td>
                <td style="color:var(--text-secondary)">{{ $p->ano }}</td>
                <td style="text-align:center">
                    <span class="pr-num pr-ok"><i class="fas fa-user-check" style="margin-right:5px"></i>{{ $p->presencas }}</span>
                </td>
                <td style="text-align:center">
                    <span class="pr-num {{ $p->faltas === 0 ? 'pr-ok' : ($p->faltas < 3 ? 'pr-warn' : 'pr-bad') }}">
                        <i class="fas fa-user-times" style="margin-right:5px"></i>{{ $p->faltas }}
                    </span>
                </td>
                <td style="text-align:center">
                    <span class="pr-num pr-neutral">{{ $p->justificadas }}</span>
                </td>
                <td style="text-align:center">
                    <span class="pr-num {{ $taxaMes >= 75 ? 'pr-ok' : ($taxaMes >= 50 ? 'pr-warn' : 'pr-bad') }}">{{ $taxaMes }}%</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state">
        <i class="fas fa-calendar-check" style="font-size:28px;margin-bottom:12px;display:block;opacity:.4"></i>
        Ainda não há registos de presenças para si.
    </div>
    @endif
</div>
@endsection