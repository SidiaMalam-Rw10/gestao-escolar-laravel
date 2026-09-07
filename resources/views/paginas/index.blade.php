@extends('layouts.app')

@section('title', 'Páginas da Escola')
@section('page-title', 'Gestão de Páginas')

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
    .page-title{font-size:20px;font-weight:700}
    .stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px}
    .stat-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:8px;padding:16px}
    .stat-label{font-size:10px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.8px;font-weight:600;margin-bottom:8px}
    .stat-value{font-size:24px;font-weight:700}
    .filter-bar{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap}
    .filter-select{padding:10px 14px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;cursor:pointer;min-width:200px}
    .filter-select:focus{outline:none;border-color:var(--accent-green)}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .table-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:hidden}
    .table-header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--border-color)}
    .table-title{font-size:14px;font-weight:600}
    .table-count{font-size:12px;color:var(--text-secondary)}
    table{width:100%;border-collapse:collapse}
    thead th{text-align:left;padding:12px 20px;font-size:10px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.8px;font-weight:600;border-bottom:1px solid var(--border-color);background:rgba(0,0,0,.2)}
    tbody tr{transition:background .15s}
    tbody tr:hover{background:var(--bg-hover)}
    tbody td{padding:12px 20px;font-size:13px;border-bottom:1px solid rgba(255,255,255,.03)}
    .user-cell{display:flex;align-items:center;gap:12px}
    .thumb{width:44px;height:34px;border-radius:6px;object-fit:cover;border:1px solid var(--border-color);flex-shrink:0}
    .thumb-icon{width:44px;height:34px;border-radius:6px;border:1px solid var(--border-color);display:grid;place-items:center;font-size:12px;color:var(--text-secondary);flex-shrink:0}
    .user-name{font-weight:600;color:var(--text-primary)}
    .user-username{font-size:11px;color:var(--text-secondary)}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap}
    .tag-horario{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .tag-atividades{background:rgba(234,179,8,.12);color:var(--accent-yellow)}
    .tag-sobre{background:rgba(59,130,246,.12);color:#60A5FA}
    .actions-cell{display:flex;gap:6px}
    .btn-icon{width:30px;height:30px;border-radius:6px;border:1px solid var(--border-color);background:var(--bg-card);color:var(--text-secondary);display:grid;place-items:center;cursor:pointer;transition:all .15s;text-decoration:none;font-size:12px}
    .btn-icon:hover{border-color:rgba(255,255,255,.15);color:var(--text-primary);background:var(--bg-hover)}
    .btn-icon.danger:hover{border-color:rgba(239,68,68,.3);color:#FCA5A5;background:rgba(239,68,68,.08)}
    .empty-state{text-align:center;padding:40px 20px;color:var(--text-secondary);font-size:12px}
    .empty-state i{font-size:32px;margin-bottom:12px;display:block;opacity:.3}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .pagination{display:flex;justify-content:center;gap:6px;margin-top:20px}
    .pagination a,.pagination span{padding:8px 12px;border-radius:6px;font-size:13px;text-decoration:none;transition:all .15s}
    .pagination a{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-secondary)}
    .pagination a:hover{border-color:rgba(255,255,255,.15);color:var(--text-primary)}
    .pagination .active{background:var(--accent-green);color:#000;font-weight:600}
    @media(max-width:768px){.stats-grid{grid-template-columns:1fr}.filter-bar{flex-direction:column}table{font-size:12px}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif

<div class="stats-grid">
    @foreach(App\Models\Pagina::TIPOS as $tipo => $label)
    <div class="stat-card"><div class="stat-label">{{ $label }}</div><div class="stat-value">{{ $contagens[$tipo] ?? 0 }}</div></div>
    @endforeach
</div>

<div class="page-header">
    <div class="page-title">Páginas da Escola</div>
    <a href="{{ route('admin.paginas.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Nova Página</a>
</div>

<form method="GET" action="{{ route('admin.paginas.index') }}">
    <div class="filter-bar">
        <select name="tipo" class="filter-select">
            <option value="">Todas as secções</option>
            @foreach(App\Models\Pagina::TIPOS as $tipo => $label)
            <option value="{{ $tipo }}" {{ request('tipo') === $tipo ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Filtrar</button>
        @if(request()->filled('tipo'))
        <a href="{{ route('admin.paginas.index') }}" class="btn"><i class="fas fa-times"></i> Limpar</a>
        @endif
    </div>
</form>

<div class="table-card">
    <div class="table-header">
        <div class="table-title">Lista de Páginas</div>
        <div class="table-count">{{ $paginas->total() }} registro(s)</div>
    </div>
    @if($paginas->count() > 0)
    <table>
        <thead><tr><th>Título</th><th>Secção</th><th>Ordem</th><th>Criado por</th><th>Atualizado</th><th style="text-align:right">Ações</th></tr></thead>
        <tbody>
            @foreach($paginas as $pagina)
            <tr>
                <td>
                    <div class="user-cell">
                        @if($pagina->imagem)
                        <img src="{{ asset('storage/' . $pagina->imagem) }}" alt="" class="thumb">
                        @elseif($pagina->hasVideo())
                        <div class="thumb-icon"><i class="fas fa-play"></i></div>
                        @else
                        <div class="thumb-icon"><i class="fas {{ $pagina->tipo === 'horario' ? 'fa-clock' : ($pagina->tipo === 'atividades' ? 'fa-calendar-check' : 'fa-school') }}"></i></div>
                        @endif
                        <div>
                            <div class="user-name">{{ $pagina->titulo }}</div>
                            <div class="user-username">{{ \Illuminate\Support\Str::limit(strip_tags($pagina->conteudo), 60) }}</div>
                        </div>
                    </div>
                </td>
                <td><span class="tag tag-{{ $pagina->tipo }}">{{ $pagina->tipoLabel() }}</span></td>
                <td style="font-size:12px">{{ $pagina->ordem }}</td>
                <td style="font-size:12px">{{ $pagina->autor?->name ?? '—' }}</td>
                <td style="font-size:12px;color:var(--text-secondary)">{{ $pagina->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <div class="actions-cell" style="justify-content:flex-end">
                        <a href="{{ route('admin.paginas.edit', $pagina) }}" class="btn-icon" title="Editar"><i class="fas fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.paginas.destroy', $pagina) }}" style="display:inline" onsubmit="return confirm('Eliminar esta página?');">@csrf @method('DELETE')
                            <button type="submit" class="btn-icon danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state"><i class="fas fa-file-alt"></i><div>Nenhuma página criada ainda</div></div>
    @endif
</div>

@if($paginas->hasPages())<div class="pagination">{{ $paginas->links() }}</div>@endif
@endsection