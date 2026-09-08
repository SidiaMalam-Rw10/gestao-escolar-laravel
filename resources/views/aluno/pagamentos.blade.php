@extends('layouts.app')

@section('title', 'Meus Pagamentos')
@section('page-title', 'Meus Pagamentos')

@section('content')
<style>
    .pg-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:20px}
    .pg-stat{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:18px}
    .pg-stat-header{font-size:10px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.8px;font-weight:600;margin-bottom:8px}
    .pg-stat-value{font-size:26px;font-weight:700;line-height:1}
    .table-wrap{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow-x:auto}
    .pg-table{width:100%;border-collapse:collapse;font-size:13px;min-width:640px}
    .pg-table th{text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-secondary);font-weight:600;padding:10px 12px;border-bottom:1px solid var(--border-color)}
    .pg-table td{padding:12px;border-bottom:1px solid rgba(255,255,255,.03)}
    .pg-table tr:last-child td{border-bottom:none}
    .pg-status{display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600}
    .pg-status-pago{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .pg-status-pendente{background:rgba(234,179,8,.12);color:var(--accent-yellow)}
    .pg-status-atrasado{background:rgba(239,68,68,.12);color:#FCA5A5}
    .empty-state{text-align:center;padding:48px 20px;color:var(--text-secondary);font-size:12px}
    .prog-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:18px;margin-bottom:20px}
    .prog-head{display:flex;justify-content:space-between;align-items:center;font-size:13px;margin-bottom:10px}
    .prog-head b{color:var(--accent-green)}
    .prog-bar{height:9px;background:var(--bg-input,#151D19);border-radius:6px;overflow:hidden}
    .prog-fill{height:100%;background:linear-gradient(90deg,#1EA34E,#34D399);border-radius:6px}
    .prog-caption{display:flex;justify-content:space-between;font-size:11px;color:var(--text-secondary);margin-top:8px}
    .rec-btn{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none;border:1px solid var(--border-color);color:var(--text-primary)}
    .rec-btn:hover{border-color:var(--accent-green);color:var(--accent-green)}
</style>

@if($resumoAno['totalAno'] > 0)
<div class="prog-card">
    <div class="prog-head">
        <span><i class="fas fa-calendar-alt" style="color:var(--accent-green)"></i> Progresso do ano letivo {{ $ano }}</span>
        <b>{{ $resumoAno['percentagem'] }}% pago</b>
    </div>
    <div class="prog-bar">
        <div class="prog-fill" style="width:{{ $resumoAno['percentagem'] }}%"></div>
    </div>
    <div class="prog-caption">
        <span>Pago: <b style="color:var(--accent-green)">{{ number_format($resumoAno['pago'], 2, ',', ' ') }} Xof</b></span>
        <span>Anual: {{ number_format($resumoAno['totalAno'], 2, ',', ' ') }} Xof</span>
        <span>Restante: <b style="color:#FB923C">{{ number_format($resumoAno['restante'], 2, ',', ' ') }} Xof</b></span>
    </div>
</div>
@endif

<div class="pg-stats">
    <div class="pg-stat">
        <div class="pg-stat-header">Total pago</div>
        <div class="pg-stat-value" style="color:var(--accent-green)">{{ number_format($totalPago, 2, ',', ' ') }} Xof</div>
    </div>
    <div class="pg-stat">
        <div class="pg-stat-header">Em dívida</div>
        <div class="pg-stat-value" style="color:#FB923C">{{ number_format($totalPendente, 2, ',', ' ') }} Xof</div>
    </div>
    <div class="pg-stat">
        <div class="pg-stat-header">Pagamentos registados</div>
        <div class="pg-stat-value">{{ $pagamentos->count() }}</div>
    </div>
    <div class="pg-stat">
        <div class="pg-stat-header">Em dia ({{ date('Y') }})</div>
        <div class="pg-stat-value" style="color:#60A5FA">{{ $pagamentos->where('ano', date('Y'))->where('status', 'pago')->count() }}</div>
    </div>
</div>

<div class="table-wrap">
    @if($pagamentos->count() > 0)
    <table class="pg-table">
        <thead>
            <tr>
                <th>Mês</th>
                <th>Ano</th>
                <th>Data de pagamento</th>
                <th>Valor</th>
                <th>Método</th>
                <th>Status</th>
                <th>Observações</th>
                <th>Recibo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagamentos as $pag)
            <tr>
                <td style="font-weight:500">{{ $meses[$pag->mes] ?? $pag->mes }}</td>
                <td>{{ $pag->ano }}</td>
                <td style="color:var(--text-secondary)">{{ $pag->data_pagamento ? $pag->data_pagamento->format('d/m/Y') : '—' }}</td>
                <td style="font-weight:600">{{ number_format($pag->valor, 2, ',', ' ') }} Xof</td>
                <td style="color:var(--text-secondary)">{{ $pag->metodo_pagamento ?? '—' }}</td>
                <td>
                    @if($pag->status === 'pago')
                    <span class="pg-status pg-status-pago"><i class="fas fa-check-circle"></i> Pago</span>
                    @elseif($pag->status === 'atrasado')
                    <span class="pg-status pg-status-atrasado"><i class="fas fa-exclamation-circle"></i> Atrasado</span>
                    @else
                    <span class="pg-status pg-status-pendente"><i class="fas fa-clock"></i> Pendente</span>
                    @endif
                </td>
                <td style="color:var(--text-secondary)">{{ $pag->observacoes ?? '—' }}</td>
                <td>
                    @if($pag->status === 'pago')
                    @if($pag->recibo_numero)
                    <a href="{{ route('recibos.show', $pag) }}" class="rec-btn" title="Ver recibo"><i class="fas fa-receipt"></i> Ver</a>
                    <a href="{{ route('recibos.pdf', $pag) }}" class="rec-btn" title="Baixar PDF"><i class="fas fa-file-pdf"></i></a>
                    @else
                    <span style="color:var(--text-secondary);font-size:11px">—</span>
                    @endif
                    @else
                    <span style="color:var(--text-secondary);font-size:11px">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state">
        <i class="fas fa-money-check-alt" style="font-size:28px;margin-bottom:12px;display:block;opacity:.4"></i>
        Ainda não há pagamentos registados para si.
    </div>
    @endif
</div>
@endsection