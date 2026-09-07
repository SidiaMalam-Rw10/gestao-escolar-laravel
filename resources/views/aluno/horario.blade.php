@extends('layouts.app')

@section('title', 'Meu Horário')
@section('page-title', 'Meu Horário')

@section('content')
<style>
    .doc-toolbar{display:flex;justify-content:flex-end;margin-bottom:16px}
    .btn-print{background:var(--accent-green);color:#000;border:none;padding:10px 16px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn-print:hover{background:#1ea34e}
    .hz-doc{background:#fff;color:#000;width:100%;max-width:850px;margin:0 auto;padding:30px;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,.05);border:1px solid var(--border-color)}
    .hz-doc *{color:#000}
    .header-top{text-align:center;margin-bottom:25px}
    .header-top h1{font-size:18px;font-weight:bold;margin-bottom:4px}
    .header-top h2{font-size:14px;font-weight:normal;margin-bottom:4px}
    .header-top h3{font-size:14px;font-weight:bold}
    .info-section{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;gap:12px;flex-wrap:wrap}
    .info-column{font-size:14px;line-height:1.8}
    .info-column strong{font-weight:bold}
    .logo-container{text-align:center}
    .logo-container img{max-width:120px;height:auto;border-radius:4px}
    .schedule-card{border:1px solid #dcdcdc;border-radius:12px;padding:25px}
    .schedule-title{text-align:center;font-size:20px;font-weight:normal;letter-spacing:.5px;margin-bottom:25px}
    .schedule-card table{width:100%;border-collapse:collapse;margin-bottom:25px}
    .schedule-card th,.schedule-card td{border:2px solid #000;padding:8px;text-align:center;font-size:13px}
    .schedule-card th{color:#999;font-weight:bold}
    .cell-main{font-weight:bold;font-size:13px}
    .cell-sub{font-size:10px;color:#444;margin-top:2px}
    .summary-text{font-size:14px;margin-bottom:50px}
    .signatures{display:flex;justify-content:space-around;margin-bottom:20px}
    .signature-box{text-align:center;width:250px}
    .signature-box p{font-size:14px;margin-bottom:40px}
    .signature-line{border-bottom:1.5px solid #000;width:100%}
    .footer-obs{margin-top:25px;font-size:13px;line-height:1.4}
    .empty-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;text-align:center;padding:48px 20px;color:var(--text-secondary);font-size:12px;box-shadow:0 0 10px rgba(0,0,0,.05)}
    .empty-card i{font-size:28px;margin-bottom:12px;display:block;opacity:.4}
    @media(max-width:600px){
        .info-section{flex-direction:column;text-align:center}
        .hz-doc{padding:16px;overflow-x:auto}
        .schedule-card table{min-width:560px}
    }
    @media print{
        aside,header,.doc-toolbar{display:none !important}
        .content{padding:0 !important}
        body{background:#fff}
        .hz-doc{box-shadow:none;border-radius:0;border:none;max-width:100%;padding:10px}
    }
</style>

@php
    $aluno = auth()->user();
    $temTurma = $aluno->turma ? true : false;
@endphp

<div class="doc-toolbar">
    <button onclick="window.print()" class="btn-print"><i class="fas fa-print"></i> Imprimir</button>
</div>

@if($horarios->isNotEmpty() && $temTurma)
<div class="hz-doc">
    <div class="header-top">
        <h1>{{ config('app.name', 'Sistema de Gestão Escolar') }}</h1>
        <h2>Sector Autónomo de Bissau - Região de Bissau</h2>
        <h3>Conselho Técnico Pedagógico</h3>
    </div>

    <div class="info-section">
        <div class="info-column">
            <p><strong>Aluno:</strong> {{ $aluno->name }}</p>
            <p><strong>Nº:</strong> {{ $aluno->numero ?? '—' }}</p>
        </div>
        <div class="logo-container">
            <img src="{{ asset('logo.png') }}" alt="Logo">
        </div>
        <div class="info-column" style="text-align:right">
            <p><strong>Turma:</strong> {{ $aluno->turma->nome_turma }}</p>
            <p><strong>Turno:</strong> {{ $aluno->turma->periodo }}</p>
            <p><strong>Ano Letivo:</strong> {{ $aluno->turma->ano_lectivo }}</p>
        </div>
    </div>

    <div class="schedule-card">
        <h2 class="schedule-title">HORÁRIO SEMANAL DA TURMA {{ $aluno->turma->nome_turma }}</h2>

        @include('horarios._grid', ['horarios' => $horarios, 'modo' => 'aluno'])

        <div class="summary-text">
            <strong>Disciplinas:</strong> {{ $horarios->pluck('disciplina')->unique()->count() }} | <strong>Horas:</strong> {{ $horarios->count() > 0 ? round($horarios->sum(fn ($a) => $a->hora_fim->diffInMinutes($a->hora_inicio)) / 60, 1) : 0 }}h/semana
        </div>

        <div class="signatures">
            <div class="signature-box">
                <p>O P.C.T.P.</p>
                <div class="signature-line"></div>
            </div>
            <div class="signature-box">
                <p>O Diretor</p>
                <div class="signature-line"></div>
            </div>
        </div>
    </div>

    <div class="footer-obs">
        <strong>Observação:</strong> Qualquer alteração de horário ou troca de turmas deverá ser previamente comunicada e autorizada pelo Conselho Técnico-Pedagógico.
    </div>
</div>
@elseif(!$temTurma)
<div class="empty-card">
    <i class="fas fa-exclamation-circle"></i>
    <div>Ainda não foi associado a nenhuma turma.</div>
</div>
@else
<div class="empty-card">
    <i class="fas fa-calendar-times"></i>
    <div>Não há horários atribuídos para a sua turma.</div>
</div>
@endif
@endsection
