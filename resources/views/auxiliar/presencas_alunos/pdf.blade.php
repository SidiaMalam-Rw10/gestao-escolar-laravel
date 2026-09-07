<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Relatório de Faltas — {{ $turma->nome_turma }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1f2937; padding: 24px; }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #16a34a;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .header .brand { font-size: 20px; font-weight: 800; color: #14532d; }
        .header .brand small { display: block; font-size: 10px; font-weight: 400; color: #6b7280; }

        .header .meta { text-align: right; font-size: 10px; color: #6b7280; }

        .title { margin-bottom: 14px; }

        .title h1 { font-size: 15px; color: #111827; margin-bottom: 2px; }
        .title p { font-size: 11px; color: #6b7280; }

        .info-grid {
            display: flex;
            gap: 24px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 16px;
        }

        .info-grid .item { font-size: 11px; }
        .info-grid .item .k { font-size: 9px; text-transform: uppercase; letter-spacing: .5px; color: #6b7280; }
        .info-grid .item .v { font-weight: 700; color: #111827; margin-top: 2px; }
        .info-grid .item .v.ok { color: #16a34a; }
        .info-grid .item .v.bad { color: #dc2626; }
        .info-grid .item .v.warn { color: #d97706; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table th {
            background: #14532d;
            color: #fff;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .5px;
            padding: 7px 8px;
            text-align: left;
        }
        table th.center, table td.center { text-align: center; }
        table td { padding: 7px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10.5px; }
        table tr:nth-child(even) td { background: #f9fafb; }

        .aluno { display: flex; align-items: center; gap: 8px; }
        .aluno .num { font-size: 9px; color: #6b7280; }

        .chip {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: 700;
            min-width: 28px;
            text-align: center;
        }

        .chip-presente { background: #dcfce7; color: #166534; }
        .chip-falta { background: #fee2e2; color: #991b1b; }
        .chip-justificada { background: #fef3c7; color: #92400e; }

        .alerta {
            margin-top: 12px;
            padding: 10px 12px;
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #991b1b;
            border-radius: 6px;
            font-size: 10.5px;
        }

        .footer {
            margin-top: 20px;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            font-size: 9px;
            color: #9ca3af;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">
            MiScool
            <small>By We-Tech · Gestão Escolar</small>
        </div>
        <div class="meta">
            Emitido em: {{ now()->format('d/m/Y H:i') }}<br>
            Por: {{ $progenitor->name }}
        </div>
    </div>

    <div class="title">
        <h1>Relatório de Faltas e Presenças — Turma {{ $turma->nome_turma }}</h1>
        <p>{{ $turma->nivel }} · {{ $turma->ano_lectivo }} — Período: <strong>{{ $tituloPeriodo }}</strong></p>
    </div>

    <div class="info-grid">
        <div class="item">
            <div class="k">Alunos</div>
            <div class="v">{{ $alunos->count() }}</div>
        </div>
        <div class="item">
            <div class="k">Total presenças</div>
            <div class="v ok">{{ $totalPresencas }}</div>
        </div>
        <div class="item">
            <div class="k">Total faltas</div>
            <div class="v bad">{{ $totalFaltas }}</div>
        </div>
        <div class="item">
            <div class="k">Justificadas</div>
            <div class="v warn">{{ $totalJustificadas }}</div>
        </div>
        <div class="item">
            <div class="k">Professor responsável</div>
            <div class="v">{{ $turma->professorResponsavel?->name ?: '—' }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Alunos</th>
                <th class="center" style="width:10%">Presenças</th>
                <th class="center" style="width:10%">Faltas</th>
                <th class="center" style="width:13%">Justificadas</th>
                <th class="center" style="width:12%">Taxa</th>
            </tr>
        </thead>
        <tbody>
            @forelse($linhas as $linha)
            @php
                $taxa = $linha['taxa'];
                $cor = $taxa === null ? '#6b7280' : ($taxa >= 75 ? '#16a34a' : ($taxa >= 50 ? '#d97706' : '#dc2626'));
            @endphp
            <tr>
                <td>
                    <div class="aluno">
                        <span>{{ $linha['aluno']->name }}</span>
                        @if($linha['aluno']->numero)
                        <span class="num">Nº {{ $linha['aluno']->numero }}</span>
                        @endif
                    </div>
                </td>
                <td class="center"><span class="chip chip-presente">{{ $linha['presencas'] }}</span></td>
                <td class="center"><span class="chip chip-falta">{{ $linha['faltas'] }}</span></td>
                <td class="center"><span class="chip chip-justificada">{{ $linha['justificadas'] }}</span></td>
                <td class="center" style="font-weight:700;color:{{ $cor }}">{{ $taxa !== null ? $taxa . ' %' : '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;color:#9ca3af;padding:16px">Sem marcações registadas neste período.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @php
        $alunosCom3MaisFaltas = $linhas->filter(fn ($l) => $l['faltas'] >= 3)->values();
    @endphp
    @if($alunosCom3MaisFaltas->count() > 0)
    <div class="alerta">
        <strong>Atenção:</strong> {{ $alunosCom3MaisFaltas->count() }} aluno(s) com <strong>3 ou mais faltas</strong> no período:
        {{ $alunosCom3MaisFaltas->pluck('aluno.name')->implode(', ') }}.
    </div>
    @endif

    <div class="footer">
        <span>MiScool · Gestão Escolar</span>
        <span>Relatório de Faltas dos Alunos</span>
    </div>
</body>
</html>
