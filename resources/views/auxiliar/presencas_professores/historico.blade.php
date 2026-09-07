@extends('layouts.app')

@section('title', 'Histórico de Presenças dos Professores')
@section('page-title', 'Histórico de Marcações — Professores')

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
        margin-left: auto;
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

    .prof-list { display: flex; flex-direction: column; gap: 14px; margin-bottom: 20px; }

    .prof-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        overflow: hidden;
    }

    .prof-card-header {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border-color);
        cursor: pointer;
        user-select: none;
    }

    .prof-card-header:hover { background: var(--bg-hover); }

    .prof-avatar {
        width: 38px; height: 38px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 14px; flex-shrink: 0;
        background: rgba(34, 197, 94, .14); color: var(--accent-green);
    }

    .prof-info { flex: 1; min-width: 0; }
    .prof-name { font-weight: 600; font-size: 14px; }
    .prof-extra { font-size: 11px; color: var(--text-secondary); margin-top: 2px; }

    .prof-total { display: flex; gap: 8px; flex-wrap: wrap; }

    .pill {
        padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;
        display: inline-flex; align-items: center; gap: 4px;
    }

    .pill-p { background: rgba(34,197,94,.12); color: var(--accent-green); }
    .pill-f { background: rgba(239,68,68,.12); color: #FCA5A5; }
    .pill-j { background: rgba(234,179,8,.12); color: var(--accent-yellow); }
    .pill-mes { background: rgba(255,255,255,.05); color: var(--text-secondary); }

    .prof-card-body { padding: 14px 18px; }

    .rp-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 560px; }

    .rp-table thead th {
        text-align: left;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: var(--text-secondary);
        font-weight: 600;
        padding: 10px 12px;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    .rp-table td { padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,.03); }

    .rp-table tbody tr:last-child td { border-bottom: none; }

    .rp-table tbody tr:hover { background: var(--bg-hover); }

    .chip {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 9px; border-radius: 6px;
        font-size: 11px; font-weight: 600; white-space: nowrap;
    }

    .chip-presente { background: rgba(34,197,94,.12); color: var(--accent-green); }
    .chip-falta { background: rgba(239,68,68,.12); color: #FCA5A5; }
    .chip-justificada { background: rgba(234,179,8,.12); color: var(--accent-yellow); }

    .pdf-link {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 9px; border-radius: 6px;
        font-size: 11px; font-weight: 600;
        background: rgba(239,68,68,.1); color: #FCA5A5;
        text-decoration: none; transition: background .12s;
    }

    .pdf-link:hover { background: rgba(239,68,68,.2); }

    .data-cell { font-weight: 500; white-space: nowrap; }
    .hora-cell { color: var(--text-secondary); white-space: nowrap; }

    .empty-state { text-align: center; padding: 48px 20px; color: var(--text-secondary); font-size: 12px; }

    .alert-warning {
        padding: 12px 16px;
        background: rgba(234,179,8,.08);
        border: 1px solid rgba(234,179,8,.25);
        color: var(--accent-yellow);
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

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

    @media (max-width: 768px) {
        .rp-toolbar { flex-direction: column; align-items: stretch; }
        .rp-btn, .rp-btn-link { justify-content: center; margin-left: 0; }
    }
</style>

@if(session('success'))
<div class="alert-success">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<form method="GET" action="{{ route('auxiliar.presencas.professores.historico') }}" class="rp-toolbar">
    <div class="rp-field">
        <label>Professor</label>
        <select name="professor" class="rp-select">
            <option value="">Todos os professores</option>
            @foreach($professores as $prof)
            <option value="{{ $prof->id }}" {{ request('professor') == $prof->id ? 'selected' : '' }}>{{ $prof->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="rp-field">
        <label>Mês</label>
        <select name="mes" class="rp-select">
            <option value="">Todos os meses</option>
            @foreach($meses as $numero => $nome)
            <option value="{{ $numero }}" {{ request('mes') == $numero ? 'selected' : '' }}>{{ $nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="rp-field">
        <label>Ano</label>
        <input type="number" name="ano" value="{{ request('ano', '') }}" min="2020" max="2030" class="rp-input" style="width:110px;" placeholder="Todos">
    </div>
    <button type="submit" class="rp-btn">
        <i class="fas fa-filter"></i> Filtrar
    </button>
    <a href="{{ route('auxiliar.presencas.professores.index') }}" class="rp-btn-link">
        <i class="fas fa-clipboard-list"></i> Folha do mês
    </a>
</form>

@if($filtroProfessor)
<div class="alert-warning">
    <i class="fas fa-info-circle"></i>
    <span>A mostrar histórico de <strong>{{ $filtroProfessor->name }}</strong></span>
</div>
@endif

@if($marcacoes->count() > 0)
<div class="rp-stats">
    <div class="rp-stat">
        <div class="h">Dias registados</div>
        <div class="v">{{ $diasRegistados }}</div>
    </div>
    <div class="rp-stat">
        <div class="h">Presenças</div>
        <div class="v" style="color:var(--accent-green)">{{ $totalPresentes }}</div>
    </div>
    <div class="rp-stat">
        <div class="h">Faltas</div>
        <div class="v" style="color:#FCA5A5">{{ $totalFaltas }}</div>
    </div>
    <div class="rp-stat">
        <div class="h">Justificadas</div>
        <div class="v">{{ $totalJustificadas }}</div>
    </div>
</div>

<div class="prof-list">
    @foreach($agrupados as $userId => $marcacoesProf)
    @php $prof = $marcacoesProf->first()->user; @endphp
    <div class="prof-card">
        <div class="prof-card-header" onclick="document.getElementById('body-prof-{{ $prof->id ?? $userId }}').classList.toggle('open-corpo')">
            <div class="prof-avatar">{{ strtoupper(substr($prof->name ?? '?', 0, 1)) }}</div>
            <div class="prof-info">
                <div class="prof-name">{{ $prof->name ?? 'Professor removido' }}</div>
                <div class="prof-extra">{{ $prof->disciplina ?: '—' }}{{ $prof->nivel ? ' · ' . $prof->nivel : '' }}</div>
            </div>
            <div class="prof-total">
                <span class="pill pill-p"><i class="fas fa-user-check"></i>{{ $marcacoesProf->where('estado', 'presente')->count() }}</span>
                <span class="pill pill-f"><i class="fas fa-user-times"></i>{{ $marcacoesProf->where('estado', 'falta')->count() }}</span>
                <span class="pill pill-j"><i class="fas fa-file-invoice"></i>{{ $marcacoesProf->where('estado', 'justificada')->count() }}</span>
                <span class="pill pill-mes"><i class="fas fa-calendar-day"></i>{{ $marcacoesProf->pluck('data')->unique()->count() }} dias</span>
            </div>
            <i class="fas fa-chevron-down" style="color:var(--text-secondary);font-size:11px;"></i>
        </div>
        <div class="prof-card-body" id="body-prof-{{ $prof->id ?? $userId }}">
            <table class="rp-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th style="text-align:center">PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($marcacoesProf as $m)
                    <tr>
                        <td class="data-cell">
                            <i class="fas fa-calendar-day" style="margin-right:6px;color:var(--text-secondary);font-size:11px;"></i>
                            {{ \Carbon\Carbon::parse($m->data)->format('d/m/Y') }}
                        </td>
                        <td class="hora-cell">
                            <i class="fas fa-clock" style="margin-right:6px;color:var(--text-secondary);font-size:11px;"></i>
                            {{ $m->hora }}
                        </td>
                        <td>
                            <span class="chip chip-{{ $m->estado }}">
                                <i class="fas fa-{{ $m->estado === 'presente' ? 'user-check' : ($m->estado === 'falta' ? 'user-times' : 'file-invoice') }}"></i>
                                {{ $m->estadoLabel }}
                            </span>
                        </td>
                        <td style="text-align:center">
                            <a class="pdf-link" href="{{ route('auxiliar.presencas.professores.pdf', ['professor' => $prof->id ?? $userId, 'mes' => $m->mes, 'ano' => $m->ano]) }}" target="_blank">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="table-wrap">
    <div class="empty-state">
        <i class="fas fa-history" style="font-size:28px;margin-bottom:12px;display:block;opacity:.4"></i>
        Ainda não existem marcações de presenças de professores.
    </div>
</div>
@endif

<style>
    .open-corpo { display: block; }
    .prof-card-body { display: none; }
</style>
@endsection