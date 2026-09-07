@extends('layouts.app')

@section('title', 'Notas do Filho')
@section('page-title', 'Notas do Filho')

@section('content')
<style>
    .filho-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:20px}
    .filho-stat{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:18px}
    .filho-stat-header{font-size:10px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.8px;font-weight:600;margin-bottom:8px}
    .filho-stat-value{font-size:26px;font-weight:700;line-height:1}
    .table-wrap{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow-x:auto}
    .trimestre-block{padding:20px 24px}
    .trimestre-block+.trimestre-block{border-top:1px solid var(--border-color)}
    .trimestre-title{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:var(--text-secondary);margin-bottom:14px;display:flex;align-items:center;gap:8px}
    .notas-table{width:100%;border-collapse:collapse;font-size:13px;min-width:560px}
    .notas-table th{text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-secondary);font-weight:600;padding:8px 10px;border-bottom:1px solid var(--border-color)}
    .notas-table td{padding:10px;border-bottom:1px solid rgba(255,255,255,.03)}
    .notas-table tr:last-child td{border-bottom:none}
    .nota-valor{display:inline-block;min-width:44px;text-align:center;padding:3px 8px;border-radius:6px;font-weight:600;font-size:12px}
    .nota-boa{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .nota-media{background:rgba(234,179,8,.12);color:var(--accent-yellow)}
    .nota-baixa{background:rgba(239,68,68,.12);color:#FCA5A5}
    .tag{font-size:10px}
    .empty-state{text-align:center;padding:48px 20px;color:var(--text-secondary);font-size:12px}
</style>

@include('encarregados._filho_header', ['aluno' => $aluno, 'filhos' => $filhos, 'secao' => 'notas'])

<div class="filho-stats">
    <div class="filho-stat">
        <div class="filho-stat-header">Média Geral ({{ date('Y') }})</div>
        <div class="filho-stat-value" style="color:{{ $notas->avg('mg') >= 10 ? 'var(--accent-green)' : '#FB923C' }}">
            {{ $notas->count() > 0 ? rtrim(rtrim(number_format($notas->avg('mg'), 2, ',', ' '), '0'), ',') : '—' }}
        </div>
    </div>
    <div class="filho-stat">
        <div class="filho-stat-header">Disciplinas</div>
        <div class="filho-stat-value">{{ $notas->count() }}</div>
    </div>
    <div class="filho-stat">
        <div class="filho-stat-header">Aprovadas (mg ≥ 10)</div>
        <div class="filho-stat-value" style="color:var(--accent-green)">{{ $notas->where('mg', '>=', 10)->count() }}</div>
    </div>
    <div class="filho-stat">
        <div class="filho-stat-header">Por melhorar (mg < 10)</div>
        <div class="filho-stat-value" style="color:#FB923C">{{ $notas->where('mg', '<', 10)->count() }}</div>
    </div>
</div>

<div class="table-wrap">
    @if($notas->count() > 0)
    @foreach($trimestres as $trimestre => $notasTrimestre)
    <div class="trimestre-block">
        <div class="trimestre-title">
            <i class="fas fa-chart-line" style="color:var(--accent-green)"></i>
            {{ $trimestre }}º Trimestre
            <span class="tag" style="padding:3px 8px;border-radius:10px;background:rgba(59,130,246,.12);color:#60A5FA">
                Média: {{ rtrim(rtrim(number_format($notasTrimestre->avg('mg'), 2, ',', ' '), '0'), ',') }}
            </span>
        </div>
        <table class="notas-table">
            <thead>
                <tr>
                    <th>Disciplina</th>
                    <th style="text-align:center">TPI</th>
                    <th style="text-align:center">CO</th>
                    <th style="text-align:center">TG</th>
                    <th style="text-align:center">Média</th>
                    <th style="text-align:center">Exame</th>
                    <th style="text-align:center">MG</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notasTrimestre as $nota)
                <tr>
                    <td style="font-weight:500">{{ $nota->disciplina }}</td>
                    <td style="text-align:center"><span class="nota-valor {{ $nota->tpi >= 10 ? 'nota-boa' : ($nota->tpi >= 7 ? 'nota-media' : 'nota-baixa') }}">{{ $nota->tpi !== null ? number_format($nota->tpi, 1) : '—' }}</span></td>
                    <td style="text-align:center"><span class="nota-valor {{ $nota->co >= 10 ? 'nota-boa' : ($nota->co >= 7 ? 'nota-media' : 'nota-baixa') }}">{{ $nota->co !== null ? number_format($nota->co, 1) : '—' }}</span></td>
                    <td style="text-align:center"><span class="nota-valor {{ $nota->tg >= 10 ? 'nota-boa' : ($nota->tg >= 7 ? 'nota-media' : 'nota-baixa') }}">{{ $nota->tg !== null ? number_format($nota->tg, 1) : '—' }}</span></td>
                    <td style="text-align:center"><span class="nota-valor {{ $nota->media >= 10 ? 'nota-boa' : ($nota->media >= 7 ? 'nota-media' : 'nota-baixa') }}">{{ $nota->media !== null ? number_format($nota->media, 1) : '—' }}</span></td>
                    <td style="text-align:center"><span class="nota-valor {{ $nota->exame >= 10 ? 'nota-boa' : ($nota->exame >= 7 ? 'nota-media' : 'nota-baixa') }}">{{ $nota->exame !== null ? number_format($nota->exame, 1) : '—' }}</span></td>
                    <td style="text-align:center"><span class="nota-valor" style="color:{{ $nota->mg >= 10 ? 'var(--accent-green)' : '#FB923C' }};font-weight:700">{{ $nota->mg !== null ? number_format($nota->mg, 1) : '—' }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
    @else
    <div class="empty-state">
        <i class="fas fa-graduation-cap" style="font-size:28px;margin-bottom:12px;display:block;opacity:.4"></i>
        Ainda não há notas registadas para o ano letivo {{ date('Y') }}.
    </div>
    @endif
</div>
@endsection