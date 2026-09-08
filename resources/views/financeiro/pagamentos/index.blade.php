@extends('layouts.app')

@section('title', 'Pagamentos')
@section('page-title', 'Pagamentos')

@section('content')
<style>
    .fn-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:20px}
    .fn-stat{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:18px}
    .fn-stat-header{font-size:10px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.8px;font-weight:600;margin-bottom:8px}
    .fn-stat-value{font-size:24px;font-weight:700;line-height:1}
    .fn-toolbar{display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:16px;margin-bottom:20px}
    .fn-field{display:flex;flex-direction:column;gap:6px}
    .fn-field label{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--text-secondary)}
    .fn-select,.fn-input{padding:9px 14px;background:var(--bg-input,#151D19);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;font-family:inherit}
    .fn-select:focus,.fn-input:focus{outline:none;border-color:var(--accent-green)}
    .fn-btn{background:var(--accent-green);color:#000;border:none;padding:9px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;text-decoration:none;transition:background .15s}
    .fn-btn:hover{background:#1ea34e}
    .fn-btn-outline{background:transparent;border:1px solid var(--border-color);color:var(--text-primary);padding:7px 14px;border-radius:6px;font-size:12px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;text-decoration:none}
    .fn-btn-outline:hover{border-color:var(--accent-green);color:var(--accent-green)}
    .table-wrap{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow-x:auto}
    .pg-table{width:100%;border-collapse:collapse;font-size:13px;min-width:760px}
    .pg-table th{text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-secondary);font-weight:600;padding:10px 12px;border-bottom:1px solid var(--border-color)}
    .pg-table td{padding:12px;border-bottom:1px solid rgba(255,255,255,.03)}
    .pg-table tr:last-child td{border-bottom:none}
    .pg-status{display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600}
    .pg-status-pago{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .pg-status-pendente{background:rgba(234,179,8,.12);color:var(--accent-yellow)}
    .pg-status-atrasado{background:rgba(239,68,68,.12);color:#FCA5A5}
    .pagination-wrap{margin-top:20px}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
</style>

@if(session('success'))
<div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="fn-stats">
    <div class="fn-stat" style="border-left:3px solid var(--accent-green)">
        <div class="fn-stat-header"><i class="fas fa-hand-holding-usd"></i> Receitas do mês ({{ $meses[$mes] }})</div>
        <div class="fn-stat-value" style="color:var(--accent-green)">{{ number_format($stats['receitasMes'], 2, ',', ' ') }} Xof</div>
    </div>
    <div class="fn-stat">
        <div class="fn-stat-header"><i class="fas fa-calendar-alt"></i> Receitas do ano ({{ $ano }})</div>
        <div class="fn-stat-value" style="color:#60A5FA">{{ number_format($stats['receitasAno'], 2, ',', ' ') }} Xof</div>
    </div>
    <div class="fn-stat">
        <div class="fn-stat-header"><i class="fas fa-file-invoice"></i> Pagamentos no mês</div>
        <div class="fn-stat-value">{{ $stats['pagamentosMes'] }}</div>
    </div>
    <div class="fn-stat">
        <div class="fn-stat-header"><i class="fas fa-exclamation-circle"></i> Pendentes/atrasados (total)</div>
        <div class="fn-stat-value" style="color:#FB923C">{{ $stats['pendentes'] }}</div>
    </div>
</div>

<div class="fn-toolbar">
    <form method="GET" action="{{ route('financeiro.pagamentos.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;width:100%">
        <div class="fn-field" style="flex:1;min-width:180px">
            <label>Pesquisar</label>
            <input type="text" class="fn-input" name="search" value="{{ request('search') }}" placeholder="Nome ou nº de processo">
        </div>
        <div class="fn-field">
            <label>Turma</label>
            <select class="fn-select" name="turma_id">
                <option value="">Todas</option>
                @foreach($turmas as $t)
                <option value="{{ $t->id }}" {{ request('turma_id') == $t->id ? 'selected' : '' }}>{{ $t->nome_turma }}</option>
                @endforeach
            </select>
        </div>
        <div class="fn-field">
            <label>Mês</label>
            <select class="fn-select" name="mes">
                <option value="">Todos</option>
                @foreach($meses as $k => $m)
                <option value="{{ $k }}" {{ request('mes') == $k ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div class="fn-field">
            <label>Ano</label>
            <select class="fn-select" name="ano">
                @foreach([now()->year, now()->year - 1, now()->year - 2] as $a)
                <option value="{{ $a }}" {{ request('ano') == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
        </div>
        <div class="fn-field">
            <label>Status</label>
            <select class="fn-select" name="status">
                <option value="">Todos</option>
                <option value="pago" {{ request('status') == 'pago' ? 'selected' : '' }}>Pago</option>
                <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="atrasado" {{ request('status') == 'atrasado' ? 'selected' : '' }}>Atrasado</option>
            </select>
        </div>
        <button type="submit" class="fn-btn"><i class="fas fa-filter"></i> Filtrar</button>
        @if(auth()->user()->can('financeiro'))
        <a href="{{ route('financeiro.pagamentos.create') }}" class="fn-btn" style="margin-left:auto"><i class="fas fa-plus"></i> Registar pagamento</a>
        @endif
    </form>
</div>

<div class="table-wrap">
    @if($pagamentos->count() > 0)
    <table class="pg-table">
        <thead>
            <tr>
                <th>Recibo</th>
                <th>Aluno</th>
                <th>Mês</th>
                <th>Ano</th>
                <th>Data</th>
                <th>Valor</th>
                <th>Método</th>
                <th>Status</th>
                <th>Recibo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagamentos as $pag)
            <tr>
                <td style="font-weight:600;color:var(--accent-green);font-size:11px">{{ $pag->recibo_numero ?? '—' }}</td>
                <td style="font-weight:500">{{ $pag->aluno?->name ?? '—' }}</td>
                <td>{{ $meses[$pag->mes] ?? $pag->mes }}</td>
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
                <td>
                    <a href="{{ route('financeiro.pagamentos.recibo', $pag) }}" class="fn-btn-outline" title="Ver recibo">
                        <i class="fas fa-receipt"></i> Ver
                    </a>
                    <a href="{{ route('financeiro.pagamentos.recibo.pdf', $pag) }}" class="fn-btn-outline" title="Baixar PDF">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state" style="text-align:center;padding:48px 20px;color:var(--text-secondary);font-size:12px">
        <i class="fas fa-money-check-alt" style="font-size:28px;margin-bottom:12px;display:block;opacity:.4"></i>
        Nenhum pagamento encontrado.
    </div>
    @endif
</div>

@if($pagamentos->hasPages())
<div class="pagination-wrap">
    {{ $pagamentos->links() }}
</div>
@endif
@endsection