@extends('layouts.app')

@section('title', 'Gestão de Salários')
@section('page-title', 'Gestão de Salários')

@section('content')
<style>
    .sal-toolbar {
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

    .sal-field { display: flex; flex-direction: column; gap: 6px; }
    .sal-field label {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .7px;
        color: var(--text-secondary);
    }

    .sal-select {
        padding: 9px 14px;
        background: var(--bg-input, #151D19);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        cursor: pointer;
    }
    .sal-select:focus { outline: none; border-color: var(--accent-green); }

    .sal-btn {
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
    .sal-btn:hover { background: #1ea34e; }

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

    .form-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .form-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 20px;
        border-bottom: 1px solid var(--border-color);
    }
    .form-card-header i { color: var(--accent-green); font-size: 15px; }
    .form-card-header .title { font-weight: 600; font-size: 13px; }
    .form-card-header .sub { font-size: 11px; color: var(--text-secondary); margin-top: 2px; }
    .form-card-body { padding: 20px; }

    .sal-form-grid {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    .sal-form-grid .sal-field { flex: 1; min-width: 200px; }

    .sal-input {
        width: 100%;
        background: var(--bg-input, #151D19);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        font-size: 13px;
        font-family: inherit;
        padding: 9px 12px;
        transition: border-color .15s;
    }
    .sal-input:focus { outline: none; border-color: var(--accent-green); }

    .input-icon-wrap { position: relative; }
    .input-icon-wrap .prefix {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        font-size: 12px;
        font-weight: 600;
        pointer-events: none;
    }
    .input-icon-wrap .sal-input { padding-left: 38px; }

    .table-wrap { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 10px; overflow-x: auto; }

    .sal-table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 900px; }

    .sal-table thead th {
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

    .sal-table td { padding: 10px 14px; border-bottom: 1px solid rgba(255,255,255,.03); vertical-align: middle; }
    .sal-table tbody tr:last-child td { border-bottom: none; }

    .prof-cell { display: flex; align-items: center; gap: 12px; }
    .prof-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 13px; flex-shrink: 0;
        background: rgba(34, 197, 94, .14); color: var(--accent-green);
    }
    .prof-name { font-weight: 500; white-space: nowrap; }
    .prof-extra { font-size: 11px; color: var(--text-secondary); margin-top: 2px; }

    .sal-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .sal-chip.ok { background: rgba(34,197,94,.12); color: var(--accent-green); }
    .sal-chip.bad { background: rgba(239,68,68,.12); color: #FCA5A5; }
    .sal-chip.warn { background: rgba(234,179,8,.12); color: var(--accent-yellow); }
    .sal-chip.neutral { background: rgba(255,255,255,.05); color: var(--text-secondary); }

    .sal-val { font-weight: 700; font-size: 13px; }
    .sal-val.ok { color: var(--accent-green); }
    .sal-val.bad { color: #F87171; }
    .sal-val.neutral { color: var(--text-primary); }

    .empty-state { text-align: center; padding: 48px 20px; color: var(--text-secondary); font-size: 12px; }
    .empty-state i { font-size: 28px; margin-bottom: 12px; display: block; opacity: .4; }

    @media (max-width: 768px) {
        .sal-toolbar { flex-direction: column; align-items: stretch; }
        .sal-btn { justify-content: center; }
        .sal-form-grid .sal-field { min-width: 100%; }
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

@can('gerir_salarios')
<div class="form-card">
    <div class="form-card-header">
        <i class="fas fa-user-plus"></i>
        <div>
            <div class="title">Cadastrar / Editar Salário</div>
            <div class="sub">Selecione o professor, introduza o salário base e o valor descontado por cada falta.</div>
        </div>
    </div>
    <form method="POST" action="#" id="salario-form" class="form-card-body">
        @csrf
        @method('PUT')

        <div class="sal-form-grid">
            <div class="sal-field" style="flex:1.4">
                <label><i class="fas fa-chalkboard-teacher" style="margin-right:5px"></i> Professor</label>
                <select name="professor_id" id="professor-select" class="sal-select" required>
                    <option value="">— Selecionar professor —</option>
                    @foreach($professores as $prof)
                    <option value="{{ $prof->id }}"
                            data-url="{{ route('diretor.salarios.update', $prof) }}"
                            data-salario="{{ $prof->salario_base !== null ? $prof->salario_base : '' }}"
                            data-desconto="{{ $prof->desconto_por_falta !== null ? $prof->desconto_por_falta : '' }}">
                        {{ $prof->name }}{{ $prof->disciplina ? ' — ' . $prof->disciplina : '' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="sal-field">
                <label><i class="fas fa-money-bill" style="margin-right:5px"></i> Salário base (Kz)</label>
                <div class="input-icon-wrap">
                    <span class="prefix">Xof</span>
                    <input type="number" step="0.01" min="0" name="salario_base" id="salario-base-input" class="sal-input" placeholder="0.00" required>
                </div>
            </div>

            <div class="sal-field">
                <label><i class="fas fa-ban" style="margin-right:5px"></i> Desconto por falta (Xof)</label>
                <div class="input-icon-wrap">
                    <span class="prefix">Xof</span>
                    <input type="number" step="0.01" min="0" name="desconto_por_falta" id="desconto-input" class="sal-input" placeholder="0.00" required>
                </div>
            </div>

            <div class="sal-field" style="flex:0 0 auto">
                <button type="submit" class="sal-btn">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </form>
</div>
@else
<div style="padding:12px 16px;background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);color:#93C5FD;border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px">
    <i class="fas fa-eye"></i>
    <span>Modo consulta: apenas o diretor pode cadastrar ou editar os salários.</span>
</div>
@endcan

<form method="GET" action="{{ route('diretor.salarios.index') }}" class="sal-toolbar">
    <div class="sal-field">
        <label><i class="fas fa-calendar-alt" style="margin-right:5px"></i> Mês de referência</label>
        <select name="mes" class="sal-select" onchange="this.form.submit()">
            @foreach($meses as $num => $nome)
            <option value="{{ $num }}" @selected($mes === $num)>{{ $nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="sal-field">
        <label><i class="fas fa-calendar" style="margin-right:5px"></i> Ano</label>
        <select name="ano" class="sal-select" onchange="this.form.submit()">
            @foreach($anosDisponiveis as $an)
            <option value="{{ $an }}" @selected($ano === (int) $an)>{{ $an }}</option>
            @endforeach
        </select>
    </div>
    <span style="font-size:12px;color:var(--text-secondary);margin-left:auto;display:flex;align-items:center;gap:6px">
        <i class="fas fa-info-circle" style="color:var(--accent-green)"></i>
        Desconto por falta aplicado automaticamente ao <strong>mês selecionado</strong>.
    </span>
</form>

@if($professores->count() === 0)
<div class="table-wrap">
    <div class="empty-state">
        <i class="fas fa-chalkboard-teacher"></i>
        <div>Não há professores registados.</div>
    </div>
</div>
@else
<div class="table-wrap">
    <table class="sal-table">
        <thead>
            <tr>
                <th>Professor</th>
                <th>Disciplina</th>
                <th style="text-align:center">Faltas ({{ $meses[$mes] }}/{{ $ano }})</th>
                <th>Salário base</th>
                <th>Desconto por falta</th>
                <th style="text-align:center">Desconto total</th>
                <th style="text-align:right">Salário líquido</th>
            </tr>
        </thead>
        <tbody>
            @foreach($professores as $prof)
            <tr>
                <td>
                    <div class="prof-cell">
                        <div class="prof-avatar">{{ strtoupper(substr($prof->name, 0, 1)) }}</div>
                        <div>
                            <div class="prof-name">{{ $prof->name }}</div>
                            <div class="prof-extra">
                                @if($prof->nivel) {{ $prof->nivel }} @endif
                                @if($prof->ano_lectivo) · {{ $prof->ano_lectivo }} @endif
                            </div>
                        </div>
                    </div>
                </td>
                <td style="color:var(--text-secondary);font-size:12px">{{ $prof->disciplina ?: '—' }}</td>
                <td style="text-align:center">
                    @if($prof->faltas_mes === 0)
                    <span class="sal-chip ok"><i class="fas fa-check-circle"></i> {{ $prof->faltas_mes }}</span>
                    @elseif($prof->faltas_mes < 3)
                    <span class="sal-chip warn"><i class="fas fa-exclamation-circle"></i> {{ $prof->faltas_mes }}</span>
                    @else
                    <span class="sal-chip bad"><i class="fas fa-times-circle"></i> {{ $prof->faltas_mes }}</span>
                    @endif
                </td>
                <td>
                    @if($prof->salario_base !== null)
                    <span class="sal-val neutral">Xof {{ number_format((float) $prof->salario_base, 2, ',', ' ') }}</span>
                    @else
                    <span class="sal-chip neutral">sem salário</span>
                    @endif
                </td>
                <td>
                    @if($prof->desconto_por_falta !== null)
                    <span class="sal-val neutral">Xof {{ number_format((float) $prof->desconto_por_falta, 2, ',', ' ') }}</span>
                    @else
                    <span class="sal-chip neutral">sem desconto</span>
                    @endif
                </td>
                <td style="text-align:center">
                    @if($prof->desconto_mes > 0)
                    <span class="sal-val bad">- Xof {{ number_format((float) $prof->desconto_mes, 2, ',', ' ') }}</span>
                    @else
                    <span class="sal-val neutral">0,00</span>
                    @endif
                </td>
                <td style="text-align:right">
                    @if($prof->salario_base !== null)
                    <span class="sal-val {{ $prof->desconto_mes > 0 ? 'bad' : 'ok' }}">Xof {{ number_format((float) $prof->liquido_mes, 2, ',', ' ') }}</span>
                    @else
                    <span class="sal-chip neutral">sem salário</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<script>
    const select = document.getElementById('professor-select');

    if (select && document.getElementById('salario-form')) {
        const form = document.getElementById('salario-form');
        const baseInput = document.getElementById('salario-base-input');
        const descontoInput = document.getElementById('desconto-input');

        select.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            form.action = opt.dataset.url || '#';
            baseInput.value = opt.dataset.salario || '';
            descontoInput.value = opt.dataset.desconto || '';
        });

        document.addEventListener('DOMContentLoaded', function() {
            if (select.options.selectedIndex > 0) {
                const opt = select.options[select.selectedIndex];
                form.action = opt.dataset.url || '#';
                baseInput.value = opt.dataset.salario || '';
                descontoInput.value = opt.dataset.desconto || '';
            }
        });
    }
</script>
@endsection
