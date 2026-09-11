<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Recibo {{ $pagamento->recibo_numero ?? '' }}</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'DejaVu Sans',Helvetica,Arial,sans-serif;color:#1f2937;font-size:11px}
        .recibo{max-width:440px;margin:0 auto;border:1.5px solid #10b981;border-radius:12px;overflow:hidden}
        .head{background:#0b3d24;color:#fff;padding:18px 20px;text-align:center}
        .head h1{font-size:15px;letter-spacing:.4px}
        .head .tagline{font-size:9px;opacity:.85;margin-top:3px}
        .head .badge{margin-top:10px;display:inline-block;border:1px dashed rgba(255,255,255,.5);border-radius:6px;padding:5px 12px;font-size:10px;font-family:monospace}
        .body{padding:18px 20px}
        .meta{width:100%;border-collapse:collapse;margin-bottom:14px}
        .meta td{padding:5px 0;vertical-align:top}
        .meta .k{color:#6b7280;font-size:8.5px;text-transform:uppercase;letter-spacing:.6px;padding-right:8px;white-space:nowrap}
        .meta .v{font-weight:600;font-size:11px}
        .divider{border-top:1px dashed #d1d5db;margin:10px 0}
        .extract{display:flex;justify-content:space-between;font-size:11px;padding:6px 0;color:#4b5563}
        .total{background:rgba(16,185,129,.1);border:1px solid #34d399;border-radius:8px;padding:12px 14px;display:flex;justify-content:space-between;align-items:center;margin-top:10px}
        .total .l{font-size:9px;text-transform:uppercase;letter-spacing:.7px;color:#047857;font-weight:700}
        .total .v{font-size:19px;font-weight:800;color:#047857}
        .progress{margin-top:14px}
        .progress .l{font-size:9px;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:6px}
        .bar{height:8px;background:#e5e7eb;border-radius:5px;overflow:hidden}
        .fill{height:100%;background:linear-gradient(90deg,#10b981,#34d399);border-radius:5px}
        .prog-row{display:flex;justify-content:space-between;font-size:9px;color:#6b7280;margin-top:6px}
        .foot{background:#0b3d24;color:#fff;text-align:center;padding:10px;font-size:8.5px}
        .foot span{display:block;margin:1px 0}
        .obs{font-size:9.5px;color:#6b7280;font-style:italic;margin-top:8px}
    </style>
</head>
<body>
    <div class="recibo">
        <div class="head">
            <h1>{{ $escola->nome }}</h1>
            <div class="tagline">{{ $escola->morada }} · {{ $escola->telefone }} · {{ $escola->email }}</div>
            <div class="badge">RECIBO {{ $pagamento->recibo_numero ?? '' }}</div>
        </div>

        <div class="body">
            @php
                $quantidade = (int) ($pagamento->quantidade_meses ?? 1);
                $valorPorMes = $quantidade > 1 ? round(((float) $pagamento->valor) / $quantidade, 2) : (float) $pagamento->valor;
            @endphp
            <table class="meta">
                <tr>
                    <td class="k">Aluno</td><td class="v">{{ $pagamento->aluno?->name ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="k">Turma</td><td class="v">{{ $pagamento->aluno?->turma?->nome_turma ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="k">Período</td><td class="v">
                        @if($quantidade > 1)
                            {{ $quantidade }} meses · {{ $meses[$pagamento->mes] ?? $pagamento->mes }}/{{ $pagamento->ano }} → {{ $meses[$pagamento->mes_fim] ?? $pagamento->mes_fim }}/{{ $pagamento->ano_fim }}
                        @else
                            {{ $meses[$pagamento->mes] ?? $pagamento->mes }} de {{ $pagamento->ano }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="k">Data</td><td class="v">{{ $pagamento->data_pagamento ? $pagamento->data_pagamento->format('d/m/Y') : '—' }}</td>
                </tr>
                <tr>
                    <td class="k">Método</td><td class="v">{{ $pagamento->metodo_pagamento ?? '—' }}</td>
                </tr>
            </table>

            <div class="divider"></div>

            @foreach($pagamento->meses_array as $m)
            <div class="extract">
                <span>Mensalidade — {{ $meses[$m['mes']] ?? $m['mes'] }} / {{ $m['ano'] }}</span>
                <b>{{ number_format($valorPorMes, 2, ',', ' ') }} {{ $escola->moeda }}</b>
            </div>
            @endforeach

            <div class="total">
                <div class="l">Total pago</div>
                <div class="v">{{ number_format($pagamento->valor, 2, ',', ' ') }} {{ $escola->moeda }}</div>
            </div>

            <div class="progress">
                <div class="l">Progresso do ano letivo {{ $pagamento->ano }} ({{ number_format($resumoAno['percentagem'], 1) }}% pago)</div>
                <div class="bar"><div class="fill" style="width:{{ $resumoAno['percentagem'] }}%"></div></div>
                <div class="prog-row">
                    <span>Pago: {{ number_format($resumoAno['pago'], 2, ',', ' ') }} {{ $escola->moeda }}</span>
                    <span>Anual: {{ number_format($resumoAno['totalAno'], 2, ',', ' ') }} {{ $escola->moeda }}</span>
                    <span>Restante: {{ number_format($resumoAno['restante'], 2, ',', ' ') }} {{ $escola->moeda }}</span>
                </div>
            </div>

            @if($pagamento->observacoes)
            <div class="obs">Obs.: {{ $pagamento->observacoes }}</div>
            @endif

            <div class="divider"></div>
            <div style="font-size:9px;color:#6b7280;text-align:center">
                Obrigado pela confiança. O pagamento foi registado com sucesso.
            </div>
        </div>

        <div class="foot">
            <span>{{ $escola->nome }}</span>
            <span>{{ $escola->morada }}</span>
        </div>
    </div>
</body>
</html>