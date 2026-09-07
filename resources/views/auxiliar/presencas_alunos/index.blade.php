@extends('layouts.app')

@section('title', 'Presenças e Faltas dos Alunos')
@section('page-title', 'Presenças e Faltas dos Alunos')

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

    .rp-field { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 200px; }

    .rp-field label {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .7px;
        color: var(--text-secondary);
    }

    .rp-select {
        padding: 9px 14px;
        background: var(--bg-input, #151D19);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
    }

    .rp-select:focus { outline: none; border-color: var(--accent-green); }

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

    .rp-btn-pdf {
        border: 1px solid rgba(239,68,68,.4);
        background: transparent;
        color: #FCA5A5;
        text-decoration: none;
        padding: 9px 20px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all .15s;
    }

    .rp-btn-pdf:hover { background: rgba(239,68,68,.12); }

    .info-note {
        font-size: 12px;
        color: var(--text-secondary);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-note i { color: var(--accent-green); font-size: 12px; }

    .table-wrap { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 10px; overflow-x: auto; }

    .rp-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 720px; }

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

    .cell-aluno { display: flex; align-items: center; gap: 12px; }

    .cell-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 13px; flex-shrink: 0;
        background: rgba(34, 197, 94, .14); color: var(--accent-green);
    }

    .cell-nome { font-weight: 500; white-space: nowrap; }

    .cell-extra { font-size: 11px; color: var(--text-secondary); margin-top: 2px; }

    .count-mini { display: flex; gap: 6px; justify-content: center; }

    .count-mini span { font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 6px; }

    .c-p { background: rgba(34,197,94,.1); color: var(--accent-green); }
    .c-f { background: rgba(239,68,68,.1); color: #FCA5A5; }
    .c-j { background: rgba(234,179,8,.1); color: var(--accent-yellow); }

    .taxa-ok { color: var(--accent-green); font-weight: 700; }
    .taxa-warn { color: var(--accent-yellow); font-weight: 700; }
    .taxa-bad { color: #F87171; font-weight: 700; }

    .empty-state { text-align: center; padding: 48px 20px; color: var(--text-secondary); font-size: 12px; }

    .empty-state i { font-size: 28px; margin-bottom: 12px; display: block; opacity: .4; }

    .resumo-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    .resumo-card {
        background: linear-gradient(145deg, var(--bg-card) 0%, var(--bg-hover) 100%);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        position: relative;
        overflow: hidden;
        transition: transform .15s ease, border-color .15s ease;
    }

    .resumo-card:hover { transform: translateY(-2px); border-color: rgba(34,197,94,.35); }

    .resumo-card::after {
        content: '';
        position: absolute;
        right: -22px;
        top: -22px;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(34,197,94,.12) 0%, transparent 70%);
        pointer-events: none;
    }

    .resumo-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 17px;
        flex-shrink: 0;
    }

    .resumo-icon.green { background: rgba(34,197,94,.14); color: var(--accent-green); }
    .resumo-icon.red { background: rgba(239,68,68,.14); color: #F87171; }
    .resumo-icon.yellow { background: rgba(234,179,8,.14); color: var(--accent-yellow); }
    .resumo-icon.school { background: rgba(59,130,246,.14); color: #60A5FA; }
    .resumo-icon.users { background: rgba(139,92,246,.14); color: #A78BFA; }

    .resumo-card .info { display: flex; flex-direction: column; gap: 2px; min-width: 0; }

    .resumo-card .k {
        font-size: 9px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: .8px;
        font-weight: 600;
        white-space: nowrap;
    }

    .resumo-card .v { font-size: 22px; font-weight: 700; line-height: 1.1; }

    .resumo-card .v.ok { color: var(--accent-green); }
    .resumo-card .v.bad { color: #F87171; }
    .resumo-card .v.warn { color: var(--accent-yellow); }
    .resumo-card .v.neutral { color: var(--text-primary); }

    .resumo-sub { font-size: 10px; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    @media (max-width: 1100px) { .resumo-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px) { .resumo-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { .resumo-grid { grid-template-columns: 1fr; } }

    .alerta-faltas {
        margin-top: 16px;
        padding: 12px 14px;
        border: 1px solid rgba(239,68,68,.35);
        background: rgba(239,68,68,.08);
        color: #FCA5A5;
        border-radius: 8px;
        font-size: 12px;
    }

    @media (max-width: 768px) {
        .rp-toolbar { flex-direction: column; align-items: stretch; }
        .rp-btn, .rp-btn-pdf { justify-content: center; }
    }
</style>

<form method="GET" action="{{ route('auxiliar.presencas.alunos.index') }}" class="rp-toolbar" id="frm-filtro">
    <div class="rp-field">
        <label><i class="fas fa-school" style="margin-right:5px"></i> Turma</label>
        <select name="turma_id" class="rp-select" {{ $turmas->count() === 0 ? 'disabled' : '' }}>
            <option value="">— Selecione a turma —</option>
            @foreach($turmas as $t)
            <option value="{{ $t->id }}" {{ $turma && $turma->id === $t->id ? 'selected' : '' }}>
                {{ $t->nome_turma }} ({{ $t->nivel }} · {{ $t->ano_lectivo }})
            </option>
            @endforeach
        </select>
    </div>
    <div class="rp-field">
        <label><i class="fas fa-calendar-alt" style="margin-right:5px"></i> Mês</label>
        <select name="mes" class="rp-select">
            @foreach($meses as $num => $nome)
            <option value="{{ $num }}" @selected($mes === $num)>{{ $nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="rp-field">
        <label><i class="fas fa-calendar" style="margin-right:5px"></i> Ano</label>
        <select name="ano" class="rp-select">
            @foreach($anosDisponiveis as $an)
            <option value="{{ $an }}" @selected($ano === (int) $an)>{{ $an }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="rp-btn">
        <i class="fas fa-search"></i> Ver relatório
    </button>
    @if($turma)
    <a class="rp-btn-pdf" href="{{ route('auxiliar.presencas.alunos.pdf', ['turma' => $turma, 'mes' => $mes, 'ano' => $ano]) }}" target="_blank">
        <i class="fas fa-file-pdf"></i> Baixar PDF
    </a>
    @endif
</form>

@if($turmas->count() === 0)
<div class="table-wrap">
    <div class="empty-state">
        <i class="fas fa-school"></i>
        <div>Não há turmas registadas na escola.</div>
    </div>
</div>
@elseif(!$turma)
<div class="table-wrap">
    <div class="empty-state">
        <i class="fas fa-user-check"></i>
        <div>Selecione uma turma para ver o relatório de presenças e faltas dos alunos.</div>
    </div>
</div>
@else
<div class="info-note">
    <i class="fas fa-info-circle"></i>
    Relatório de leitura — <strong>apenas consulta</strong>. A marcação de presenças é feita pelos professores.
</div>

<div class="resumo-grid">
    <div class="resumo-card">
        <div class="resumo-icon school"><i class="fas fa-school"></i></div>
        <div class="info">
            <span class="k">Turma</span>
            <span class="v neutral">{{ $turma->nome_turma }}</span>
            <span class="resumo-sub">{{ $turma->nivel }} · {{ $turma->ano_lectivo }}</span>
        </div>
    </div>
    <div class="resumo-card">
        <div class="resumo-icon users"><i class="fas fa-users"></i></div>
        <div class="info">
            <span class="k">Alunos</span>
            <span class="v neutral">{{ $alunos->count() }}</span>
            <span class="resumo-sub">matriculados</span>
        </div>
    </div>
    <div class="resumo-card">
        <div class="resumo-icon green"><i class="fas fa-user-check"></i></div>
        <div class="info">
            <span class="k">Total presenças</span>
            <span class="v ok">{{ $marcacoes->flatten(1)->where('estado', 'presente')->count() }}</span>
            <span class="resumo-sub">{{ $meses[$mes] ?? $mes }}</span>
        </div>
    </div>
    <div class="resumo-card">
        <div class="resumo-icon red"><i class="fas fa-user-times"></i></div>
        <div class="info">
            <span class="k">Total faltas</span>
            <span class="v bad">{{ $marcacoes->flatten(1)->where('estado', 'falta')->count() }}</span>
            <span class="resumo-sub">no mês selecionado</span>
        </div>
    </div>
    <div class="resumo-card">
        <div class="resumo-icon yellow"><i class="fas fa-clipboard-check"></i></div>
        <div class="info">
            <span class="k">Justificadas</span>
            <span class="v warn">{{ $marcacoes->flatten(1)->where('estado', 'justificada')->count() }}</span>
            <span class="resumo-sub">faltas justificadas</span>
        </div>
    </div>
</div>

@if($alunos->count() > 0)
<div class="table-wrap">
    <table class="rp-table">
        <thead>
            <tr>
                <th>Aluno</th>
                <th style="text-align:center">Presenças</th>
                <th style="text-align:center">Faltas</th>
                <th style="text-align:center">Justificadas</th>
                <th style="text-align:center">Taxa de assiduidade</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alunos as $aluno)
            @php
                $m = $marcacoes->get($aluno->id, collect());
                $p = $m->where('estado', 'presente')->count();
                $f = $m->where('estado', 'falta')->count();
                $j = $m->where('estado', 'justificada')->count();
                $taxa = ($p + $f) > 0 ? round(($p / ($p + $f)) * 100, 1) : null;
                $corTaxa = $taxa === null ? 'var(--text-secondary)' : ($taxa >= 75 ? 'var(--accent-green)' : ($taxa >= 50 ? 'var(--accent-yellow)' : '#F87171'));
            @endphp
            <tr>
                <td>
                    <div class="cell-aluno">
                        <div class="cell-avatar">{{ strtoupper(substr($aluno->name, 0, 1)) }}</div>
                        <div>
                            <div class="cell-nome">{{ $aluno->name }}</div>
                            <div class="cell-extra">
                                {{ $aluno->numero ? 'Nº ' . $aluno->numero : '' }}
                                @if($aluno->genero) · {{ $aluno->genero }}@endif
                            </div>
                        </div>
                    </div>
                </td>
                <td style="text-align:center">
                    <div class="count-mini"><span class="c-p">{{ $p }}</span></div>
                </td>
                <td style="text-align:center">
                    <div class="count-mini"><span class="c-f">{{ $f }}</span></div>
                </td>
                <td style="text-align:center">
                    <div class="count-mini"><span class="c-j">{{ $j }}</span></div>
                </td>
                <td style="text-align:center">
                    <span style="font-weight:700;color:{{ $corTaxa }}">{{ $taxa !== null ? $taxa . ' %' : '—' }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@php
    $alunosCom3MaisFaltas = $alunos->filter(function ($aluno) use ($marcacoes) {
        return $marcacoes->get($aluno->id, collect())->where('estado', 'falta')->count() >= 3;
    });
@endphp
@if($alunosCom3MaisFaltas->count() > 0)
<div class="alerta-faltas">
    <i class="fas fa-exclamation-triangle" style="margin-right:6px"></i>
    <strong>Atenção:</strong>
    {{ $alunosCom3MaisFaltas->count() }} aluno(s) com <strong>3 ou mais faltas</strong> neste mês:
    {{ $alunosCom3MaisFaltas->pluck('name')->implode(', ') }}.
</div>
@endif
@else
<div class="table-wrap">
    <div class="empty-state">
        <i class="fas fa-users-slash"></i>
        <div>A turma {{ $turma->nome_turma }} ainda não tem alunos registados.</div>
    </div>
</div>
@endif
@endif

<script>
    document.getElementById('frm-filtro')?.addEventListener('submit', function (e) {
        if (!this.turma_id.value) {
            e.preventDefault();
        }
    });
</script>
@endsection