@extends('layouts.app')

@section('title', 'Detalhes da Turma')
@section('page-title', 'Turma')

@section('content')
<style>
    .profile-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:hidden;max-width:720px}
    .profile-header{padding:28px;border-bottom:1px solid var(--border-color)}
    .profile-name{font-size:20px;font-weight:700;margin-bottom:4px}
    .profile-meta{display:flex;gap:12px;align-items:center;flex-wrap:wrap}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600}
    .tag-periodo{background:rgba(59,130,246,.12);color:#60A5FA}
    .tag-nivel{background:rgba(139,92,246,.12);color:#A78BFA}
    .tag-ano{background:rgba(234,179,8,.12);color:var(--accent-yellow)}
    .profile-section{padding:20px 28px}
    .profile-section+.profile-section{border-top:1px solid var(--border-color)}
    .section-title{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:var(--text-secondary);margin-bottom:14px}
    .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .info-item{display:flex;flex-direction:column;gap:2px}
    .info-label{font-size:11px;color:var(--text-secondary);font-weight:500}
    .info-value{font-size:13px;color:var(--text-primary)}
    .member-list{list-style:none;padding:0;margin:0}
    .member-item{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.03)}
    .member-item:last-child{border-bottom:none}
    .member-info{display:flex;align-items:center;gap:10px}
    .member-avatar{width:28px;height:28px;border-radius:50%;background:var(--accent-yellow);color:#000;display:grid;place-items:center;font-weight:700;font-size:11px}
    .member-name{font-size:13px;font-weight:500}
    .member-sub{font-size:11px;color:var(--text-secondary)}
    .profile-actions{padding:20px 28px;border-top:1px solid var(--border-color);display:flex;gap:12px}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 24px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .btn-danger{background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.2);color:#FCA5A5}
    .btn-danger:hover{background:rgba(239,68,68,.15);border-color:rgba(239,68,68,.3)}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .empty-state{text-align:center;padding:20px;color:var(--text-secondary);font-size:12px}
    @media(max-width:768px){.info-grid{grid-template-columns:1fr}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif

<div class="profile-card">
    <div class="profile-header">
        <div class="profile-name">{{ $turma->nome_turma }}</div>
        <div class="profile-meta">
            <span class="tag tag-nivel">{{ $turma->nivel }}</span>
            <span class="tag tag-periodo">{{ $turma->periodo }}</span>
            <span class="tag tag-ano">{{ $turma->ano_lectivo }}</span>
        </div>
    </div>

    <div class="profile-section">
        <div class="section-title">Dados da Turma</div>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Nome</span><span class="info-value">{{ $turma->nome_turma }}</span></div>
            <div class="info-item"><span class="info-label">Nível</span><span class="info-value">{{ $turma->nivel }}</span></div>
            <div class="info-item"><span class="info-label">Período</span><span class="info-value">{{ $turma->periodo }}</span></div>
            <div class="info-item"><span class="info-label">Ano Lectivo</span><span class="info-value">{{ $turma->ano_lectivo }}</span></div>
            <div class="info-item"><span class="info-label">Capacidade</span><span class="info-value">{{ $turma->alunos->count() }} / {{ $turma->capacidade }}</span></div>
            <div class="info-item"><span class="info-label">Propina Mensal</span><span class="info-value">{{ number_format($turma->propina_mensal, 2, ',', ' ') }} Xof</span></div>
            <div class="info-item"><span class="info-label">Meses de pagamento</span><span class="info-value">{{ $turma->meses_pagamento }} por ano ({{ number_format($turma->propina_anual, 2, ',', ' ') }} Xof/ano)</span></div>
            <div class="info-item"><span class="info-label">Professor Responsável</span><span class="info-value">{{ $turma->professorResponsavel ? $turma->professorResponsavel->name : '—' }}</span></div>
        </div>
    </div>

    <div class="profile-section">
        <div class="section-title">Alunos ({{ $turma->alunos->count() }})</div>
        @if($turma->alunos->count() > 0)
        <ul class="member-list">
            @foreach($turma->alunos as $aluno)
            <li class="member-item">
                <div class="member-info">
                    <div class="member-avatar">{{ strtoupper(substr($aluno->name, 0, 1)) }}</div>
                    <div>
                        <div class="member-name">{{ $aluno->name }}</div>
                        <div class="member-sub">{{ $aluno->numero ?? $aluno->username }}</div>
                    </div>
                </div>
                <span style="font-size:11px;color:var(--text-secondary)">{{ $aluno->created_at->format('d/m/Y') }}</span>
            </li>
            @endforeach
        </ul>
        @else
        <div class="empty-state">Nenhum aluno nesta turma</div>
        @endif
    </div>

    <div class="profile-actions">
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.turmas.edit', $turma) }}" class="btn-primary"><i class="fas fa-pen"></i> Editar</a>
        @endif
        <a href="{{ route('admin.turmas.index') }}" class="btn"><i class="fas fa-arrow-left"></i> Voltar</a>
        @if(auth()->user()->isAdmin())
        <form method="POST" action="{{ route('admin.turmas.destroy', $turma) }}" style="margin-left:auto" onsubmit="return confirm('Eliminar esta turma?');">@csrf @method('DELETE')
            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Eliminar</button>
        </form>
        @endif
    </div>
</div>
@endsection
