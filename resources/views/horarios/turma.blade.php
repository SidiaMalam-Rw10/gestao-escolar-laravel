@extends('layouts.app')

@section('title', 'Horário da Turma')
@section('page-title', 'Horário da Turma')

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
    .page-title{font-size:20px;font-weight:700}
    .back-link{font-size:12px;color:var(--text-secondary);text-decoration:none;display:inline-flex;align-items:center;gap:6px;margin-bottom:12px}
    .back-link:hover{color:var(--text-primary)}
    .stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px}
    .stat-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:8px;padding:16px}
    .stat-label{font-size:10px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.8px;font-weight:600;margin-bottom:8px}
    .stat-value{font-size:22px;font-weight:700}
    .card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:20px;margin-bottom:24px}
    .card-title{font-size:14px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:10px}
    .card-title i{color:var(--accent-green);font-size:13px}
    .table-wrap{overflow-x:auto}
    .hz-table{width:100%;border-collapse:collapse;font-size:13px;min-width:560px}
    .hz-table th{text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-secondary);font-weight:600;padding:10px 12px;border-bottom:1px solid var(--border-color)}
    .hz-table td{padding:12px;border-bottom:1px solid rgba(255,255,255,.03)}
    .hz-table tr:last-child td{border-bottom:none}
    .hz-table tr:hover td{background:var(--bg-hover)}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600}
    .tag-dia{background:rgba(96,165,250,.12);color:#60A5FA}
    .tag-prof{background:rgba(234,179,8,.12);color:var(--accent-yellow)}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:9px 16px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn-primary:hover{background:#1ea34e}
    .btn-danger{background:transparent;border:none;color:var(--text-secondary);cursor:pointer;font-size:13px;padding:6px 8px;border-radius:6px;transition:all .15s}
    .btn-danger:hover{color:#FCA5A5;background:rgba(239,68,68,.1)}
    .form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px}
    .form-field{display:flex;flex-direction:column;gap:6px}
    .form-label{font-size:11px;font-weight:600;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.5px}
    .form-input{padding:10px 12px;background:var(--bg-main);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s}
    .form-input:focus{outline:none;border-color:var(--accent-green)}
    .form-select{padding:10px 12px;background:var(--bg-main);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;cursor:pointer}
    .form-select:focus{outline:none;border-color:var(--accent-green)}
    .form-actions{display:flex;justify-content:flex-end;margin-top:18px}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .alert-error{padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .empty-state{text-align:center;padding:32px 20px;color:var(--text-secondary);font-size:12px}
    .text-error{font-size:11px;color:#FCA5A5;margin-top:4px}
    @media(max-width:768px){.stats-grid{grid-template-columns:1fr}.form-grid{grid-template-columns:1fr 1fr}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert-error"><i class="fas fa-exclamation-circle"></i>{{ session('error') }}</div>@endif

<a href="{{ route('admin.horarios.index') }}" class="back-link"><i class="fas fa-arrow-left"></i> Voltar à gestão de horários</a>

<div class="page-header">
    <div>
        <div class="page-title">Horário da turma {{ $turma->nome_turma }}</div>
        <div style="font-size:12px;color:var(--text-secondary);margin-top:4px">{{ $turma->nivel }} · Período {{ $turma->periodo }} · {{ $turma->ano_lectivo }}</div>
    </div>
    <a href="{{ route('admin.turmas.show', $turma) }}" class="btn-primary"><i class="fas fa-users"></i> Ver alunos da turma</a>
</div>

<div class="stats-grid">
    <div class="stat-card"><div class="stat-label">Aulas no horário</div><div class="stat-value" style="color:var(--accent-green)">{{ $horarios->count() }}</div></div>
    <div class="stat-card"><div class="stat-label">Alunos na turma</div><div class="stat-value">{{ $turma->alunos->count() }}</div></div>
    <div class="stat-card"><div class="stat-label">Dias com aulas</div><div class="stat-value" style="color:#60A5FA">{{ $horarios->pluck('dia_semana')->unique()->count() }}</div></div>
</div>

<div class="card">
    <div class="card-title"><i class="fas fa-list-alt"></i> Aulas já atribuídas</div>
    @if($horarios->count() > 0)
    <div class="table-wrap">
        <table class="hz-table">
            <thead>
                <tr>
                    <th>Dia</th>
                    <th>Hora</th>
                    <th>Disciplina</th>
                    <th>Professor</th>
                    <th style="text-align:center">Sala</th>
                    <th style="text-align:right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($horarios as $aula)
                <tr>
                    <td><span class="tag tag-dia">{{ $aula->dia_semana }}</span></td>
                    <td style="font-weight:500">{{ $aula->hora_inicio }} – {{ $aula->hora_fim }}</td>
                    <td style="font-weight:600">{{ $aula->disciplina }}</td>
                    <td>@if($aula->professor)<span class="tag tag-prof">{{ $aula->professor->name }}</span>@else <span style="color:var(--text-secondary)">—</span> @endif</td>
                    <td style="text-align:center;color:var(--text-secondary)">{{ $aula->sala ?? '—' }}</td>
                    <td style="text-align:right">
                        <form method="POST" action="{{ route('admin.horarios.destroy', $aula) }}" style="display:inline" onsubmit="return confirm('Remover esta aula do horário?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger" title="Remover"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state"><i class="fas fa-calendar-times"></i><div>Esta turma ainda não tem aulas no horário.</div></div>
    @endif
</div>

<div class="card">
    <div class="card-title"><i class="fas fa-plus-circle"></i> Adicionar aula ao horário da turma</div>
    <form method="POST" action="{{ route('admin.horarios.turma.store', $turma) }}">
        @csrf
        <div class="form-grid">
            <div class="form-field">
                <label class="form-label">Dia da semana</label>
                <select name="dia_semana" class="form-select" required>
                    <option value="">Selecionar dia</option>
                    @foreach(\App\Http\Controllers\HorarioController::DIAS as $dia)
                    <option value="{{ $dia }}" {{ old('dia_semana') === $dia ? 'selected' : '' }}>{{ $dia }}</option>
                    @endforeach
                </select>
                @error('dia_semana')<div class="text-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-field">
                <label class="form-label">Hora início</label>
                <input type="time" name="hora_inicio" class="form-input" value="{{ old('hora_inicio') }}" required>
                @error('hora_inicio')<div class="text-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-field">
                <label class="form-label">Hora fim</label>
                <input type="time" name="hora_fim" class="form-input" value="{{ old('hora_fim') }}" required>
                @error('hora_fim')<div class="text-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-field">
                <label class="form-label">Disciplina</label>
                <input type="text" name="disciplina" class="form-input" value="{{ old('disciplina') }}" placeholder="Ex: Matemática" required>
                @error('disciplina')<div class="text-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-field">
                <label class="form-label">Professor</label>
                <select name="professor_id" class="form-select" required>
                    <option value="">Selecionar professor</option>
                    @foreach($professores as $professor)
                    <option value="{{ $professor->id }}" {{ old('professor_id') == $professor->id ? 'selected' : '' }}>{{ $professor->name }}@if($professor->disciplina) ({{ $professor->disciplina }})@endif</option>
                    @endforeach
                </select>
                @error('professor_id')<div class="text-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-field">
                <label class="form-label">Sala (opcional)</label>
                <input type="text" name="sala" class="form-input" value="{{ old('sala') }}" placeholder="Ex: Sala 12">
                @error('sala')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Adicionar aula</button>
        </div>
    </form>
</div>
@endsection