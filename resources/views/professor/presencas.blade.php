@extends('layouts.app')

@section('title', 'Presenças dos Alunos')
@section('page-title', 'Presenças dos Alunos')

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

    .rp-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 760px; }

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

    .count-mini { display: flex; gap: 6px; justify-content: center; }

    .count-mini span {
        font-size: 11px; font-weight: 600;
        padding: 3px 8px; border-radius: 6px;
    }

    .c-p { background: rgba(34,197,94,.1); color: var(--accent-green); }
    .c-f { background: rgba(239,68,68,.1); color: #FCA5A5; }
    .c-j { background: rgba(234,179,8,.1); color: var(--accent-yellow); }

    .empty-state { text-align: center; padding: 48px 20px; color: var(--text-secondary); font-size: 12px; }

    .empty-state i { font-size: 28px; margin-bottom: 12px; display: block; opacity: .4; }

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

    .info-note {
        font-size: 12px;
        color: var(--text-secondary);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-note i { color: var(--accent-green); font-size: 12px; }

    @media (max-width: 768px) {
        .rp-toolbar { flex-direction: column; align-items: stretch; }
        .rp-btn { justify-content: center; }
    }
</style>

@if(session('success'))
<div class="alert-success">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

@if(isset($errors) && $errors->any())
<div class="alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <span>{{ $errors->first() }}</span>
</div>
@endif

<form method="GET" action="{{ route('professor.presencas') }}" class="rp-toolbar" id="frm-filtro">
    <div class="rp-field">
        <label><i class="fas fa-school" style="margin-right:5px"></i> Turma</label>
        <select name="turma_id" id="select-turma" class="rp-select" {{ $turmas->count() === 0 ? 'disabled' : '' }}>
            <option value="">— Selecione a turma —</option>
            @foreach($turmas as $t)
            <option value="{{ $t->id }}" {{ $turma && $turma->id === $t->id ? 'selected' : '' }}>
                {{ $t->nome_turma }} ({{ $t->nivel }} · {{ $t->ano_lectivo }})
            </option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="rp-btn">
        <i class="fas fa-search"></i> Ver lista
    </button>
    <span class="today-chip" style="margin-left:auto;">
        <i class="fas fa-clock" style="margin-right:6px;color:var(--accent-green)"></i>
        Hoje: <b>{{ \Carbon\Carbon::today()->format('d/m/Y') }}</b>
    </span>
</form>

@if($turmas->count() === 0)
<div class="table-wrap">
    <div class="empty-state">
        <i class="fas fa-school"></i>
        <div>Não tem turmas atribuídas no seu horário para marcar presenças.</div>
    </div>
</div>
@elseif(!$turma)
<div class="table-wrap">
    <div class="empty-state">
        <i class="fas fa-user-check"></i>
        <div>Selecione uma turma para ver a lista de alunos.</div>
    </div>
</div>
@elseif($alunos->count() === 0)
<div class="table-wrap">
    <div class="empty-state">
        <i class="fas fa-users-slash"></i>
        <div>A turma {{ $turma->nome_turma }} ainda não tem alunos registados.</div>
    </div>
</div>
@else
<form id="frm-marcar" method="POST" action="{{ route('professor.presencas.marcar') }}" hidden>
    @csrf
    <input type="hidden" name="aluno_id" id="marcar-aluno-id">
    <input type="hidden" name="turma_id" id="marcar-turma-id" value="{{ $turma->id }}">
    <input type="hidden" name="estado" id="marcar-estado">
    <input type="hidden" name="data" id="marcar-data" value="{{ \Carbon\Carbon::today()->toDateString() }}">
</form>

<form id="frm-desfazer" method="POST" action="{{ route('professor.presencas.desfazer') }}" hidden>
    @csrf
    <input type="hidden" name="aluno_id" id="desfazer-aluno-id">
    <input type="hidden" name="data" id="desfazer-data" value="{{ \Carbon\Carbon::today()->toDateString() }}">
</form>

<div class="info-note">
    <i class="fas fa-info-circle"></i>
    Marque presença ou falta. <strong>Ao marcar falta, o encarregado de educação do aluno é notificado automaticamente.</strong>
</div>

<div class="table-wrap">
    <table class="rp-table">
        <thead>
            <tr>
                <th>Aluno</th>
                <th>Hoje</th>
                <th style="text-align:center">{{ $meses[$mes] ?? $mes }}/{{ $ano }}</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alunos as $aluno)
            @php
                $marcacoesAluno = $marcacoes->get($aluno->id, collect());
                $marcacaoHoje = $marcacoesAluno->firstWhere('data', \Carbon\Carbon::today()->toDateString());
            @endphp
            <tr>
                <td>
                    <div class="prof-cell">
                        <div class="prof-avatar">{{ strtoupper(substr($aluno->name, 0, 1)) }}</div>
                        <div>
                            <div class="prof-name">{{ $aluno->name }}</div>
                            <div class="prof-extra">
                                {{ $aluno->numero ? 'Nº ' . $aluno->numero : '' }}
                                @if($aluno->genero) · {{ $aluno->genero }}@endif
                                @if($aluno->encarregado?->nome) · {{ $aluno->encarregado->nome }}@endif
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    @if($marcacaoHoje)
                    <span class="estado-chip estado-{{ $marcacaoHoje->estado }}">
                        <i class="fas fa-{{ $marcacaoHoje->estado === 'presente' ? 'user-check' : ($marcacaoHoje->estado === 'falta' ? 'user-times' : 'file-invoice') }}"></i>
                        {{ $marcacaoHoje->estadoLabel }}
                        @if($marcacaoHoje->hora)
                        <span style="opacity:.7">· {{ substr($marcacaoHoje->hora, 0, 5) }}</span>
                        @endif
                    </span>
                    @else
                    <span class="estado-chip estado-sem"><i class="fas fa-minus-circle"></i> Sem marcação</span>
                    @endif
                </td>
                <td style="text-align:center">
                    <div class="count-mini">
                        <span class="c-p" title="Presenças">{{ $marcacoesAluno->where('estado', 'presente')->count() }}</span>
                        <span class="c-f" title="Faltas">{{ $marcacoesAluno->where('estado', 'falta')->count() }}</span>
                        <span class="c-j" title="Justificadas">{{ $marcacoesAluno->where('estado', 'justificada')->count() }}</span>
                    </div>
                </td>
                <td>
                    <div class="acc-actions">
                        <button type="button" class="acc-btn acc-presente" onclick="marcar({{ $aluno->id }}, 'presente')">
                            <i class="fas fa-user-check"></i> Presente
                        </button>
                        <button type="button" class="acc-btn acc-falta" onclick="marcar({{ $aluno->id }}, 'falta')">
                            <i class="fas fa-user-times"></i> Falta
                        </button>
                        <button type="button" class="acc-btn acc-justificar" onclick="marcar({{ $aluno->id }}, 'justificada')">
                            <i class="fas fa-file-invoice"></i> Justificar
                        </button>
                        <button type="button" class="acc-btn acc-desfazer" onclick="desfazer({{ $aluno->id }})" title="Desfazer marcação de hoje">
                            <i class="fas fa-undo"></i>
                        </button>
                        <a class="acc-pdf" href="{{ route('professor.presencas.pdf', ['aluno' => $aluno, 'mes' => $mes, 'ano' => $ano]) }}" title="Baixar relatório PDF" target="_blank">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<script>
    document.getElementById('select-turma')?.addEventListener('change', function () {
        this.form.submit();
    });

    function marcar(alunoId, estado) {
        document.getElementById('marcar-aluno-id').value = alunoId;
        document.getElementById('marcar-estado').value = estado;
        document.getElementById('frm-marcar').submit();
    }

    function desfazer(alunoId) {
        if (!confirm('Deseja desfazer a marcação deste aluno hoje?')) return;
        document.getElementById('desfazer-aluno-id').value = alunoId;
        document.getElementById('frm-desfazer').submit();
    }
</script>
@endsection