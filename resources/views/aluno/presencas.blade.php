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
    .pr-select{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);border-radius:8px;padding:7px 10px;font-size:12px;cursor:pointer;outline:none}
    .pr-select:focus{border-color:var(--accent-green)}
    .pr-mes-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px}
    .pr-mes-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:16px;display:flex;flex-direction:column;gap:10px}
    .pr-mes-card.destaque{border-color:var(--accent-green);box-shadow:0 0 0 1px rgba(34,197,94,.4)}
    .pr-mes-nome{display:flex;align-items:center;justify-content:space-between;gap:8px}
    .pr-mes-nome b{font-size:14px}
    .pr-mes-ano{font-size:11px;color:var(--text-secondary)}
    .pr-mes-linha{display:flex;justify-content:space-between;align-items:center;font-size:12px}
    .pr-mes-linha span{color:var(--text-secondary)}
    .pr-mes-linha b{font-size:14px}
    .pr-mes-linha b.is-ok{color:var(--accent-green)}
    .pr-mes-linha b.is-falta{color:#F87171}
    .pr-mes-linha b.is-just{color:#FBBF24}
    .pr-mes-taxa{font-weight:700;font-size:20px;text-align:center;padding-top:10px;border-top:1px solid var(--border-color)}
    .pr-mes-vazio{border:1px dashed var(--border-color)}
</style>

<div class="table-wrap">
    <div style="padding:14px 16px;font-size:10px;text-transform:uppercase;letter-spacing:.8px;color:var(--text-secondary);font-weight:600;border-bottom:1px solid var(--border-color)">
        Resumo anual (agregado da escola)
        <span style="float:right;font-size:11px;text-transform:none;font-weight:400">
            <select class="pr-select" onchange="this.form.submit()" form="pr-filtro-form" name="mes" style="min-width:130px">
                <option value="0" @selected($mesFiltro < 1)>Todos os meses</option>
                @foreach($meses as $num => $nome)
                <option value="{{ $num }}" @selected($mesFiltro === $num)>{{ $nome }}</option>
                @endforeach
            </select>
            <select class="pr-select" onchange="this.form.submit()" form="pr-filtro-form" name="ano" style="min-width:90px">
                @foreach($anosDisponiveis as $ano)
                <option value="{{ $ano }}" @selected($anoFiltro === (int) $ano)>{{ $ano }}</option>
                @endforeach
            </select>
        </span>
    </div>

    <form id="pr-filtro-form" method="GET" action="{{ url()->current() }}"></form>

    <div class="pr-mes-grid" style="padding:16px;margin:0">
        @forelse($cardsMensais->where('visivel', true) as $card)
        @if($card['temRegistos'])
        <div class="pr-mes-card {{ $mesFiltro === $card['mes'] ? 'destaque' : '' }}">
            <div class="pr-mes-nome">
                <b>{{ $card['nome'] }}</b>
                <span class="pr-mes-ano">{{ $card['ano'] }}</span>
            </div>
            <div class="pr-mes-linha">
                <span>Presenças</span>
                <b class="is-ok"><i class="fas fa-user-check" style="margin-right:4px"></i>{{ $card['presencas'] }}</b>
            </div>
            <div class="pr-mes-linha">
                <span>Faltas</span>
                <b class="is-falta"><i class="fas fa-user-times" style="margin-right:4px"></i>{{ $card['faltas'] }}</b>
            </div>
            <div class="pr-mes-linha">
                <span>Faltas justificadas</span>
                <b class="is-just"><i class="fas fa-clipboard-check" style="margin-right:4px"></i>{{ $card['justificadas'] }}</b>
            </div>
            <div class="pr-mes-taxa" style="color:{{ $card['taxa'] !== null ? ($card['taxa'] >= 75 ? 'var(--accent-green)' : ($card['taxa'] >= 50 ? 'var(--accent-yellow)' : '#F87171')) : 'var(--text-secondary)' }}">
                {{ $card['taxa'] !== null ? $card['taxa'] . ' %' : '—' }}
            </div>
        </div>
        @else
        <div class="pr-mes-card pr-mes-vazio">
            <div class="pr-mes-nome">
                <b style="color:var(--text-secondary)">{{ $card['nome'] }}</b>
                <span class="pr-mes-ano">{{ $card['ano'] }}</span>
            </div>
            <div class="empty-state" style="padding:18px 10px;font-size:11px">
                <i class="fas fa-calendar-times" style="font-size:20px;display:block;margin-bottom:8px;opacity:.4"></i>
                Sem registos
            </div>
        </div>
        @endif
        @empty
        <div class="empty-state" style="grid-column:1/-1">
            <i class="fas fa-calendar-check" style="font-size:28px;margin-bottom:12px;display:block;opacity:.4"></i>
            Ainda não há registos de presenças para si no período selecionado.
        </div>
        @endforelse
    </div>
    <div style="border-top:1px solid var(--border-color)"></div>
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
    <!--<div class="empty-state">
        <i class="fas fa-calendar-check" style="font-size:28px;margin-bottom:12px;display:block;opacity:.4"></i>
        Ainda não há registos de presenças para si.
    </div>-->
    @endif
</div>
@endsection
