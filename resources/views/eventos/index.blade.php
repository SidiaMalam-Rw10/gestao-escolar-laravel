@extends('layouts.app')

@section('title', 'Calendário')
@section('page-title', 'Calendário de Atividades')

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
    .page-title{font-size:20px;font-weight:700}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:hidden;margin-bottom:16px}
    .card-header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--border-color)}
    .card-title{font-size:14px;font-weight:600}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .legenda{display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;padding:12px 16px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:8px;font-size:12px}
    .legenda-item{display:flex;align-items:center;gap:6px;color:var(--text-secondary)}
    .legenda-dot{width:10px;height:10px;border-radius:50%;display:inline-block}
    .cal-toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px}
    .cal-titulo{font-size:16px;font-weight:700}
    .cal-nav{display:flex;align-items:center;gap:10px}
    .cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:6px;margin-bottom:24px}
    .cal-dow{padding:8px 6px;text-align:center;font-size:11px;font-weight:600;color:var(--text-secondary);background:var(--bg-hover);border-radius:6px}
    .cal-dia{min-height:74px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;padding:6px;display:flex;flex-direction:column;gap:3px;overflow:hidden;transition:background .15s,border-color .15s}
    .cal-dia[style*="--ev-cor"]{border-color:var(--ev-cor)55}
    .cal-dia[style*="--ev-cor"]:hover{background:var(--ev-cor)44;border-color:var(--ev-cor);cursor:pointer}
    .cal-dia:not([style]):hover{border-color:rgba(255,255,255,.25)}
    .cal-dia.hoje{border-color:var(--accent-green);box-shadow:0 0 0 1px var(--accent-green)}
    .cal-num{font-size:11px;font-weight:600;color:var(--text-secondary);margin-left:auto}
    .cal-dia.hoje .cal-num{color:var(--accent-green)}
    .cal-chip{height:8px;border-radius:4px;display:block;flex:1;min-width:12px;cursor:pointer;transition:all .12s}
    .cal-chip:hover{height:14px;transform:scaleX(1.05);filter:brightness(1.25)}
    .cal-eventos-lista{display:flex;gap:3px}
    .ev-tag{display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap}
    .ev-meta{display:flex;gap:14px;font-size:11px;color:var(--text-secondary);flex-wrap:wrap}
    .ev-meta i{margin-right:4px}
    .btn-icon{width:30px;height:30px;border-radius:6px;border:1px solid var(--border-color);background:var(--bg-card);color:var(--text-secondary);display:grid;place-items:center;cursor:pointer;transition:all .15s;text-decoration:none;font-size:12px}
    .btn-icon:hover{border-color:rgba(255,255,255,.15);color:var(--text-primary)}
    .btn-icon.danger:hover{border-color:rgba(239,68,68,.3);color:#FCA5A5;background:rgba(239,68,68,.08)}
    .empty-state{text-align:center;padding:40px 20px;color:var(--text-secondary);font-size:12px}
    .empty-state i{font-size:32px;margin-bottom:12px;display:block;opacity:.3}
    @media(max-width:768px){.cal-grid{font-size:10px}.cal-dia{min-height:44px}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif

<div class="page-header">
    <div class="page-title">Calendário de Atividades</div>
    @if($podeGerir)
    <a href="{{ route('admin.eventos.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Marcar Evento</a>
    @endif
</div>

<div class="legenda">
    @foreach(\App\Models\Evento::TIPOS as $tipo => $legenda)
    <span class="legenda-item"><span class="legenda-dot" style="background:{{ \App\Models\Evento::CORES[$tipo] }}"></span>{{ $legenda }}</span>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <div class="cal-toolbar" style="width:100%;justify-content:space-between;margin:0">
            <div class="cal-titulo">{{ $meses[$mes] ?? $mes }} {{ $ano }}</div>
            <div class="cal-nav">
                <a href="{{ route('calendario.index', ['mes' => $mes === 1 ? 12 : $mes - 1, 'ano' => $mes === 1 ? $ano - 1 : $ano]) }}" class="btn"><i class="fas fa-chevron-left"></i></a>
                <a href="{{ route('calendario.index', ['mes' => now()->month, 'ano' => now()->year]) }}" class="btn">Hoje</a>
                <a href="{{ route('calendario.index', ['mes' => $mes === 12 ? 1 : $mes + 1, 'ano' => $mes === 12 ? $ano + 1 : $ano]) }}" class="btn"><i class="fas fa-chevron-right"></i></a>
            </div>
        </div>
    </div>
    <div style="padding:16px">
        <div class="cal-grid">
            @foreach($semanas as $nome)
            <div class="cal-dow">{{ $nome }}</div>
            @endforeach
            @for($i = 0; $i < $colunasAntes; $i++)
            <div></div>
            @endfor
            @for($dia = 1; $dia <= $diasNoMes; $dia++)
            @php
                $hoje = $referencia->copy()->day($dia)->isToday();
                $evDia = $porDia[$dia] ?? [];
                $corDia = isset($evDia[0]) ? $evDia[0]->corEfetiva() : null;
            @endphp
            <div class="cal-dia {{ $hoje ? 'hoje' : '' }}" @if($corDia) style="--ev-cor:{{ $corDia }};background:{{ $corDia }}26" @endif>
                <span class="cal-num">{{ $dia }}</span>
                <div class="cal-eventos-lista">
                    @foreach($evDia as $ev)
                    @php
                        $sufixoHora = $ev->data_inicio->format('H:i');
                        if ($ev->multidiario()) { $sufixoHora .= ' até ' . $ev->data_fim->format('d/m'); }
                        if ($ev->local) { $sufixoHora .= ' · ' . $ev->local; }
                    @endphp
                    <span class="cal-chip" title="{{ $ev->titulo }} · {{ $sufixoHora }}" style="background:{{ $ev->corEfetiva() }}"></span>
                    @endforeach
                </div>
            </div>
            @endfor
        </div>

        @php $eventosMes = collect();
             for($dia=1;$dia<=$diasNoMes;$dia++){ foreach($porDia[$dia] ?? [] as $ev){ $eventosMes->push($ev);} }
        @endphp

        @if($eventosMes->count() > 0)
        <div class="card-title" style="margin-bottom:12px">Eventos de {{ $meses[$mes] ?? $mes }} {{ $ano }}</div>
        @foreach($eventosMes as $ev)
        <div style="padding:12px 0;border-bottom:1px solid var(--border-color);display:flex;justify-content:space-between;gap:12px;align-items:flex-start">
            <div style="flex:1;min-width:0">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                    <span style="font-weight:600;font-size:13px">{{ $ev->titulo }}</span>
                    <span class="ev-tag" style="background:{{ $ev->corEfetiva() }}22;color:{{ $ev->corEfetiva() }}">{{ \App\Models\Evento::tipoLabel($ev->tipo) }}</span>
                </div>
                @if($ev->descricao)<div style="font-size:12px;color:var(--text-secondary);margin-top:6px;line-height:1.5">{{ Str::limit($ev->descricao, 160) }}</div>@endif
                <div class="ev-meta" style="margin-top:8px">
                    <span><i class="far fa-clock"></i>{{ $ev->sistema ? $ev->data_inicio->format('d/m/Y') . ($ev->multidiario() ? ' → ' . $ev->data_fim->format('d/m/Y') : '') : $ev->data_inicio->format('d/m/Y H:i') . ($ev->multidiario() ? ' → ' . $ev->data_fim->format('d/m/Y H:i') : '') }}</span>
                    @if($ev->local)<span><i class="fas fa-map-marker-alt"></i>{{ $ev->local }}</span>@endif
                </div>
            </div>
            @if($podeGerir && !($ev->sistema ?? false))
            <div style="display:flex;gap:8px;flex-shrink:0">
                <a href="{{ route('admin.eventos.edit', $ev) }}" class="btn-icon" title="Editar"><i class="fas fa-pen"></i></a>
                <form method="POST" action="{{ route('admin.eventos.destroy', $ev) }}" onsubmit="return confirm('Eliminar este evento?');">@csrf @method('DELETE')
                    <button type="submit" class="btn-icon danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                </form>
            </div>
            @endif
        </div>
        @endforeach
        @else
        <div class="empty-state" style="padding:24px"><i class="far fa-calendar-check"></i><div>Nenhum evento marcado neste mês.</div></div>
        @endif
    </div>
</div>

@if($proximos->count() > 0)
<div class="card">
    <div class="card-header"><div class="card-title"><i class="far fa-hourglass" style="color:#FCD34D;margin-right:8px"></i>Próximos eventos</div></div>
    <div style="padding:8px 20px">
        @foreach($proximos as $ev)
        <div style="padding:10px 0;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;gap:12px">
            <div style="display:flex;align-items:center;gap:12px;min-width:0">
                <span class="ev-tag" style="background:{{ $ev->corEfetiva() }}22;color:{{ $ev->corEfetiva() }}">{{ \App\Models\Evento::tipoLabel($ev->tipo) }}</span>
                <span style="font-size:13px;font-weight:500;max-width:420px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $ev->titulo }}</span>
            </div>
            <span style="font-size:11px;color:var(--text-secondary);white-space:nowrap"><i class="far fa-clock" style="margin-right:4px"></i>{{ $ev->sistema ? $ev->data_inicio->format('d/m/Y') . ($ev->multidiario() ? ' → ' . $ev->data_fim->format('d/m/Y') : '') : $ev->data_inicio->format('d/m/Y H:i') }}</span>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection