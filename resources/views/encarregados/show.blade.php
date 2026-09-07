@extends('layouts.app')

@section('title', $encarregado->nome)
@section('page-title', 'Encarregado de Educação')

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
    .page-title{font-size:20px;font-weight:700}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .profile-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:24px;display:flex;align-items:center;gap:20px;margin-bottom:24px}
    .profile-avatar{width:64px;height:64px;border-radius:50%;background:var(--accent-blue,#60A5FA);color:#000;display:grid;place-items:center;font-weight:700;font-size:24px;flex-shrink:0}
    .profile-name{font-size:18px;font-weight:700}
    .profile-meta{margin-top:6px;display:flex;gap:14px;flex-wrap:wrap;color:var(--text-secondary);font-size:13px}
    .profile-meta i{margin-right:5px}
    .tag{padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap}
    .tag-parentesco{background:rgba(59,130,246,.12);color:#60A5FA}
    .tag-turma{background:rgba(168,85,247,.15);color:#A78BFA}
    .tag-ativo{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .tag-inativo{background:rgba(239,68,68,.12);color:#FCA5A5}
    .card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:hidden;margin-bottom:24px}
    .card-header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--border-color)}
    .card-title{font-size:14px;font-weight:600}
    .info-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;padding:20px}
    .info-item{display:flex;flex-direction:column;gap:4px}
    .info-label{font-size:10px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.8px;font-weight:600}
    .info-value{font-size:14px;font-weight:500}
    table{width:100%;border-collapse:collapse}
    thead th{text-align:left;padding:12px 20px;font-size:10px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.8px;font-weight:600;border-bottom:1px solid var(--border-color);background:rgba(0,0,0,.2)}
    tbody tr{transition:background .15s}
    tbody tr:hover{background:var(--bg-hover)}
    tbody td{padding:12px 20px;font-size:13px;border-bottom:1px solid rgba(255,255,255,.03)}
    .user-cell{display:flex;align-items:center;gap:12px}
    .user-avatar{width:32px;height:32px;border-radius:50%;background:var(--accent-yellow);color:#000;display:grid;place-items:center;font-weight:700;font-size:12px;flex-shrink:0}
    .user-name{font-weight:600}
    .user-username{font-size:11px;color:var(--text-secondary)}
    .actions-cell{display:flex;gap:6px}
    .btn-icon{width:30px;height:30px;border-radius:6px;border:1px solid var(--border-color);background:var(--bg-card);color:var(--text-secondary);display:grid;place-items:center;cursor:pointer;transition:all .15s;text-decoration:none;font-size:12px}
    .btn-icon:hover{border-color:rgba(255,255,255,.15);color:var(--text-primary);background:var(--bg-hover)}
    .btn-icon.danger:hover{border-color:rgba(239,68,68,.3);color:#FCA5A5;background:rgba(239,68,68,.08)}
    .empty-state{text-align:center;padding:40px 20px;color:var(--text-secondary);font-size:12px}
    .empty-state i{font-size:32px;margin-bottom:12px;display:block;opacity:.3}
    .alert-success{padding:12px 16px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:var(--accent-green);border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .alert-error{padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .associate-bar{display:flex;gap:12px;padding:20px;border-top:1px solid var(--border-color)}
    .associate-bar .searchable-select{flex:1}
    .searchable-select{position:relative}
    .searchable-select .ss-input{width:100%;display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 14px;background:var(--bg-input,#151D19);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;cursor:pointer;text-align:left;transition:border-color .15s}
    .searchable-select .ss-input:focus{outline:none;border-color:var(--accent-green)}
    .searchable-select .ss-input .ss-value{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .searchable-select .ss-input .ss-placeholder{color:var(--text-secondary)}
    .searchable-select .ss-caret{color:var(--text-secondary);font-size:11px;flex-shrink:0;transition:transform .15s}
    .searchable-select .ss-dropdown{display:none;position:absolute;top:calc(100% + 6px);left:0;right:0;z-index:50;background:var(--bg-input,#151D19);border:1px solid var(--border-color);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.35);overflow:hidden}
    .searchable-select .ss-dropdown.open{display:block}
    .searchable-select .ss-search-box{position:relative;padding:10px 12px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:8px}
    .searchable-select .ss-search-box i{font-size:12px;color:var(--text-secondary)}
    .searchable-select .ss-search-box input{flex:1;background:transparent;border:none;outline:none;color:var(--text-primary);font-size:13px}
    .searchable-select .ss-search-box input::placeholder{color:var(--text-secondary)}
    .searchable-select .ss-options{max-height:240px;overflow-y:auto}
    .searchable-select .ss-option{display:flex;align-items:center;gap:10px;padding:10px 12px;cursor:pointer;font-size:13px;color:var(--text-primary);transition:background .12s}
    .searchable-select .ss-option:hover{background:var(--bg-hover)}
    .searchable-select .ss-option.selected{background:rgba(34,197,94,.1);color:var(--accent-green)}
    .searchable-select .ss-option.no-results{color:var(--text-secondary);cursor:default}
    .searchable-select .ss-option.no-results:hover{background:transparent}
    @media(max-width:768px){.info-grid{grid-template-columns:1fr}.profile-card{flex-direction:column;text-align:center}.associate-bar{flex-direction:column}}
</style>

@if(session('success'))<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert-error"><i class="fas fa-exclamation-circle"></i>{{ $errors->first() }}</div>@endif

<div class="page-header">
    <div class="page-title">Detalhes do Encarregado</div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('admin.encarregados.index') }}" class="btn"><i class="fas fa-arrow-left"></i> Voltar</a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.encarregados.edit', $encarregado) }}" class="btn-primary"><i class="fas fa-pen"></i> Editar</a>
        @endif
    </div>
</div>

<div class="profile-card">
    <div class="profile-avatar">{{ strtoupper(substr($encarregado->nome, 0, 1)) }}</div>
    <div>
        <div class="profile-name">
            {{ $encarregado->nome }}
            @if($encarregado->parentesco)
            <span class="tag tag-parentesco" style="margin-left:8px;vertical-align:middle">{{ $encarregado->parentesco }}</span>
            @endif
            @if($encarregado->user)
            <span class="tag tag-ativo" style="margin-left:4px;vertical-align:middle"><i class="fas fa-key" style="margin-right:3px"></i>Tem acesso</span>
            @else
            <span class="tag tag-inativo" style="margin-left:4px;vertical-align:middle">Sem acesso</span>
            @endif
        </div>
        <div class="profile-meta">
            @if($encarregado->telefone)<span><i class="fas fa-phone"></i>{{ $encarregado->telefone }}</span>@endif
            @if($encarregado->email)<span><i class="fas fa-envelope"></i>{{ $encarregado->email }}</span>@endif
            @if($encarregado->genero)<span><i class="fas fa-user"></i>{{ $encarregado->genero === 'M' ? 'Masculino' : 'Feminino' }}</span>@endif
            @if($encarregado->endereco)<span><i class="fas fa-map-marker-alt"></i>{{ $encarregado->endereco }}</span>@endif
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">Acesso ao Sistema</div>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.encarregados.edit', $encarregado) }}" class="btn" style="padding:8px 14px;font-size:12px"><i class="fas fa-user-cog"></i> Gerir Acesso</a>
        @endif
    </div>
    <div class="info-grid" style="grid-template-columns:repeat(2,1fr)">
        @if($encarregado->user)
        <div class="info-item"><span class="info-label">Estado</span><span class="info-value" style="color:var(--accent-green)"><i class="fas fa-check-circle" style="margin-right:4px"></i>Conta ativa</span></div>
        <div class="info-item"><span class="info-label">Nome de Utilizador</span><span class="info-value">{{ $encarregado->user->username }}</span></div>
        @else
        <div class="info-item"><span class="info-label">Estado</span><span class="info-value" style="color:var(--text-secondary)">Sem conta de acesso</span></div>
        <div class="info-item"><span class="info-label">Como dar acesso</span><span class="info-value" style="font-size:12px;color:var(--text-secondary)">Edite o encarregado e crie uma conta.</span></div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">Alunos Associados ({{ $encarregado->alunos->count() }})</div>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.alunos.create') }}" class="btn" style="padding:8px 14px;font-size:12px"><i class="fas fa-plus"></i> Novo Aluno</a>
        @endif
    </div>

    @if($encarregado->alunos->count() > 0)
    <table>
        <thead><tr><th>Aluno</th><th>Número</th><th>Turma</th><th>Contacto</th><th>Status</th><th style="text-align:right">Ações</th></tr></thead>
        <tbody>
            @foreach($encarregado->alunos as $aluno)
            <tr>
                <td>
                    <div class="user-cell">
                        <div class="user-avatar">{{ strtoupper(substr($aluno->name, 0, 1)) }}</div>
                        <div>
                            <div class="user-name">
                                <a href="{{ route('admin.alunos.show', $aluno) }}" style="color:var(--text-primary);text-decoration:none">{{ $aluno->name }}</a>
                            </div>
                            <div class="user-username">{{ $aluno->username }}</div>
                        </div>
                    </div>
                </td>
                <td style="color:var(--text-secondary);font-size:12px">{{ $aluno->numero ?? '—' }}</td>
                <td>
                    @if($aluno->turma)
                    <span class="tag tag-turma">{{ $aluno->turma->nome_turma }}</span>
                    @else
                    <span style="color:var(--text-secondary);font-size:12px">—</span>
                    @endif
                </td>
                <td style="font-size:12px">
                    @if($aluno->telefone)<span><i class="fas fa-phone" style="color:var(--text-secondary);margin-right:4px"></i>{{ $aluno->telefone }}</span>@endif
                </td>
                <td><span class="tag {{ $aluno->is_active ? 'tag-ativo' : 'tag-inativo' }}">{{ $aluno->is_active ? 'Ativo' : 'Inativo' }}</span></td>
                <td>
                    <div class="actions-cell" style="justify-content:flex-end">
                        <a href="{{ route('admin.alunos.show', $aluno) }}" class="btn-icon" title="Ver"><i class="fas fa-eye"></i></a>
                        @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('admin.encarregados.desassociar-aluno', [$encarregado, $aluno]) }}" style="display:inline" onsubmit="return confirm('Desassociar este aluno do encarregado?');">@csrf @method('DELETE')
                            <button type="submit" class="btn-icon danger" title="Desassociar"><i class="fas fa-user-minus"></i></button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state"><i class="fas fa-user-graduate"></i><div>Nenhum aluno associado a este encarregado.</div></div>
    @endif

    @if(auth()->user()->isAdmin())
    <form method="POST" action="{{ route('admin.encarregados.associar-aluno', $encarregado) }}" class="associate-bar">
        @csrf
        <div class="searchable-select" id="ss-aluno">
            <input type="hidden" name="aluno_id" id="aluno_id">
            <button type="button" class="ss-input" id="ss-aluno-btn">
                <span class="ss-value" id="ss-aluno-value"><span class="ss-placeholder">Pesquisar aluno para associar...</span></span>
                <i class="fas fa-chevron-down ss-caret"></i>
            </button>
            <div class="ss-dropdown" id="ss-aluno-dropdown">
                <div class="ss-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="ss-aluno-search" placeholder="Pesquisar por nome...">
                </div>
                <div class="ss-options" id="ss-aluno-options">
                    @forelse($alunosDisponiveis as $al)
                    <div class="ss-option" data-value="{{ $al->id }}" data-label="{{ $al->name }}">
                        <span>{{ $al->name }}</span>
                        <span style="margin-left:auto;font-size:11px;color:var(--text-secondary)">@if($al->numero)Nº {{ $al->numero }}@endif</span>
                    </div>
                    @empty
                    <div class="ss-option no-results">Todos os alunos já têm encarregado associado.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <button type="submit" class="btn-primary"><i class="fas fa-link"></i> Associar</button>
    </form>
    @endif
</div>

<script>
    document.addEventListener('click', function(e) {
        closeAllSearchables(e.target);
    });

    function initSearchableSelect(id) {
        const container = document.getElementById(id);
        if (!container) return;
        const btn = document.getElementById(id + '-btn');
        const dropdown = document.getElementById(id + '-dropdown');
        const search = document.getElementById(id + '-search');
        const options = document.getElementById(id + '-options');
        const hidden = document.getElementById(id.replace('ss-', '')) || container.querySelector('input[type="hidden"]');
        const valueSpan = document.getElementById(id + '-value');
        const placeholderText = valueSpan ? valueSpan.querySelector('.ss-placeholder') : null;
        let isOpen = false;

        if (!btn || !dropdown || !options || !hidden) return;

        function close() {
            isOpen = false;
            dropdown.classList.remove('open');
            if (search) search.value = '';
            filterOptions('');
        }

        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            closeAllSearchables(e.target);
            isOpen = !isOpen;
            dropdown.classList.toggle('open', isOpen);
            if (isOpen && search) { search.focus(); filterOptions(''); }
        });

        if (search) {
            search.addEventListener('input', function() {
                filterOptions(search.value);
            });
            search.addEventListener('click', function(e) { e.stopPropagation(); });
        }

        dropdown.addEventListener('click', function(e) { e.stopPropagation(); });

        options.querySelectorAll('.ss-option[data-value]').forEach(function(opt) {
            opt.addEventListener('click', function(e) {
                e.stopPropagation();
                const val = opt.getAttribute('data-value');
                const label = opt.getAttribute('data-label');
                hidden.value = val;
                valueSpan.innerHTML = label;
                if (placeholderText) placeholderText.style.display = 'none';
                options.querySelectorAll('.ss-option').forEach(function(o) { o.classList.remove('selected'); });
                opt.classList.add('selected');
                close();
            });
        });

        function filterOptions(q) {
            q = (q || '').toLowerCase();
            let visible = 0;
            options.querySelectorAll('.ss-option[data-value]').forEach(function(opt) {
                const label = (opt.getAttribute('data-label') || '').toLowerCase();
                const match = !q || label.indexOf(q) !== -1;
                opt.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            let noResults = options.querySelector('.ss-option.no-results');
            if (!noResults) {
                noResults = document.createElement('div');
                noResults.className = 'ss-option no-results';
                noResults.textContent = 'Nenhum resultado encontrado';
                options.appendChild(noResults);
            }
            noResults.style.display = visible > 0 ? 'none' : '';
        }

        window['ss_' + id + '_close'] = close;
    }

    function closeAllSearchables(except) {
        document.querySelectorAll('.searchable-select').forEach(function(c) {
            const d = c.querySelector('.ss-dropdown');
            if (d && c !== except && !c.contains(except)) {
                d.classList.remove('open');
            }
        });
    }

    initSearchableSelect('ss-aluno');
</script>
@endsection