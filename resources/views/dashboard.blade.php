@extends('layouts.app')
@php use App\Models\Configuracao; @endphp

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div style="width: 100%; overflow: hidden;">
<style>
    /* Container constraints */
    .dashboard-wrapper {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }

    /* Welcome Section */
    .welcome-banner {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 24px;
        border: 1px solid var(--border-color);
        min-height: 200px;
        display: flex;
        align-items: center;
    }

    .welcome-banner .welcome-bg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .welcome-banner .welcome-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(11, 15, 13, 0.92) 0%, rgba(11, 15, 13, 0.55) 60%, rgba(11, 15, 13, 0.15) 100%);
    }

    .welcome-banner .welcome-content {
        position: relative;
        z-index: 2;
        padding: 32px;
    }

    .date-header {
        color: var(--text-secondary);
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .welcome-title {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 6px;
        line-height: 1.2;
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.5);
    }

    .welcome-subtitle {
        color: var(--text-secondary);
        font-size: 13px;
        margin-bottom: 24px;
    }

    .welcome-actions {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 32px;
    }

    .btn-link {
        color: var(--text-primary);
        text-decoration: none;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 6px;
        transition: all 0.15s;
    }

    .btn-link:hover {
        background: var(--bg-card);
    }

    /* Metrics Grid */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }

    .metric-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 20px;
        position: relative;
        transition: all 0.15s;
        z-index: 1;
    }

    .metric-card:hover {
        border-color: rgba(255, 255, 255, 0.15);
        z-index: 2;
    }

    .metric-header {
        font-size: 10px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .metric-value {
        font-size: 36px;
        font-weight: 700;
        line-height: 1;
    }

    .metric-icon {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 32px;
        height: 32px;
        display: grid;
        place-items: center;
        font-size: 16px;
    }

    /* Chart Card */
    .chart-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 28px;
        min-height: 260px;
        position: relative;
        z-index: 1;
    }

    .chart-card:hover {
        border-color: rgba(255, 255, 255, 0.1);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .chart-title {
        font-size: 14px;
        font-weight: 600;
    }

    .chart-subtitle {
        font-size: 12px;
        color: var(--text-secondary);
    }

    .chart-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 180px;
        color: var(--text-secondary);
        font-size: 12px;
    }

    /* Two Column Grid */
    .grid-2col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 28px;
    }

    .list-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 20px;
        position: relative;
        z-index: 1;
    }

    .list-card:hover {
        border-color: rgba(255, 255, 255, 0.1);
    }

    .list-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }

    .list-title {
        font-size: 14px;
        font-weight: 600;
    }

    .list-actions {
        display: flex;
        gap: 12px;
        font-size: 12px;
        color: var(--text-secondary);
    }

    .list-actions a {
        color: var(--text-secondary);
        text-decoration: none;
        transition: color 0.15s;
    }

    .list-actions a:hover {
        color: var(--text-primary);
    }

    .list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        font-size: 13px;
    }

    .list-item:last-child {
        border-bottom: none;
    }

    .tag {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .tag-blue {
        background: rgba(59, 130, 246, 0.12);
        color: #60A5FA;
    }

    .tag-green {
        background: rgba(34, 197, 94, 0.12);
        color: var(--accent-green);
    }

    .tag-aluno {
        background: rgba(234, 179, 8, 0.12);
        color: var(--accent-yellow);
    }

    .tag-ativo {
        background: rgba(34, 197, 94, 0.12);
        color: var(--accent-green);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-secondary);
        font-size: 12px;
    }

    /* Cards genéricos do dashboard */
    .card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 20px;
        position: relative;
        z-index: 1;
    }

    .card-header {
        font-size: 10px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .card-value {
        font-size: 28px;
        font-weight: 700;
        line-height: 1;
    }

    .card-icon {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 16px;
    }

    .card-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }

    .card-section-title {
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .card-section-actions {
        display: flex;
        gap: 12px;
        font-size: 12px;
        color: var(--text-secondary);
    }

    .card-section-actions a {
        color: var(--text-secondary);
        text-decoration: none;
        transition: color 0.15s;
    }

    .card-section-actions a:hover {
        color: var(--text-primary);
    }

    .list-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        font-size: 13px;
    }

    .list-row:last-child {
        border-bottom: none;
    }

    /* Bloco de Turma (design igual ao do professor) */
    .turma-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 20px;
        transition: border-color 0.15s;
    }

    .turma-card:hover {
        border-color: rgba(255, 255, 255, 0.1);
    }

    .turma-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 12px;
    }

    .turma-name {
        font-size: 16px;
        font-weight: 700;
    }

    .turma-nivel {
        font-size: 12px;
        color: var(--text-secondary);
        margin-top: 2px;
    }

    .tag-periodo {
        background: rgba(59, 130, 246, 0.12);
        color: #60A5FA;
    }

    .tag-ano {
        background: rgba(139, 92, 246, 0.12);
        color: #A78BFA;
    }

    .turma-info {
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-size: 12px;
        color: var(--text-secondary);
        margin-bottom: 14px;
    }

    .turma-info-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .turma-info-row i {
        width: 14px;
        text-align: center;
        font-size: 11px;
    }

    .turma-info-row strong {
        color: var(--text-primary);
        font-weight: 600;
    }

    .aulas-title {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 600;
        color: var(--text-secondary);
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 8px;
    }

    .aulas-list {
        display: flex;
        flex-direction: column;
    }

    .aula-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        font-size: 12px;
    }

    .aula-row:last-child {
        border-bottom: none;
    }

    .aula-dia {
        font-size: 11px;
        color: #60A5FA;
        font-weight: 600;
        min-width: 56px;
    }

    .aula-hora {
        font-size: 11px;
        color: var(--text-secondary);
    }

    .aula-disc {
        font-weight: 600;
        color: var(--text-primary);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        padding: 10px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s;
        font-weight: 500;
    }

    .btn:hover {
        border-color: rgba(255, 255, 255, 0.2);
        background: var(--bg-hover);
    }

    .btn i {
        font-size: 12px;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .welcome-title {
            font-size: 28px;
        }

        .metrics-grid {
            grid-template-columns: 1fr;
        }

        .grid-2col {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Welcome Section -->
<div class="welcome-banner">
    <img src="{{ asset('Image.jpeg') }}" alt="" class="welcome-bg">
    <div class="welcome-overlay"></div>
    <div class="welcome-content">
        <div class="date-header">{{ strtoupper(\Carbon\Carbon::now()->locale('pt')->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}</div>
        <h1 class="welcome-title">Olá, {{ explode(' ', auth()->user()->name)[0] }}</h1>
        <p class="welcome-subtitle">Aqui estão os assuntos que exigem sua atenção hoje.</p>
    </div>
</div>

<div class="welcome-actions">
    @if(auth()->user()->isAdmin() || auth()->user()->isDiretor())
    <a href="#" class="btn-link">
        <!--<span>Voir les projets</span>
        <i class="fas fa-arrow-right" style="font-size: 11px;"></i>-->
    </a>
    @endif
</div>

<!-- Metrics -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-header">Alunos</div>
        <div class="metric-value">{{ $totalAlunos }}</div>
        <div class="metric-icon" style="color: #60A5FA;">
            <i class="fas fa-user-graduate"></i>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-header">Professores</div>
        <div class="metric-value">{{ $totalProfessores }}</div>
        <div class="metric-icon" style="color: var(--accent-yellow);">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-header">Turmas</div>
        <div class="metric-value">{{ $totalTurmas }}</div>
        <div class="metric-icon" style="color: var(--accent-green);">
            <i class="fas fa-school"></i>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-header">Departamentos</div>
        <div class="metric-value">{{ $totalDepartamentos }}</div>
        <div class="metric-icon" style="color: var(--accent-yellow);">
            <i class="fas fa-building"></i>
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin() || auth()->user()->isDiretor() || auth()->user()->isFinanceiro())
<!-- Chart -->
<div class="chart-card">
    <div class="chart-header">
        <div>
            <div class="chart-title">Gráfico de progresso</div>
            <div class="chart-subtitle">Receitas do ano {{ date('Y') }} por mês (Xof)</div>
        </div>
    </div>
    @php
        $meses = [1 => 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $maxReceita = max($receitasMes->values()->all()) > 0 ? max($receitasMes->values()->all()) : 1;
    @endphp
    <div style="display: flex; align-items: flex-end; gap: 6px; height: 180px; padding: 0 4px;">
        @foreach($meses as $num => $nome)
            @php
                $total = $receitasMes[$num] ?? 0;
                $altura = round(($total / $maxReceita) * 100, 1);
            @endphp
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:6px; height:100%; justify-content:flex-end;">
                <div style="font-size:10px; color:var(--text-secondary);">{{ $total > 0 ? number_format($total, 0, ',', ' ') : '' }}</div>
                <div style="width:100%; max-width:38px; height:{{ $altura }}%; min-height:{{ $total > 0 ? 4 : 2 }}px; background:{{ $total > 0 ? 'linear-gradient(90deg,#1EA34E,#34D399)' : 'rgba(255,255,255,.08)' }}; border-radius:4px 4px 0 0;" title="{{ $nome }}: {{ number_format($total, 0, ',', ' ') }} Xof"></div>
                <div style="font-size:10px; color:var(--text-secondary);">{{ $nome }}</div>
            </div>
        @endforeach
    </div>
</div>

<!-- Lists Grid removido -->

<!-- Action Buttons -->
<!--<div class="action-buttons">
    <span style="color: var(--text-secondary); font-size: 12px; font-weight: 600; padding: 10px 0;">Ações rápidas:</span>
    <button class="btn"><i class="fas fa-plus"></i> Novo pedido</button>
    <button class="btn"><i class="fas fa-plus"></i> Nova proposta</button>
    <button class="btn"><i class="fas fa-plus"></i> Novo projeto</button>
    <button class="btn"><i class="fas fa-plus"></i> Nova tarefa</button>
</div>
@endif-->

@if(auth()->user()->isAluno())
<!-- Dashboard do Aluno -->
<div class="metrics-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
    <div class="card">
        <div class="card-header">Média Geral</div>
        <div class="card-value" style="color: var(--accent-green);">
            @if($notas->count() > 0)
                {{ number_format($notas->avg('mg'), 1) }}
            @else
                --
            @endif
        </div>
        <div class="card-icon" style="color: var(--accent-green);">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Presenças</div>
        <div class="card-value" style="color: {{ $taxaAssiduidade !== null && $taxaAssiduidade >= 75 ? 'var(--accent-green)' : ($taxaAssiduidade !== null && $taxaAssiduidade >= 50 ? '#FCD34D' : 'var(--text-primary)') }};">
            @if($taxaAssiduidade !== null)
                {{ number_format($taxaAssiduidade, 1) }}<span style="font-size: 16px; color: var(--text-secondary);">%</span>
            @else
                --
            @endif
        </div>
        <div class="card-icon" style="color: #60A5FA;">
            <i class="fas fa-user-check"></i>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Pendências</div>
        <div class="card-value" style="color: {{ $pendencias > 0 ? '#FB923C' : 'var(--accent-green)' }};">{{ $pendencias }}</div>
        <div class="card-icon" style="color: #FB923C;">
            <i class="fas fa-exclamation-circle"></i>
        </div>
    </div>
</div>

<div class="grid-2col">
    <!-- Meus Dados e Minha Turma -->
    <div class="card">
        <div class="card-section-header">
            <div class="card-section-title">
                <i class="fas fa-user" style="color: var(--accent-green);"></i>
                <span>Meus Dados e Minha Turma</span>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 16px;">
            <div style="width: 52px; height: 52px; border-radius: 50%; background: var(--accent-green); color: #000; display: grid; place-items: center; font-weight: 700; font-size: 22px; flex-shrink: 0;">
                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div style="min-width: 0;">
                <div style="font-size: 17px; font-weight: 700;">{{ auth()->user()->name }}</div>
                <div style="font-size: 12px; color: var(--text-secondary);">{{ auth()->user()->email ?? auth()->user()->username }}</div>
            </div>
            @if(auth()->user()->is_active)
            <span class="tag tag-ativo" style="margin-left: auto;">Ativo</span>
            @endif
        </div>

        <div style="display: flex; flex-direction: column;">
            @if(auth()->user()->numero)
            <div class="list-row">
                <span style="color: var(--text-secondary); font-size: 13px;">Número</span>
                <span style="font-weight: 600; font-size: 14px;">{{ auth()->user()->numero }}</span>
            </div>
            @endif
            <div class="list-row">
                <span style="color: var(--text-secondary); font-size: 13px;">Usuário</span>
                <span style="font-weight: 600; font-size: 14px;">{{ auth()->user()->username }}</span>
            </div>
            @if(auth()->user()->genero)
            <div class="list-row">
                <span style="color: var(--text-secondary); font-size: 13px;">Género</span>
                <span style="font-weight: 600; font-size: 14px;">{{ auth()->user()->genero }}</span>
            </div>
            @endif
        </div>

        <div class="aulas-title" style="margin-top: 16px;">Minha Turma</div>
        @if($turma)
        <div class="turma-card">
            <div class="turma-header">
                <div>
                    <div class="turma-name">{{ $turma->nome_turma }}</div>
                    <div class="turma-nivel">{{ $turma->nivel }}</div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 6px; align-items: flex-end;">
                    <span class="tag tag-periodo">{{ $turma->periodo }}</span>
                    <span class="tag tag-ano">{{ $turma->ano_lectivo }}</span>
                </div>
            </div>
            <div class="turma-info">
                <div class="turma-info-row">
                    <i class="fas fa-users"></i>
                    <span><strong>{{ $turma->alunos->count() }}</strong> aluno(s)</span>
                </div>
                <div class="turma-info-row">
                    <i class="fas fa-user-tie"></i>
                    <span>Responsável: <strong>{{ $turma->professorResponsavel?->name ?? '—' }}</strong></span>
                </div>
            </div>

            <div class="aulas-title">Horário da turma ({{ $horarios->count() }})</div>
            @php
                $diasOrdem = ['Segunda' => 1, 'Terça' => 2, 'Quarta' => 3, 'Quinta' => 4, 'Sexta' => 5, 'Sábado' => 6, 'Domingo' => 7];
                $aulasTurma = $horarios->sortBy(fn ($a) => $diasOrdem[$a->dia_semana] ?? 99)->take(8);
            @endphp
            @if($aulasTurma->count() > 0)
            <div class="aulas-list">
                @foreach($aulasTurma as $aula)
                <div class="aula-row">
                    <span class="aula-dia">{{ $aula->dia_semana }}</span>
                    <span class="aula-disc">{{ $aula->disciplina }}</span>
                    <span class="aula-hora">{{ $aula->hora_inicio }}–{{ $aula->hora_fim }}@if($aula->sala) · {{ $aula->sala }}@endif</span>
                </div>
                @endforeach
            </div>
            @else
            <div style="font-size: 12px; color: var(--text-secondary); padding: 10px 0;">Sem aulas no horário.</div>
            @endif

            <div style="margin-top: 14px;">
                <a href="{{ route('aluno.minhas.horario') }}" class="btn" style="padding: 7px 12px; font-size: 12px;">
                    <i class="fas fa-calendar-alt"></i> Ver horário completo
                </a>
            </div>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">🏫</div>
            <p>Você ainda não foi atribuído a uma turma.</p>
        </div>
        @endif
    </div>

    <!-- Últimas Notas -->
    <div class="card">
        <div class="card-section-header">
            <div class="card-section-title">
                <i class="fas fa-graduation-cap" style="color: var(--accent-green);"></i>
                <span>Últimas Notas</span>
            </div>
            <div class="card-section-actions">
                <a href="#">Ver todas →</a>
            </div>
        </div>
        @if($notas->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 2px;">
            @foreach($notas->take(5) as $nota)
            <div class="list-row">
                <span style="font-size: 13px; font-weight: 500;">{{ $nota->disciplina }}</span>
                <span style="font-size: 20px; font-weight: 700; color: {{ $nota->mg >= 10 ? 'var(--accent-green)' : '#FB923C' }};">
                    {{ number_format($nota->mg, 1) }}
                </span>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">📝</div>
            <p>Ainda não há notas registradas.</p>
        </div>
        @endif
    </div>
</div>
@endif

@if(auth()->user()->isProfessor())
<!-- Dashboard do Professor -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-section-header">
        <div class="card-section-title">
            <i class="fas fa-calendar-alt" style="color: var(--accent-green);"></i>
            <span>Meus Horários</span>
        </div>
        <div class="card-section-actions">
            <a href="#"><i class="fas fa-plus"></i> Adicionar</a>
            <span style="color: var(--border-color);">|</span>
            <a href="#">Ver todos →</a>
        </div>
    </div>
    @if($horarios->count() > 0)
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px;">
        @foreach($horarios as $horario)
        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); border-radius: 10px; padding: 16px; transition: all 0.2s;" onmouseover="this.style.borderColor='rgba(255, 255, 255, 0.15)'" onmouseout="this.style.borderColor='var(--border-color)'">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                <div style="font-weight: 700; color: #60A5FA; font-size: 14px;">
                    {{ $horario->dia_semana }}
                </div>
                <span class="tag tag-green" style="font-size: 10px;">Ativo</span>
            </div>
            <div style="color: var(--text-secondary); font-size: 13px; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-clock" style="font-size: 11px;"></i>
                {{ $horario->hora_inicio }} - {{ $horario->hora_fim }}
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 11px; color: var(--text-secondary);">Turma</span>
                    <span style="font-size: 13px; font-weight: 600;">{{ $horario->turma->nome_turma }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 11px; color: var(--text-secondary);">Disciplina</span>
                    <span style="font-size: 13px; font-weight: 600;">{{ $horario->disciplina }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">�</div>
        <p>Ainda não há horários atribuídos.</p>
        <a href="#" class="btn" style="margin-top: 12px;">
            <i class="fas fa-plus"></i>
            Adicionar Horário
        </a>
    </div>
    @endif
</div>
@endif

@if(auth()->user()->isEncarregado())
<!-- Dashboard do Encarregado = Centro de Alertas -->
@if(isset($filhos) && $filhos->count() > 0)
<div class="metrics-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
    <div class="card">
        <div class="card-header">Filhos a acompanhar</div>
        <div class="card-value" style="color: var(--accent-green);">{{ $filhos->count() }}</div>
        <div class="card-icon" style="color: var(--accent-green);"><i class="fas fa-child"></i></div>
    </div>
    <!--<div class="card">
        <div class="card-header">Avisos não lidos</div>
        <div class="card-value" style="color: {{ $avisosNaoLidos > 0 ? '#FCD34D' : 'var(--text-secondary)' }};">{{ $avisosNaoLidos }}</div>
        <div class="card-icon" style="color: #FCD34D;"><i class="fas fa-bell"></i></div>
    </div>-->
    <!--<div class="card">
        <div class="card-header">Faltas este ano</div>
        <div class="card-value" style="color: {{ $totalFaltasAno > 0 ? '#FB923C' : 'var(--text-primary)' }};">{{ $totalFaltasAno }}</div>
        <div class="card-icon" style="color: #FB923C;"><i class="fas fa-user-times"></i></div>
    </div>-->
    <div class="card">
        <div class="card-header">Em dívida ({{ date('Y') }})</div>
        <div class="card-value" style="color: {{ $dividaTotal > 0 ? '#FCA5A5' : 'var(--text-primary)' }};">{{ number_format($dividaTotal, 0, ',', ' ') }} Xof</div>
        <div class="card-icon" style="color: #FCA5A5;"><i class="fas fa-money-bill-wave"></i></div>
    </div>
</div>

<div class="grid-2col" style="margin-bottom: 24px;">
    <!-- Alertas de Faltas -->
    <!--<div class="card">
        <div class="card-section-header">
            <div class="card-section-title">
                <i class="fas fa-user-times" style="color: #FB923C;"></i>
                <span>Alertas de Faltas</span>
            </div>
        </div>
        @if($alertasFaltas->count() > 0)
        <div style="display:flex;flex-direction:column;gap:4px;">
            @foreach($alertasFaltas as $alerta)
            <div class="list-row">
                <div>
                    <div style="font-size:13px;font-weight:600;">{{ $alerta->filho->name }}</div>
                    <div style="font-size:11px;color:var(--text-secondary);">{{ $alerta->mes }} · {{ $alerta->faltas }} falta(s) · {{ $alerta->justificadas }} justificada(s)</div>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <span class="tag tag-aluno">{{ $alerta->faltas }} faltas</span>
                    <a href="{{ route('encarregado.filhos.presencas', $alerta->filho) }}" title="Ver presenças" style="color:var(--text-secondary);font-size:13px"><i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state"><i class="fas fa-check-circle" style="color:var(--accent-green);margin-bottom:8px;display:block"></i>Sem alertas — tudo em ordem nas presenças.</div>
        @endif
    </div>-->

    <!-- Alertas de Pagamentos -->
    <div class="card">
        <div class="card-section-header">
            <div class="card-section-title">
                <i class="fas fa-money-bill-wave" style="color: #FCA5A5;"></i>
                <span>Alertas de Pagamentos</span>
            </div>
        </div>
        @if($alertasPagamentos->count() > 0)
        <div style="display:flex;flex-direction:column;gap:4px;">
            @foreach($alertasPagamentos as $alerta)
            <div class="list-row">
                <div>
                    <div style="font-size:13px;font-weight:600;">{{ $alerta->filho->name }}</div>
                    <div style="font-size:11px;color:var(--text-secondary);">{{ $alerta->mes }} · {{ number_format($alerta->valor, 2, ',', ' ') }} {{ Configuracao::obter('escola.moeda', 'Xof') }}</div>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <span class="tag {{ $alerta->status === 'atrasado' ? 'tag-aluno' : 'tag-blue' }}">{{ $alerta->status === 'atrasado' ? 'Atrasado' : 'Pendente' }}</span>
                    <a href="{{ route('encarregado.filhos.pagamentos', $alerta->filho) }}" title="Ver pagamentos" style="color:var(--text-secondary);font-size:13px"><i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state"><i class="fas fa-check-circle" style="color:var(--accent-green);margin-bottom:8px;display:block"></i>Sem dívidas registadas.</div>
        @endif
    </div>
</div>
@else
<div class="empty-state" style="border:1px solid var(--border-color);border-radius:10px;margin-bottom:24px;background:var(--bg-card)">
    <i class="fas fa-child"></i>
    <p>Nenhum aluno está associado à sua conta. Contacte a escola para associar os seus filhos.</p>
</div>
@endif
@endif

@endsection
