@extends('layouts.app')

@section('title', 'Gestão de Horários')
@section('page-title', 'Gestão de Horários')

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
    .page-title{font-size:20px;font-weight:700}
    .section-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:20px;margin-bottom:24px;scroll-margin-top:80px}
    .section-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
    .section-title{font-size:15px;font-weight:700;display:flex;align-items:center;gap:10px}
    .section-title i{color:var(--accent-green);font-size:14px}
    .table-wrap{overflow-x:auto}
    .hz-table{width:100%;border-collapse:collapse;font-size:13px;min-width:560px}
    .hz-table th{text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-secondary);font-weight:600;padding:10px 12px;border-bottom:1px solid var(--border-color)}
    .hz-table td{padding:12px;border-bottom:1px solid rgba(255,255,255,.03)}
    .hz-table tr:last-child td{border-bottom:none}
    .hz-table tr:hover td{background:var(--bg-hover)}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600}
    .tag-aulas{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .tag-periodo{background:rgba(59,130,246,.12);color:#60A5FA}
    .tag-prof{background:rgba(234,179,8,.12);color:var(--accent-yellow)}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:8px 14px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:8px 14px;border-radius:6px;cursor:pointer;font-size:12px;text-decoration:none;display:inline-flex;align-items:center;gap:7px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .empty-state{text-align:center;padding:32px 20px;color:var(--text-secondary);font-size:12px}
    .empty-state i{font-size:24px;margin-bottom:10px;display:block;opacity:.3}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .alert-error{padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    @media(max-width:768px){.section-header{flex-direction:column;align-items:flex-start;gap:10px}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert-error"><i class="fas fa-exclamation-circle"></i>{{ session('error') }}</div>@endif

<div class="page-header">
    <div class="page-title">Horários por Turma e Professor</div>
</div>

<div class="section-card" id="turmas">
    <div class="section-header">
        <div class="section-title"><i class="fas fa-school"></i> Horários de Turmas</div>
        <span class="tag tag-aulas">{{ $turmas->sum('horarios_count') }} aulas</span>
    </div>
    @if($turmas->count() > 0)
    <div class="table-wrap">
        <table class="hz-table">
            <thead>
                <tr>
                    <th>Turma</th>
                    <th>Nível</th>
                    <th style="text-align:center">Período</th>
                    <th style="text-align:center">Ano</th>
                    <th style="text-align:center">Alunos</th>
                    <th style="text-align:center">Aulas</th>
                    <th style="text-align:right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($turmas as $turma)
                <tr>
                    <td style="font-weight:600">{{ $turma->nome_turma }}</td>
                    <td style="color:var(--text-secondary)">{{ $turma->nivel }}</td>
                    <td style="text-align:center"><span class="tag tag-periodo">{{ $turma->periodo }}</span></td>
                    <td style="text-align:center;color:var(--text-secondary)">{{ $turma->ano_lectivo }}</td>
                    <td style="text-align:center;color:var(--text-secondary)">{{ $turma->alunos_count }}</td>
                    <td style="text-align:center"><span class="tag tag-aulas">{{ $turma->horarios_count }}</span></td>
                    <td style="text-align:right">
                        <a href="{{ route('admin.horarios.turma', $turma) }}" class="btn"><i class="fas fa-calendar-alt"></i> Gerir horário</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state"><i class="fas fa-school"></i><div>Nenhuma turma registada.</div></div>
    @endif
</div>

<div class="section-card" id="professores">
    <div class="section-header">
        <div class="section-title"><i class="fas fa-chalkboard-teacher"></i> Horários de Professores</div>
        <span class="tag tag-aulas">{{ $professores->sum('horarios_como_professor_count') }} aulas</span>
    </div>
    @if($professores->count() > 0)
    <div class="table-wrap">
        <table class="hz-table">
            <thead>
                <tr>
                    <th>Professor</th>
                    <th>Disciplina</th>
                    <th style="text-align:center">Aulas</th>
                    <th style="text-align:right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($professores as $professor)
                <tr>
                    <td style="font-weight:600">{{ $professor->name }}</td>
                    <td style="color:var(--text-secondary)">{{ $professor->disciplina ?? '—' }}</td>
                    <td style="text-align:center"><span class="tag tag-aulas">{{ $professor->horarios_como_professor_count }}</span></td>
                    <td style="text-align:right">
                        <a href="{{ route('admin.horarios.professor', $professor) }}" class="btn"><i class="fas fa-calendar-alt"></i> Gerir horário</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state"><i class="fas fa-chalkboard-teacher"></i><div>Nenhum professor registado.</div></div>
    @endif
</div>
@endsection