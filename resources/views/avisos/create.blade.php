@extends('layouts.app')

@section('title', 'Novo Aviso')
@section('page-title', 'Publicar Aviso')

@section('content')
<style>
    .form-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:28px;max-width:820px;margin:0 auto}
    .form-section-title{font-size:14px;font-weight:600;color:var(--text-primary);border-bottom:1px solid var(--border-color);padding-bottom:12px;margin-bottom:20px}
    .form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:18px}
    .form-group{display:flex;flex-direction:column;gap:6px}
    .form-group.full-width{grid-column:1/-1}
    .form-label{font-size:12px;font-weight:500;color:var(--text-primary)}
    .form-label .required{color:#FCA5A5}
    .form-input,.form-select,textarea.form-input{width:100%;padding:10px 12px;background:var(--bg-input,#151D19);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s;resize:vertical}
    .form-input:focus,.form-select:focus{outline:none;border-color:var(--accent-green)}
    .form-input::placeholder{color:var(--text-secondary)}
    .form-hint{font-size:11px;color:var(--text-secondary);margin-top:4px}
    .form-error{font-size:11px;color:#FCA5A5;margin-top:4px}
    .is-invalid{border-color:rgba(239,68,68,.5)!important}
    .form-actions{display:flex;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color)}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 20px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 20px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .alert-error{padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;border-radius:8px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px}
    .target-box{display:none}
    .target-box.open{display:block;grid-column:1/-1;padding:16px;background:rgba(59,130,246,.06);border:1px dashed rgba(59,130,246,.35);border-radius:8px}
    .searchable-select{position:relative}
    .searchable-select .ss-input{width:100%;display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 14px;background:var(--bg-input,#151D19);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;cursor:pointer;text-align:left;transition:border-color .15s}
    .searchable-select .ss-input .ss-value{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .searchable-select .ss-input .ss-placeholder{color:var(--text-secondary)}
    .searchable-select .ss-caret{color:var(--text-secondary);font-size:11px;flex-shrink:0}
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
    @media (max-width:768px){.form-grid{grid-template-columns:1fr}.form-group.full-width{grid-column:auto}}
</style>

@if($errors->any())
<div class="alert-error"><i class="fas fa-exclamation-circle"></i>{{ $errors->first() }}</div>
@endif

<form action="{{ route('admin.avisos.store') }}" method="POST">
    @csrf
    <div class="form-card">
        <div class="form-section-title">Novo Aviso</div>
        <div class="form-grid">
            <div class="form-group full-width">
                <label class="form-label">Título <span class="required">*</span></label>
                <input type="text" name="titulo" value="{{ old('titulo') }}" class="form-input @error('titulo') is-invalid @enderror" placeholder="Ex: Reunião de pais e encarregados">
                @error('titulo')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group full-width">
                <label class="form-label">Mensagem <span class="required">*</span></label>
                <textarea name="mensagem" rows="4" class="form-input @error('mensagem') is-invalid @enderror" placeholder="Escreva o conteúdo do aviso...">{{ old('mensagem') }}</textarea>
                @error('mensagem')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group full-width">
                <label class="form-label">Destinatários <span class="required">*</span></label>
                <select name="destinatario_tipo" id="destinatario_tipo" class="form-select @error('destinatario_tipo') is-invalid @enderror" onchange="toggleTarget(this.value)">
                    <option value="">Selecionar...</option>
                    <option value="todos" {{ old('destinatario_tipo') === 'todos' ? 'selected' : '' }}>Todos (escola)</option>
                    <option value="alunos" {{ old('destinatario_tipo') === 'alunos' ? 'selected' : '' }}>Alunos</option>
                    <option value="professores" {{ old('destinatario_tipo') === 'professores' ? 'selected' : '' }}>Professores</option>
                    <option value="turma" {{ old('destinatario_tipo') === 'turma' ? 'selected' : '' }}>Turma específica</option>
                    <option value="individual" {{ old('destinatario_tipo') === 'individual' ? 'selected' : '' }}>Individual (Aluno ou Encarregado)</option>
                </select>
                @error('destinatario_tipo')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="target-box {{ old('destinatario_tipo') === 'turma' ? 'open' : '' }}" id="box-turma">
                <div class="form-group">
                    <label class="form-label">Escolher Turma <span class="required">*</span></label>
                    <div class="searchable-select" id="ss-turma">
                        <input type="hidden" name="turma_id" id="turma_id" value="{{ old('turma_id') }}">
                        <button type="button" class="ss-input" id="ss-turma-btn">
                            <span class="ss-value" id="ss-turma-value">
                                @if($turmas->where('id', old('turma_id'))->first())
                                {{ $turmas->where('id', old('turma_id'))->first()->nome_turma }}
                                @else
                                <span class="ss-placeholder">Pesquisar turma...</span>
                                @endif
                            </span>
                            <i class="fas fa-chevron-down ss-caret"></i>
                        </button>
                        <div class="ss-dropdown" id="ss-turma-dropdown">
                            <div class="ss-search-box"><i class="fas fa-search"></i><input type="text" id="ss-turma-search" placeholder="Pesquisar turma..."></div>
                            <div class="ss-options" id="ss-turma-options">
                                @foreach($turmas as $turma)
                                <div class="ss-option" data-value="{{ $turma->id }}" data-label="{{ $turma->nome_turma }}"><span>{{ $turma->nome_turma }} - {{ $turma->nivel }}</span></div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @error('turma_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="target-box {{ old('destinatario_tipo') === 'individual' ? 'open' : '' }}" id="box-individual">
                <div class="form-group">
                    <label class="form-label">Escolher Destinatário <span class="required">*</span></label>
                    <div class="searchable-select" id="ss-aluno">
                        <input type="hidden" name="destinatario_id" id="destinatario_id" value="{{ old('destinatario_id') }}">
                        <button type="button" class="ss-input" id="ss-aluno-btn">
                            <span class="ss-value" id="ss-aluno-value">
                                @if($alunos->where('id', old('destinatario_id'))->first())
                                {{ $alunos->where('id', old('destinatario_id'))->first()->name }}
                                @elseif($encarregados->where('id', old('destinatario_id'))->first())
                                {{ $encarregados->where('id', old('destinatario_id'))->first()->name }}
                                @else
                                <span class="ss-placeholder">Pesquisar aluno ou encarregado...</span>
                                @endif
                            </span>
                            <i class="fas fa-chevron-down ss-caret"></i>
                        </button>
                        <div class="ss-dropdown" id="ss-aluno-dropdown">
                            <div class="ss-search-box"><i class="fas fa-search"></i><input type="text" id="ss-aluno-search" placeholder="Pesquisar aluno ou encarregado..."></div>
                            <div class="ss-options" id="ss-aluno-options">
                                @if($alunos->count() > 0)
                                <div class="ss-option no-results" style="display:none" data-group="alunos">Alunos</div>
                                @foreach($alunos as $al)
                                <div class="ss-option" data-value="{{ $al->id }}" data-label="{{ $al->name }}"><span><i class="fas fa-graduation-cap" style="font-size:11px;color:var(--text-secondary);margin-right:6px"></i>{{ $al->name }}</span><span style="margin-left:auto;font-size:11px;color:var(--text-secondary)">@if($al->numero)Nº {{ $al->numero }}@endif</span></div>
                                @endforeach
                                @endif
                                @if($encarregados->count() > 0)
                                <div class="ss-option no-results" style="display:none">Encarregados (Pais)</div>
                                @foreach($encarregados as $enc)
                                <div class="ss-option" data-value="{{ $enc->id }}" data-label="{{ $enc->name }}"><span><i class="fas fa-user-tie" style="font-size:11px;color:var(--text-secondary);margin-right:6px"></i>{{ $enc->name }}</span><span style="margin-left:auto;font-size:11px;color:var(--text-secondary)">Encarregado</span></div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    @error('destinatario_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-bullhorn"></i> Publicar</button>
            <a href="{{ route('admin.avisos.index') }}" class="btn"><i class="fas fa-times"></i> Cancelar</a>
        </div>
    </div>
</form>

<script>
    function toggleTarget(value) {
        document.getElementById('box-turma').classList.toggle('open', value === 'turma');
        document.getElementById('box-individual').classList.toggle('open', value === 'individual');
    }

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
        const hidden = container.querySelector('input[type="hidden"]');
        const valueSpan = document.getElementById(id + '-value');
        const placeholderText = valueSpan ? valueSpan.querySelector('.ss-placeholder') : null;
        let isOpen = false;

        if (!btn || !dropdown || !options || !hidden) return;

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
            search.addEventListener('input', function() { filterOptions(search.value); });
            search.addEventListener('click', function(e) { e.stopPropagation(); });
        }

        dropdown.addEventListener('click', function(e) { e.stopPropagation(); });

        options.querySelectorAll('.ss-option[data-value]').forEach(function(opt) {
            opt.addEventListener('click', function(e) {
                e.stopPropagation();
                hidden.value = opt.getAttribute('data-value');
                valueSpan.innerHTML = opt.getAttribute('data-label');
                if (placeholderText) placeholderText.style.display = 'none';
                options.querySelectorAll('.ss-option').forEach(function(o) { o.classList.remove('selected'); });
                opt.classList.add('selected');
                close();
            });
        });
    }

    function closeAllSearchables(except) {
        document.querySelectorAll('.searchable-select').forEach(function(c) {
            const d = c.querySelector('.ss-dropdown');
            if (d && c !== except && !c.contains(except)) {
                d.classList.remove('open');
            }
        });
    }

    initSearchableSelect('ss-turma');
    initSearchableSelect('ss-aluno');
</script>
@endsection