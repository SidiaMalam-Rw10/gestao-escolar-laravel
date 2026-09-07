@extends('layouts.app')

@section('title', 'Pagamentos do Filho')
@section('page-title', 'Pagamentos do Filho')

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
</style>

@include('encarregados._filho_header', ['aluno' => $aluno, 'filhos' => $filhos, 'secao' => 'pagamentos'])

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
            </tr>
        </thead>
        <tbody>
            @foreach($pagamentos as $pag)
            <tr>
                <td style="font-weight:500">{{ $meses[$pag->mes] ?? $pag->mes }}</td>
                <td>{{ $pag->ano }}</td>
                <td style="color:var(--text-secondary)">{{ $pag->data_pagamento ? $pag->data_pagamento->format('d/m/Y') : '—' }}</td>
                <td style="font-weight:600">{{ number_format($pag->valor, 2, ',', ' ') }} Kz</td>
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
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state">
        <i class="fas fa-money-check-alt" style="font-size:28px;margin-bottom:12px;display:block;opacity:.4"></i>
        Ainda não há pagamentos registados para este filho.
    </div>
    @endif
</div>
@endsection
