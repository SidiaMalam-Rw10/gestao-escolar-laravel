@extends('layouts.app')

@section('title', 'Editar Usuário')
@section('page-title', 'Editar Usuário')

@section('content')
<style>
    .form-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 28px;
        max-width: 720px;
    }

    .form-section-title {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-secondary);
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border-color);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group.full-width {
        grid-column: span 2;
    }

    .form-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 6px;
        color: var(--text-primary);
    }

    .form-label .required {
        color: #FCA5A5;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 10px 14px;
        background: var(--bg-input, #151D19);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        font-size: 13px;
        transition: border-color 0.15s;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--accent-green);
    }

    .form-input::placeholder {
        color: var(--text-secondary);
    }

    .form-hint {
        font-size: 11px;
        color: var(--text-secondary);
        margin-top: 4px;
    }

    .form-error {
        font-size: 11px;
        color: #FCA5A5;
        margin-top: 4px;
    }

    .is-invalid {
        border-color: #FCA5A5 !important;
    }

    .searchable-select {
        position: relative;
        width: 100%;
    }

    .searchable-select .ss-input {
        width: 100%;
        padding: 10px 14px;
        background: var(--bg-input, #151D19);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        font-size: 13px;
        cursor: pointer;
        text-align: left;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        transition: border-color 0.15s;
        font-family: inherit;
        box-sizing: border-box;
    }

    .searchable-select .ss-input:focus,
    .searchable-select .ss-input:focus-visible {
        outline: none;
        border-color: var(--accent-green);
    }

    .searchable-select .ss-input .ss-placeholder {
        color: var(--text-secondary);
    }

    .searchable-select .ss-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        z-index: 100;
        overflow: hidden;
    }

    .searchable-select .ss-dropdown.open {
        display: block;
    }

    .searchable-select .ss-search-box {
        padding: 8px;
        border-bottom: 1px solid var(--border-color);
    }

    .searchable-select .ss-search-box input {
        width: 100%;
        padding: 8px 12px;
        background: var(--bg-input, #151D19);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        font-size: 13px;
        box-sizing: border-box;
    }

    .searchable-select .ss-search-box input:focus {
        outline: none;
        border-color: var(--accent-green);
    }

    .searchable-select .ss-options {
        max-height: 180px;
        overflow-y: auto;
    }

    .searchable-select .ss-option {
        padding: 8px 14px;
        font-size: 13px;
        cursor: pointer;
        transition: background 0.1s;
        color: var(--text-primary);
    }

    .searchable-select .ss-option:hover {
        background: var(--bg-hover);
    }

    .searchable-select .ss-option.selected {
        background: rgba(34, 197, 94, 0.12);
        color: var(--accent-green);
    }

    .searchable-select .ss-option.no-results {
        color: var(--text-secondary);
        cursor: default;
        font-size: 12px;
    }

    .searchable-select .ss-option.no-results:hover {
        background: transparent;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        padding-top: 16px;
        border-top: 1px solid var(--border-color);
    }

    .btn-primary {
        background: var(--accent-green);
        color: #000;
        border: none;
        padding: 10px 24px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s;
        text-decoration: none;
    }

    .btn-primary:hover {
        background: #1ea34e;
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
    }

    .btn:hover {
        border-color: rgba(255, 255, 255, 0.15);
        background: var(--bg-hover);
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

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 6px;
    }

    .checkbox-group input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: var(--accent-green);
    }

    .checkbox-group label {
        font-size: 13px;
        color: var(--text-secondary);
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full-width {
            grid-column: span 1;
        }
    }
</style>

@if($errors->any())
<div class="alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <span>Corrija os erros abaixo para continuar.</span>
</div>
@endif

<div class="form-card">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="form-section-title">Dados Pessoais</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Nome Completo <span class="required">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input @error('name') is-invalid @enderror" required>
                @error('name')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Username <span class="required">*</span></label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-input @error('username') is-invalid @enderror" required>
                @error('username')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input @error('email') is-invalid @enderror">
                @error('email')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Número</label>
                <input type="text" name="numero" value="{{ old('numero', $user->numero) }}" class="form-input @error('numero') is-invalid @enderror">
                @error('numero')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" value="{{ old('telefone', $user->telefone) }}" class="form-input @error('telefone') is-invalid @enderror">
                @error('telefone')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Género</label>
                <select name="genero" class="form-select @error('genero') is-invalid @enderror">
                    <option value="">Selecionar...</option>
                    <option value="M" {{ old('genero', $user->genero) === 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('genero', $user->genero) === 'F' ? 'selected' : '' }}>Feminino</option>
                </select>
                @error('genero')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group full-width">
                <label class="form-label">Endereço</label>
                <input type="text" name="endereco" value="{{ old('endereco', $user->endereco) }}" class="form-input @error('endereco') is-invalid @enderror">
                @error('endereco')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-section-title">Alterar Password (deixe vazio para manter)</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Nova Password</label>
                <input type="password" name="password" class="form-input @error('password') is-invalid @enderror">
                @error('password')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirmar Nova Password</label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>
        </div>

        <div class="form-section-title">Função e Dados Académicos</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Função <span class="required">*</span></label>
                <select name="role" class="form-select @error('role') is-invalid @enderror" required id="role-select">
                    <option value="">Selecionar...</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrador/a</option>
                    <option value="diretor" {{ old('role', $user->role) === 'diretor' ? 'selected' : '' }}>Diretor/a</option>
                    <option value="financeiro" {{ old('role', $user->role) === 'financeiro' ? 'selected' : '' }}>Financeiro/a</option>
                    <option value="professor" {{ old('role', $user->role) === 'professor' ? 'selected' : '' }}>Professor/a</option>
                    <option value="aluno" {{ old('role', $user->role) === 'aluno' ? 'selected' : '' }}>Aluno/a</option>
                    <option value="auxiliar" {{ old('role', $user->role) === 'auxiliar' ? 'selected' : '' }}>Auxiliar</option>
                    <option value="pctp" {{ old('role', $user->role) === 'pctp' ? 'selected' : '' }}>PCTP (Presidente do Conselho Técnico Pedagógico)</option>
                    <option value="encarregado" {{ old('role', $user->role) === 'encarregado' ? 'selected' : '' }}>Encarregado de Educação</option>
                    <option value="funcionario" {{ old('role', $user->role) === 'funcionario' ? 'selected' : '' }}>Funcionário/a</option>
                </select>
                @error('role')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group full-width">
                <label class="form-label">Funções adicionais <span class="form-hint" style="display:block; margin-top:2px;">Um usuário pode ter mais de uma função (ex: Director/a + Professor/a)</span></label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 8px;">
                    @php $extraRoles = old('roles', $user->roles ?? []); @endphp
                    @foreach([
                        'admin' => 'Administrador/a',
                        'diretor' => 'Diretor/a',
                        'financeiro' => 'Financeiro/a',
                        'professor' => 'Professor/a',
                        'aluno' => 'Aluno/a',
                        'auxiliar' => 'Auxiliar',
                        'pctp' => 'PCTP',
                        'encarregado' => 'Encarregado',
                        'funcionario' => 'Funcionário/a',
                    ] as $value => $label)
                    <label class="checkbox-group" style="gap: 6px;">
                        <input type="checkbox" name="roles[]" value="{{ $value }}" {{ in_array($value, $extraRoles) ? 'checked' : '' }}>
                        <span>{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                @error('roles')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Ano Lectivo</label>
                <input type="number" name="ano_lectivo" value="{{ old('ano_lectivo', $user->ano_lectivo) }}" min="2020" max="2030" class="form-input @error('ano_lectivo') is-invalid @enderror">
                @error('ano_lectivo')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" id="disciplina-group">
                <label class="form-label">Disciplina</label>
                <input type="text" name="disciplina" value="{{ old('disciplina', $user->disciplina) }}" class="form-input @error('disciplina') is-invalid @enderror" placeholder="Ex: Matemática">
                @error('disciplina')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" id="turma-group">
                <label class="form-label">Turma</label>
                <div class="searchable-select" id="ss-turma">
                    <input type="hidden" name="turma_id" id="turma_id" value="{{ old('turma_id', $user->turma_id) }}">
                    <button type="button" class="ss-input" id="ss-turma-btn">
                        <span class="ss-value {{ $selectedTurma ? '' : 'ss-placeholder' }}" id="ss-turma-value">
                            {{ $selectedTurma ? $selectedTurma->nome_turma . ' - ' . $selectedTurma->nivel : 'Pesquisar turma...' }}
                        </span>
                        <i class="fas fa-chevron-down" style="color: var(--text-secondary); font-size: 10px;"></i>
                    </button>
                    <div class="ss-dropdown" id="ss-turma-dropdown">
                        <div class="ss-search-box">
                            <input type="text" placeholder="Pesquisar por nome ou nível..." id="ss-turma-search">
                        </div>
                        <div class="ss-options" id="ss-turma-options">
                            <div class="ss-option" data-value="" data-label="">— Nenhuma —</div>
                            @foreach($turmas as $turma)
                            <div class="ss-option {{ old('turma_id', $user->turma_id) == $turma->id ? 'selected' : '' }}" data-value="{{ $turma->id }}" data-label="{{ $turma->nome_turma }} - {{ $turma->nivel }}">
                                {{ $turma->nome_turma }} - {{ $turma->nivel }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @error('turma_id')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" id="nivel-group">
                <label class="form-label">Nível</label>
                <input type="text" name="nivel" value="{{ old('nivel', $user->nivel) }}" class="form-input @error('nivel') is-invalid @enderror" placeholder="Ex: 10ª classe">
                @error('nivel')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Estado</label>
                <div class="checkbox-group">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                    <label for="is_active">Conta ativa</label>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Guardar Alterações
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </form>
</div>

<script>
    function initSearchableSelect(id) {
        const container = document.getElementById(id);
        if (!container) return;
        const btn = container.querySelector('.ss-input');
        const dropdown = container.querySelector('.ss-dropdown');
        const search = container.querySelector('.ss-search-box input');
        const options = container.querySelectorAll('.ss-options .ss-option');
        const hidden = container.querySelector('input[type="hidden"]');
        const valueSpan = container.querySelector('.ss-value');

        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = dropdown.classList.contains('open');
            closeAllSearchables();
            if (!isOpen) {
                dropdown.classList.add('open');
                if (search) {
                    search.value = '';
                    search.focus();
                    filterOptions();
                }
            }
        });

        search.addEventListener('input', filterOptions);
        search.addEventListener('click', function(e) { e.stopPropagation(); });

        options.forEach(function(opt) {
            opt.addEventListener('click', function() {
                const val = opt.getAttribute('data-value');
                const label = opt.getAttribute('data-label');
                hidden.value = val;
                valueSpan.textContent = label || 'Pesquisar turma...';
                valueSpan.classList.toggle('ss-placeholder', !label);
                options.forEach(o => o.classList.remove('selected'));
                opt.classList.add('selected');
                dropdown.classList.remove('open');
            });
        });

        function filterOptions() {
            const q = search ? search.value.toLowerCase() : '';
            let visible = 0;
            options.forEach(function(opt) {
                const label = (opt.getAttribute('data-label') || '').toLowerCase();
                const match = label.indexOf(q) !== -1;
                opt.style.display = match ? 'block' : 'none';
                if (match) visible++;
            });
            let noResults = container.querySelector('.ss-option.no-results');
            if (visible === 0) {
                if (!noResults) {
                    noResults = document.createElement('div');
                    noResults.className = 'ss-option no-results';
                    noResults.textContent = 'Nenhum resultado encontrado';
                    container.querySelector('.ss-options').appendChild(noResults);
                }
                noResults.style.display = 'block';
            } else if (noResults) {
                noResults.style.display = 'none';
            }
        }
    }

    function closeAllSearchables() {
        document.querySelectorAll('.ss-dropdown.open').forEach(function(d) {
            d.classList.remove('open');
        });
    }

    document.addEventListener('click', function() { closeAllSearchables(); });

    document.getElementById('role-select').addEventListener('change', function() {
        const role = this.value;
        document.getElementById('disciplina-group').style.display = role === 'professor' ? 'block' : 'none';
        document.getElementById('turma-group').style.display = role === 'aluno' ? 'block' : 'none';
        document.getElementById('nivel-group').style.display = (role === 'aluno' || role === 'professor') ? 'block' : 'none';
        if (role !== 'aluno') {
            document.getElementById('turma_id').value = '';
            const span = document.getElementById('ss-turma-value');
            span.textContent = 'Pesquisar turma...';
            span.classList.add('ss-placeholder');
        }
    });

    window.addEventListener('load', function() {
        document.getElementById('role-select').dispatchEvent(new Event('change'));
        initSearchableSelect('ss-turma');
    });
</script>
@endsection
