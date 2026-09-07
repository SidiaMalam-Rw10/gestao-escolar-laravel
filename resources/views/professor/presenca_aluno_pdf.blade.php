<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Relatório de Presenças — Aluno</title>
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

        .title {
            margin-bottom: 14px;
        }

        .title h1 { font-size: 15px; color: #111827; margin-bottom: 2px; }
        .title p { font-size: 11px; color: #6b7280; }

        .info-grid {
            display: flex;
            gap: 20px;
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
        table td { padding: 7px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10.5px; }
        table tr:nth-child(even) td { background: #f9fafb; }

        .chip {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: 700;
        }

        .chip-presente { background: #dcfce7; color: #166534; }
        .chip-falta { background: #fee2e2; color: #991b1b; }
        .chip-justificada { background: #fef3c7; color: #92400e; }

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
        <h1>Relatório de Presenças — {{ $aluno->name }}</h1>
        <p>Período: <strong>{{ $tituloPeriodo }}</strong></p>
    </div>

    <div class="info-grid">
        <div class="item">
            <div class="k">Turma</div>
            <div class="v">{{ $turmaRel?->nome_turma ?: '—' }}@if($turmaRel?->nivel) · {{ $turmaRel->nivel }}@endif</div>
        </div>
        <div class="item">
            <div class="k">Nº de aluno</div>
            <div class="v">{{ $aluno->numero ?: '—' }}</div>
        </div>
        <div class="item">
            <div class="k">Presenças</div>
            <div class="v ok">{{ $totalPresentes }}</div>
        </div>
        <div class="item">
            <div class="k">Faltas</div>
            <div class="v bad">{{ $totalFaltas }}</div>
        </div>
        <div class="item">
            <div class="k">Justificadas</div>
            <div class="v warn">{{ $totalJustificadas }}</div>
        </div>
        <div class="item">
            <div class="k">Total de marcações</div>
            <div class="v">{{ $marcacoes->count() }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:16%">Data</th>
                <th style="width:14%">Hora</th>
                <th style="width:22%">Estado</th>
                <th>Disciplina / Professor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($marcacoes as $m)
            <tr>
                <td>{{ \Carbon\Carbon::parse($m->data)->format('d/m/Y') }}</td>
                <td>{{ $m->hora }}</td>
                <td><span class="chip chip-{{ $m->estado }}">{{ $m->estadoLabel }}</span></td>
                <td>{{ $m->professor?->name ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;color:#9ca3af;padding:16px">Sem marcações registadas neste período.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <span>MiScool · Gestão Escolar</span>
        <span>Sistema de Presenças de Alunos</span>
    </div>
</body>
</html>