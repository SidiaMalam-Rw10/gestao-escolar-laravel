@extends('layouts.app')

@section('title', 'Presenças dos Professores')
@section('page-title', 'Folha de Presenças — Professores')

@section('content')
<style>
    .rp-toolbar {
        display: flex;
        align-items: flex-end;
        gap: 14px;
        flex-wrap: wrap;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 18px;
        margin-bottom: 20px;
    }

    .rp-field { display: flex; flex-direction: column; gap: 6px; }

    .rp-field label {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .7px;
        color: var(--text-secondary);
    }

    .rp-input, .rp-select {
        padding: 9px 14px;
        background: var(--bg-input, #151D19);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
    }

    .rp-input:focus, .rp-select:focus { outline: none; border-color: var(--accent-green); }

    .rp-btn {
        background: var(--accent-green);
        color: #000;
        border: none;
        padding: 9px 20px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: background .15s;
    }

    .rp-btn:hover { background: #1ea34e; }

    .rp-btn-link {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        padding: 9px 18px;
        border-radius: 6px;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all .15s;
    }

    .rp-btn-link:hover { border-color: rgba(255,255,255,.2); background: var(--bg-hover); }

    .rp-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .rp-stat {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 16px;
    }

    .rp-stat .h {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .7px;
        color: var(--text-secondary);
        font-weight: 600;
        margin-bottom: 6px;
    }

    .rp-stat .v { font-size: 24px; font-weight: 700; line-height: 1; }

    .alert-success {
        padding: 12px 16px;
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.3);
        color: var(--accent-green);
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .alert-error {
        padding: 12px 16px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #FCA5A5;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .table-wrap {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        overflow-x: auto;
    }

    .rp-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 920px; }

    .rp-table thead th {
        text-align: left;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: var(--text-secondary);
        font-weight: 600;
        padding: 12px 14px;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    .rp-table td { padding: 10px 14px; border-bottom: 1px solid rgba(255,255,255,.03); vertical-align: middle; }

    .rp-table tbody tr:last-child td { border-bottom: none; }

    .rp-table tbody tr:hover { background: var(--bg-hover); }

    .prof-cell { display: flex; align-items: center; gap: 12px; }

    .prof-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 13px; flex-shrink: 0;
        background: rgba(34, 197, 94, .14); color: var(--accent-green);
    }

    .prof-name { font-weight: 500; white-space: nowrap; }

    .prof-extra { font-size: 11px; color: var(--text-secondary); margin-top: 2px; }

    .estado-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 11px; border-radius: 20px;
        font-size: 11px; font-weight: 600; white-space: nowrap;
    }

    .chip-hoje { display: inline-flex; align-items: center; gap: 10px; font-size: 12px; }

    .estado-presente { background: rgba(34,197,94,.12); color: var(--accent-green); }
    .estado-falta { background: rgba(239,68,68,.12); color: #FCA5A5; }
    .estado-justificada { background: rgba(234,179,8,.12); color: var(--accent-yellow); }
    .estado-sem { background: rgba(255,255,255,.05); color: var(--text-secondary); }

    .acc-actions { display: flex; gap: 6px; flex-wrap: wrap; }

    .acc-btn {
        border: 1px solid var(--border-color);
        border-radius: 7px;
        padding: 7px 10px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all .12s;
        color: var(--text-primary);
        background: var(--bg-input, #151D19);
        font-family: inherit;
        text-decoration: none;
    }

    .acc-btn:hover { transform: translateY(-1px); border-color: rgba(255,255,255,.25); }

    .acc-btn i { font-size: 10px; }

    .acc-presente { border-color: rgba(34,197,94,.35); color: var(--accent-green); }
    .acc-presente:hover { background: rgba(34,197,94,.12); }

    .acc-falta { border-color: rgba(239,68,68,.35); color: #FCA5A5; }
    .acc-falta:hover { background: rgba(239,68,68,.12); }

    .acc-justificar { border-color: rgba(234,179,8,.35); color: var(--accent-yellow); }
    .acc-justificar:hover { background: rgba(234,179,8,.12); }

    .acc-desfazer { border-color: rgba(148,163,184,.25); color: var(--text-secondary); }
    .acc-desfazer:hover { background: rgba(148,163,184,.12); color: var(--text-primary); }

    .acc-pdf {
        border: 1px solid rgba(239,68,68,.35);
        border-radius: 7px;
        padding: 7px 10px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all .12s;
        color: #FCA5A5;
        background: var(--bg-input, #151D19);
        text-decoration: none;
    }

    .acc-pdf:hover { background: rgba(239,68,68,.12); transform: translateY(-1px); }

    .count-mini { display: flex; gap: 6px; }

    .count-mini span {
        font-size: 11px; font-weight: 600;
        padding: 3px 8px; border-radius: 6px;
    }

    .c-p { background: rgba(34,197,94,.1); color: var(--accent-green); }
    .c-f { background: rgba(239,68,68,.1); color: #FCA5A5; }
    .c-j { background: rgba(234,179,8,.1); color: var(--accent-yellow); }

    .empty-state { text-align: center; padding: 48px 20px; color: var(--text-secondary); font-size: 12px; }

    .today-chip {
        display: inline-block;
        background: var(--bg-hover);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 6px 13px;
        font-size: 12px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .today-chip b { color: var(--text-primary); }

    @media (max-width: 768px) {
        .rp-toolbar { flex-direction: column; align-items: stretch; }
        .rp-btn, .rp-btn-link { justify-content: center; }
    }
</style>

@if(session('success'))
<div class="alert-success">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(isset($errors) && $errors->any())
<div class="alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <span>{{ $errors->first() }}</span>
</div>
@endif

<form method="GET" action="{{ route('auxiliar.presencas.professores.index') }}" class="rp-toolbar">
    <div class="rp-field">
        <label>Mês</label>
        <select name="mes" class="rp-select">
            @foreach($meses as $numero => $nome)
            <option value="{{ $numero }}" {{ $mes == $numero ? 'selected' : '' }}>{{ $nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="rp-field">
        <label>Ano</label>
        <input type="number" name="ano" value="{{ $ano }}" min="2020" max="2030" class="rp-input" style="width:110px;">
    </div>
    <button type="submit" class="rp-btn">
        <i class="fas fa-calendar-alt"></i> Ver folha
    </button>
    <span class="today-chip" style="margin-left:auto;">
        <i class="fas fa-clock" style="margin-right:6px;color:var(--accent-green)"></i>
        Hoje: <b>{{ \Carbon\Carbon::parse($hoje)->format('d/m/Y') }}</b>
    </span>
    <a href="{{ route('auxiliar.presencas.professores.historico') }}" class="rp-btn-link">
        <i class="fas fa-history"></i> Histórico completo
    </a>
</form>

<div class="rp-stats">
    <div class="rp-stat">
        <div class="h">Professores</div>
        <div class="v">{{ $professores->count() }}</div>
    </div>
    <div class="rp-stat">
        <div class="h">Marcados hoje</div>
        <div class="v" style="color:var(--accent-green)">{{ $marcadosHoje }} / {{ $professores->count() }}</div>
    </div>
    <div class="rp-stat">
        <div class="h">Presenças (mês)</div>
        <div class="v" style="color:var(--accent-green)">{{ $totalPresentesMes }}</div>
    </div>
    <div class="rp-stat">
        <div class="h">Faltas (mês)</div>
        <div class="v" style="color:{{ $totalFaltasMes > 0 ? '#FB923C' : 'var(--text-primary)' }}">{{ $totalFaltasMes }}</div>
    </div>
    <div class="rp-stat">
        <div class="h">Justificadas</div>
        <div class="v">{{ $totalJustificadasMes }}</div>
    </div>
</div>

<form id="frm-marcar" method="POST" action="{{ route('auxiliar.presencas.professores.marcar') }}" hidden>
    @csrf
    <input type="hidden" name="user_id" id="marcar-user-id">
    <input type="hidden" name="estado" id="marcar-estado">
    <input type="hidden" name="data" id="marcar-data" value="{{ $hoje }}">
</form>

<form id="frm-desfazer" method="POST" action="{{ route('auxiliar.presencas.professores.desfazer') }}" hidden>
    @csrf
    <input type="hidden" name="user_id" id="desfazer-user-id">
</form>

@if($professores->count() > 0)
<div class="table-wrap">
    <table class="rp-table">
        <thead>
            <tr>
                <th>Professor</th>
                <th>Hoje</th>
                <th style="text-align:center">Total do mês</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($professores as $professor)
            @php
                $marcacaos = $marcacaoHoje->get($professor->id);
                $mesesProf = $marcacoesMes->get($professor->id, collect());
            @endphp
            <tr>
                <td>
                    <div class="prof-cell">
                        <div class="prof-avatar">{{ strtoupper(substr($professor->name, 0, 1)) }}</div>
                        <div>
                            <div class="prof-name">{{ $professor->name }}</div>
                            <div class="prof-extra">{{ $professor->disciplina ?: '—' }}{{ $professor->nivel ? ' · ' . $professor->nivel : '' }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    @if($marcacaos)
                    <span class="estado-chip estado-{{ $marcacaos->estado }}">
                        <i class="fas fa-{{ $marcacaos->estado === 'presente' ? 'user-check' : ($marcacaos->estado === 'falta' ? 'user-times' : 'file-invoice') }}"></i>
                        {{ $marcacaos->estadoLabel }}
                        <span style="opacity:.7">· {{ \Carbon\Carbon::parse($marcacaos->data . ' ' . $marcacaos->hora)->format('H:i') }}</span>
                    </span>
                    @else
                    <span class="estado-chip estado-sem"><i class="fas fa-minus-circle"></i> Sem marcação</span>
                    @endif
                </td>
                <td style="text-align:center">
                    <div class="count-mini" style="justify-content:center">
                        <span class="c-p" title="Presenças">P {{ $mesesProf->where('estado', 'presente')->count() }}</span>
                        <span class="c-f" title="Faltas">F {{ $mesesProf->where('estado', 'falta')->count() }}</span>
                        <span class="c-j" title="Justificadas">J {{ $mesesProf->where('estado', 'justificada')->count() }}</span>
                    </div>
                </td>
                <td>
                    <div class="acc-actions">
                        <button type="button" class="acc-btn acc-presente" onclick="marcar({{ $professor->id }}, 'presente')">
                            <i class="fas fa-user-check"></i> Presente
                        </button>
                        <button type="button" class="acc-btn acc-falta" onclick="marcar({{ $professor->id }}, 'falta')">
                            <i class="fas fa-user-times"></i> Falta
                        </button>
                        <button type="button" class="acc-btn acc-justificar" onclick="marcar({{ $professor->id }}, 'justificada')">
                            <i class="fas fa-file-invoice"></i> Justificar
                        </button>
                        <button type="button" class="acc-btn acc-desfazer" onclick="desfazer({{ $professor->id }})" title="Desfazer última marcação">
                            <i class="fas fa-undo"></i>
                        </button>
                        <a class="acc-pdf" href="{{ route('auxiliar.presencas.professores.pdf', ['professor' => $professor, 'mes' => $mes, 'ano' => $ano]) }}" title="Baixar relatório PDF" target="_blank">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="table-wrap">
    <div class="empty-state">
        <i class="fas fa-chalkboard-teacher" style="font-size:28px;margin-bottom:12px;display:block;opacity:.4"></i>
        Ainda não existem professores registados no sistema.
    </div>
</div>
@endif

<script>
    function marcar(userId, estado) {
        document.getElementById('marcar-user-id').value = userId;
        document.getElementById('marcar-estado').value = estado;
        document.getElementById('frm-marcar').submit();
    }

    function desfazer(userId) {
        if (!confirm('Deseja desfazer a última marcação deste professor?')) return;
        document.getElementById('desfazer-user-id').value = userId;
        document.getElementById('frm-desfazer').submit();
    }
</script>
@endsection