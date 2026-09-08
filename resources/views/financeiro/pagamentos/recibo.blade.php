@extends('layouts.app')

@section('title', 'Recibo de Pagamento')
@section('page-title', 'Recibo de Pagamento')

@section('content')
<style>
    .rec-wrap{max-width:760px;margin:0 auto}
    .rec-toolbar{display:flex;justify-content:flex-end;gap:10px;margin-bottom:16px}
    .rec-btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:9px 16px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;text-decoration:none;transition:border-color .15s}
    .rec-btn:hover{border-color:var(--accent-green);color:var(--accent-green)}

    .recibo{border-radius:14px;overflow:hidden;box-shadow:0 12px 34px rgba(0,0,0,.35)}
    .rec-head{background:linear-gradient(135deg,#0B3D24,#16663B 55%,#1EA34E);padding:26px 30px;color:#fff;display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap}
    .rec-head .rec-logo{display:flex;align-items:center;gap:12px}
    .rec-head .rec-logo i{font-size:30px;opacity:.95}
    .rec-head .rec-logo .name{font-size:16px;font-weight:700;letter-spacing:.3px}
    .rec-head .rec-logo .tagline{font-size:11px;opacity:.85;margin-top:2px}
    .rec-badge{background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.35);padding:8px 16px;border-radius:8px;text-align:center}
    .rec-badge .label{font-size:10px;text-transform:uppercase;letter-spacing:1px;opacity:.85}
    .rec-badge .num{font-size:15px;font-weight:700;margin-top:3px;font-family:monospace}

    .rec-body{background:var(--bg-card);padding:26px 30px;border:1px solid var(--border-color);border-top:none}
    .rec-meta{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:18px;padding-bottom:22px;border-bottom:1px dashed var(--border-color)}
    .rec-meta .item .k{font-size:10px;text-transform:uppercase;letter-spacing:.7px;color:var(--text-secondary);margin-bottom:4px}
    .rec-meta .item .v{font-size:14px;font-weight:600}

    .rec-extract{padding:24px 0;display:flex;flex-direction:column;gap:10px}
    .rec-line{display:flex;justify-content:space-between;padding:10px 16px;border-radius:8px;font-size:13px}
    .rec-line.odd{background:rgba(255,255,255,.025)}
    .rec-line b{font-weight:600}

    .rec-total{background:linear-gradient(135deg,rgba(34,197,94,.14),rgba(34,197,94,.04));border:1px solid rgba(34,197,94,.35);border-radius:10px;padding:16px 18px;display:flex;justify-content:space-between;align-items:center}
    .rec-total .tot-label{font-size:11px;text-transform:uppercase;letter-spacing:.8px;color:var(--accent-green);font-weight:600}
    .rec-total .tot-value{font-size:26px;font-weight:800;color:var(--accent-green);line-height:1}
    .rec-por{font-size:11.5px;color:var(--text-secondary);margin-top:14px;font-style:italic}

    .rec-progress{margin-top:20px;background:rgba(255,255,255,.03);border:1px solid var(--border-color);border-radius:10px;padding:16px 18px}
    .rec-progress .prog-head{display:flex;justify-content:space-between;font-size:12px;margin-bottom:10px}
    .rec-progress .prog-head b{color:var(--accent-green)}
    .rec-progress .prog-bar{height:8px;background:var(--bg-input,#151D19);border-radius:6px;overflow:hidden}
    .rec-progress .prog-fill{height:100%;background:linear-gradient(90deg,#1EA34E,#34D399);border-radius:6px;transition:width .6s ease}
    .rec-progress .prog-caption{display:flex;justify-content:space-between;font-size:11px;color:var(--text-secondary);margin-top:8px}

    .rec-foot{background:var(--bg-card);padding:16px 30px;border:1px solid var(--border-color);border-top:none;border-radius:0 0 14px 14px;display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;font-size:11px;color:var(--text-secondary)}

    @media print{
        body{background:#fff}
        .sidebar,.topbar,.rec-toolbar,footer{display:none!important}
        .main-content,.content-area{margin:0!important;padding:0!important}
        .recibo{box-shadow:none;border-radius:0}
    }
</style>

<div class="rec-wrap">
    <div class="rec-toolbar">
        <button onclick="window.print()" class="rec-btn"><i class="fas fa-print"></i> Imprimir</button>
        <a href="{{ route('recibos.pdf', $pagamento) }}" class="rec-btn"><i class="fas fa-file-pdf"></i> Baixar PDF</a>
        @if(auth()->user()->isFinanceiro())
        <a href="{{ route('financeiro.pagamentos.index') }}" class="rec-btn"><i class="fas fa-arrow-left"></i> Voltar</a>
        @else
        <a href="{{ url()->previous() }}" class="rec-btn"><i class="fas fa-arrow-left"></i> Voltar</a>
        @endif
    </div>

    <div class="recibo">
        <div class="rec-head">
            <div class="rec-logo">
                <i class="fas fa-university"></i>
                <div>
                    <div class="name">{{ $escola->nome }}</div>
                    <div class="tagline">{{ $escola->morada }}</div>
                </div>
            </div>
            <div class="rec-badge">
                <div class="label">Recibo</div>
                <div class="num">{{ $pagamento->recibo_numero ?? '—' }}</div>
            </div>
        </div>

        <div class="rec-body">
            <div class="rec-meta">
                <div class="item">
                    <div class="k">Aluno</div>
                    <div class="v">{{ $pagamento->aluno?->name ?? '—' }}</div>
                </div>
                <div class="item">
                    <div class="k">Turma</div>
                    <div class="v">{{ $pagamento->aluno?->turma?->nome_turma ?? '—' }}</div>
                </div>
                <div class="item">
                    <div class="k">Mês/Ano</div>
                    <div class="v">{{ $meses[$pagamento->mes] ?? $pagamento->mes }} / {{ $pagamento->ano }}</div>
                </div>
                <div class="item">
                    <div class="k">Data</div>
                    <div class="v">{{ $pagamento->data_pagamento ? $pagamento->data_pagamento->format('d/m/Y H:i') : '—' }}</div>
                </div>
                <div class="item">
                    <div class="k">Método</div>
                    <div class="v">{{ $pagamento->metodo_pagamento ?? '—' }}</div>
                </div>
                @if($pagamento->registrador)
                <div class="item">
                    <div class="k">Registado por</div>
                    <div class="v">{{ $pagamento->registrador->name }}</div>
                </div>
                @endif
            </div>

            <div class="rec-extract">
                <div class="rec-line odd" style="color:var(--text-secondary)">
                    <span>Mensalidade — {{ $meses[$pagamento->mes] }} / {{ $pagamento->ano }}</span>
                    <span>{{ number_format($pagamento->valor, 2, ',', ' ') }} {{ $escola->moeda }}</span>
                </div>
                @if($pagamento->observacoes)
                <div class="rec-line odd" style="color:var(--text-secondary);font-style:italic">
                    <span>Obs.: {{ $pagamento->observacoes }}</span>
                </div>
                @endif
            </div>

            <div class="rec-total">
                <div>
                    <div class="tot-label"><i class="fas fa-check-circle"></i> Total pago</div>
                    <div style="font-size:11px;color:var(--text-secondary);margin-top:4px">Recibo emitido a {{ now()->format('d/m/Y') }}</div>
                </div>
                <div class="tot-value">{{ number_format($pagamento->valor, 2, ',', ' ') }} {{ $escola->moeda }}</div>
            </div>

            <div class="rec-progress">
                <div class="prog-head">
                    <span>Progresso do ano letivo {{ $pagamento->ano }}</span>
                    <b>{{ number_format($resumoAno['percentagem'] ?? 0, 1) }}% pago</b>
                </div>
                <div class="prog-bar">
                    <div class="prog-fill" style="width:{{ $resumoAno['percentagem'] ?? 0 }}%"></div>
                </div>
                <div class="prog-caption">
                    <span>Pago: <b style="color:var(--accent-green)">{{ number_format($resumoAno['pago'], 2, ',', ' ') }} {{ $escola->moeda }}</b></span>
                    <span>Anual: {{ number_format($resumoAno['totalAno'], 2, ',', ' ') }} {{ $escola->moeda }}</span>
                    <span>Restante: <b style="color:#FB923C">{{ number_format($resumoAno['restante'], 2, ',', ' ') }} {{ $escola->moeda }}</b></span>
                </div>
            </div>
        </div>

        <div class="rec-foot">
            <span><i class="fas fa-phone"></i> {{ $escola->telefone }}</span>
            <span><i class="fas fa-envelope"></i> {{ $escola->email }}</span>
            <span>Obrigado pela confiança!</span>
        </div>
    </div>

    @if(auth()->user()->isEncarregado())
    <p class="rec-por" style="text-align:center">Podes voltar às <a href="{{ route('encarregado.filhos.pagamentos', $pagamento->aluno) }}" style="color:var(--accent-green)">pagamentos do teu filho</a> para consultar todos os recibos.</p>
    @endif
</div>
@endsection