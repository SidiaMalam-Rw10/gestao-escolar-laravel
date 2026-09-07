@extends('layouts.app')

@section('title', 'Minhas Turmas')
@section('page-title', 'Minhas Turmas')

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
    .page-title{font-size:20px;font-weight:700}
    .info-note{font-size:12px;color:var(--text-secondary);margin-bottom:20px;display:flex;align-items:center;gap:8px}
    .info-note i{color:var(--accent-green);font-size:12px}
    .turmas-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px}
    .turma-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:20px;transition:border-color .15s}
    .turma-card:hover{border-color:rgba(255,255,255,.1)}
    .turma-header{display:flex;justify-content:space-between;align-items:start;margin-bottom:12px}
    .turma-name{font-size:16px;font-weight:700}
    .turma-nivel{font-size:12px;color:var(--text-secondary);margin-top:2px}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600}
    .tag-periodo{background:rgba(59,130,246,.12);color:#60A5FA}
    .tag-ano{background:rgba(139,92,246,.12);color:#A78BFA}
    .turma-info{display:flex;flex-direction:column;gap:8px;font-size:12px;color:var(--text-secondary);margin-bottom:14px}
    .turma-info-row{display:flex;align-items:center;gap:8px}
    .turma-info-row i{width:14px;text-align:center;font-size:11px}
    .turma-info-row strong{color:var(--text-primary);font-weight:600}
    .aulas-title{font-size:10px;text-transform:uppercase;letter-spacing:.8px;font-weight:600;color:var(--text-secondary);padding-bottom:8px;border-bottom:1px solid var(--border-color);margin-bottom:8px}
    .aulas-list{display:flex;flex-direction:column}
    .aula-row{display:flex;align-items:center;justify-content:space-between;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.03);font-size:12px}
    .aula-row:last-child{border-bottom:none}
    .aula-dia{font-size:11px;color:#60A5FA;font-weight:600;min-width:56px}
    .aula-hora{font-size:11px;color:var(--text-secondary)}
    .aula-disc{font-weight:600;color:var(--text-primary)}
    .empty-state{text-align:center;padding:48px 20px;color:var(--text-secondary);font-size:12px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px}
    .empty-state i{font-size:28px;margin-bottom:12px;display:block;opacity:.4}
    @media(max-width:768px){.turmas-grid{grid-template-columns:1fr}}
</style>

<div class="page-header">
    <div>
        <div class="page-title">Turmas que leciona</div>
        <div style="font-size:12px;color:var(--text-secondary);margin-top:4px">Acesso de consulta — gestão apenas pela direção.</div>
    </div>
</div>

@if($turmas->count() > 0)
<div class="info-note">
    <i class="fas fa-info-circle"></i>
    Estas são as turmas em que tem aulas atribuídas no seu horário.
</div>

<div class="turmas-grid">
    @foreach($turmas as $turma)
    @php
        $aulas = ($aulasPorTurma->get($turma->id) ?? collect())->sortBy(function ($aula) {
            $pos = array_search($aula->dia_semana, ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado']);
            return [$pos === false ? 99 : $pos, $aula->hora_inicio];
        })->values();
    @endphp
    <div class="turma-card">
        <div class="turma-header">
            <div>
                <div class="turma-name">{{ $turma->nome_turma }}</div>
                <div class="turma-nivel">{{ $turma->nivel }}</div>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end">
                <span class="tag tag-periodo">{{ $turma->periodo }}</span>
                <span class="tag tag-ano">{{ $turma->ano_lectivo }}</span>
            </div>
        </div>

        <div class="turma-info">
            <div class="turma-info-row"><i class="fas fa-users"></i> <span><strong>{{ $turma->alunos->count() }}</strong> aluno(s)</span></div>
            <div class="turma-info-row">
                <i class="fas fa-user-tie"></i>
                <span>Responsável: <strong>{{ $turma->professorResponsavel?->name ?? '—' }}</strong></span>
            </div>
        </div>

        <div class="aulas-title">As suas aulas ({{ $aulas->count() }})</div>
        <div class="aulas-list">
            @foreach($aulas as $aula)
            <div class="aula-row">
                <span class="aula-dia">{{ $aula->dia_semana }}</span>
                <span class="aula-disc">{{ $aula->disciplina }}</span>
                <span class="aula-hora">{{ $aula->hora_inicio }} – {{ $aula->hora_fim }}@if($aula->sala) · {{ $aula->sala }}@endif</span>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@else
<div class="empty-state">
    <i class="fas fa-school"></i>
    <div>Não tem turmas atribuídas no seu horário.</div>
</div>
@endif
@endsection