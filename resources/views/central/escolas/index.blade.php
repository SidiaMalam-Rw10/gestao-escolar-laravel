@extends('layouts.app')

@section('title', 'Escolas')
@section('page-title', 'Painel ' . \App\Models\Configuracao::plataformaNome() . ' — Escolas')

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
    .page-title{font-size:20px;font-weight:700}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn-danger{background:transparent;border:1px solid rgba(239,68,68,.3);color:#FCA5A5;padding:8px 14px;border-radius:6px;cursor:pointer;font-size:12px;display:inline-flex;align-items:center;gap:6px;transition:all .15s;text-decoration:none}
    .btn-danger:hover{background:rgba(239,68,68,.12)}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:8px 14px;border-radius:6px;cursor:pointer;font-size:12px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:visible;margin-bottom:16px}
    .table{width:100%;border-collapse:collapse}
    .table th{font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--text-secondary);text-align:left;padding:12px 16px;border-bottom:1px solid var(--border-color)}
    .table td{padding:12px 16px;border-bottom:1px solid var(--border-color);font-size:13px;vertical-align:middle}
    .table tr:last-child td{border-bottom:none}
    .table tr:hover td{background:var(--bg-hover)}
    .eso-nome{font-weight:600}
    .eso-sub{font-size:11px;color:var(--text-secondary);margin-top:2px}
    .mono{font-family:monospace;font-size:12px;color:#60A5FA}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap}
    .tag-ativa{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .tag-inativa{background:rgba(107,114,128,.15);color:var(--text-secondary)}
    .btn-icon{width:30px;height:30px;border-radius:6px;border:1px solid var(--border-color);background:var(--bg-card);color:var(--text-secondary);display:grid;place-items:center;cursor:pointer;transition:all .15s;text-decoration:none;font-size:12px}
    .btn-icon:hover{border-color:rgba(255,255,255,.15);color:var(--text-primary);background:var(--bg-hover)}
    .btn-icon.danger:hover{border-color:rgba(239,68,68,.3);color:#FCA5A5;background:rgba(239,68,68,.08)}
    .btn-icon.warn:hover{border-color:rgba(251,191,36,.3);color:#FCD34D;background:rgba(251,191,36,.08)}
    .btn-icon.success:hover{border-color:rgba(34,197,94,.3);color:var(--accent-green);background:rgba(34,197,94,.08)}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .actions{display:flex;gap:6px;align-items:center}
    @media(max-width:768px){.table{display:block;overflow-x:auto}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif

<div class="page-header">
    <div class="page-title">Escolas a servir a aplicação</div>
    <a href="{{ route('central.escolas.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Nova Escola</a>
</div>

<div class="card">
    @if($escolas->isEmpty())
        <div style="text-align:center;padding:40px 20px;color:var(--text-secondary);font-size:12px">
            <i class="fas fa-school" style="font-size:32px;margin-bottom:12px;display:block;opacity:.3"></i>
            Ainda não há escolas registadas. Crie a primeira escola para começar.
        </div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Escola</th>
                    <th>Base de dados</th>
                    <th>Administrador</th>
                    <th>Estado</th>
                    <th style="text-align:right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($escolas as $escola)
                    @php
                        $host = parse_url(config('app.url'), PHP_URL_HOST);
                        $porta = parse_url(config('app.url'), PHP_URL_PORT);
                        $urlEscola = 'http://' . $escola->slug . '.' . $host . ($porta ? ':' . $porta : '');
                    @endphp
                    <tr>
                        <td>
                            <div class="eso-nome">{{ $escola->nome }}</div>
                            <div class="eso-sub">{{ $urlEscola }}</div>
                        </td>
                        <td><span class="mono">{{ $escola->nome_bd }}</span></td>
                        <td>
                            <div>{{ $escola->admin_nome }}</div>
                            <div class="eso-sub">{{ $escola->admin_username }}</div>
                        </td>
                        <td><span class="tag {{ $escola->ativa ? 'tag-ativa' : 'tag-inativa' }}">{{ $escola->ativa ? 'Ativa' : 'Inativa' }}</span></td>
                        <td style="text-align:right">
                            <div class="actions" style="justify-content:flex-end">
                                <a href="{{ $urlEscola }}" target="_blank" class="btn-icon" title="Abrir a aplicação desta escola"><i class="fas fa-external-link-alt"></i></a>
                                <a href="{{ route('central.escolas.edit', $escola) }}" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('central.escolas.toggle', $escola) }}">
                                    @csrf
                                    <button type="submit" class="btn-icon {{ $escola->ativa ? 'warn' : 'success' }}" title="{{ $escola->ativa ? 'Desativar' : 'Reativar' }}">
                                        <i class="fas {{ $escola->ativa ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('central.escolas.destroy', $escola) }}" onsubmit="return confirm('Eliminar a escola «{{ $escola->nome }}» e a sua base de dados «{{ $escola->nome_bd }}»? Esta ação é irreversível.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
