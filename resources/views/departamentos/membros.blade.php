@extends('layouts.app')

@section('title', 'Gerir Membros')
@section('page-title', 'Membros - ' . $departamento->nome)

@section('content')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 20px;
        font-weight: 700;
    }

    .form-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 28px;
        margin-bottom: 20px;
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
        grid-template-columns: 1fr 1fr 1fr;
        gap: 14px;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .form-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .form-input,
    .form-select {
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

    .form-error {
        font-size: 11px;
        color: #FCA5A5;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .checkbox-group input[type="checkbox"] {
        width: 14px;
        height: 14px;
        accent-color: var(--accent-green);
    }

    .checkbox-group label {
        font-size: 12px;
        color: var(--text-secondary);
        cursor: pointer;
    }

    .btn-primary {
        background: var(--accent-green);
        color: #000;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s;
        text-decoration: none;
        white-space: nowrap;
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

    .table-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        overflow: hidden;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .table-title {
        font-size: 14px;
        font-weight: 600;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead th {
        text-align: left;
        padding: 12px 20px;
        font-size: 10px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 600;
        border-bottom: 1px solid var(--border-color);
        background: rgba(0, 0, 0, 0.2);
    }

    tbody tr {
        transition: background 0.15s;
    }

    tbody tr:hover {
        background: var(--bg-hover);
    }

    tbody td {
        padding: 12px 20px;
        font-size: 13px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--accent-green);
        color: #000;
        display: grid;
        place-items: center;
        font-weight: 700;
        font-size: 11px;
        flex-shrink: 0;
    }

    .tag {
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 600;
    }

    .tag-principal {
        background: rgba(34, 197, 94, 0.12);
        color: var(--accent-green);
    }

    .tag-regime {
        background: rgba(59, 130, 246, 0.12);
        color: #60A5FA;
    }

    .actions-cell {
        display: flex;
        gap: 6px;
    }

    .btn-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        color: var(--text-secondary);
        display: grid;
        place-items: center;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
        font-size: 11px;
    }

    .btn-icon:hover {
        border-color: rgba(255, 255, 255, 0.15);
        color: var(--text-primary);
        background: var(--bg-hover);
    }

    .btn-icon.danger:hover {
        border-color: rgba(239, 68, 68, 0.3);
        color: #FCA5A5;
        background: rgba(239, 68, 68, 0.08);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-secondary);
        font-size: 12px;
    }

    .alert-success {
        padding: 12px 16px;
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.3);
        color: var(--accent-green);
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: 100;
        place-items: center;
    }

    .modal-overlay.active {
        display: grid;
    }

    .modal {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 24px;
        width: 100%;
        max-width: 420px;
    }

    .modal-title {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .modal-grid {
        display: grid;
        gap: 12px;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 16px;
        justify-content: flex-end;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@if(session('success'))
<div class="alert-success">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

<div class="page-header">
    <div class="page-title">{{ $departamento->nome }} — Membros</div>
    <a href="{{ route('admin.departamentos.show', $departamento) }}" class="btn">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

@if(auth()->user()->isAdmin())
<div class="form-card">
    <div class="form-section-title">Adicionar Membro</div>
    <form method="POST" action="{{ route('admin.departamentos.membros.adicionar', $departamento) }}">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Usuário</label>
                <div class="searchable-select" id="ss-usuario">
                    <input type="hidden" name="user_id" id="user_id" value="{{ old('user_id') }}">
                    <button type="button" class="ss-input" id="ss-usuario-btn">
                        <span class="ss-value ss-placeholder" id="ss-usuario-value">Pesquisar usuário...</span>
                        <i class="fas fa-chevron-down" style="color: var(--text-secondary); font-size: 10px;"></i>
                    </button>
                    <div class="ss-dropdown" id="ss-usuario-dropdown">
                        <div class="ss-search-box">
                            <input type="text" placeholder="Pesquisar por nome ou função..." id="ss-usuario-search">
                        </div>
                        <div class="ss-options" id="ss-usuario-options">
                            <div class="ss-option" data-value="" data-label="">— Nenhum —</div>
                            @foreach($users as $user)
                            <div class="ss-option {{ old('user_id') == $user->id ? 'selected' : '' }}" data-value="{{ $user->id }}" data-label="{{ $user->name }} ({{ ucfirst($user->role) }})">
                                {{ $user->name }} ({{ ucfirst($user->role) }})
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Cargo</label>
                <input type="text" name="cargo" class="form-input" placeholder="Ex: Professor, Chefe...">
            </div>

            <div class="form-group">
                <label class="form-label">Regime</label>
                <select name="regime" class="form-select" required>
                    <option value="regular">Regular</option>
                    <option value="especial">Especial</option>
                    <option value="tempo_integral">Tempo Integral</option>
                    <option value="parcial">Parcial</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Ano Lectivo</label>
                <input type="number" name="ano_lectivo" class="form-input" value="{{ date('Y') }}" min="2020" max="2030">
            </div>

            <div class="form-group">
                <label class="form-label">&nbsp;</label>
                <div class="checkbox-group">
                    <input type="checkbox" name="is_principal" value="1" id="is_principal">
                    <label for="is_principal">Atribuição principal</label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-plus"></i> Adicionar
                </button>
            </div>
        </div>
    </form>
</div>
@endif

<div class="table-card">
    <div class="table-header">
        <div class="table-title">Membros Atuais ({{ $departamento->users->count() }})</div>
    </div>

    @if($departamento->users->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Membro</th>
                <th>Cargo</th>
                <th>Regime</th>
                <th>Atribuição</th>
                <th style="text-align: right;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departamento->users as $member)
            <tr>
                <td>
                    <div class="user-cell">
                        <div class="user-avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                        <div>
                            <div style="font-weight: 500;">{{ $member->name }}</div>
                            <div style="font-size: 11px; color: var(--text-secondary);">{{ ucfirst($member->role) }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $member->pivot->cargo ?? '—' }}</td>
                <td>
                    <span class="tag tag-regime">{{ ucfirst(str_replace('_', ' ', $member->pivot->regime)) }}</span>
                </td>
                <td>
                    @if($member->pivot->is_principal)
                    <span class="tag tag-principal">Principal</span>
                    @else
                    <span style="font-size: 11px; color: var(--text-secondary);">Secundária</span>
                    @endif
                </td>
                <td>
                    <div class="actions-cell" style="justify-content: flex-end;">
                        @if(auth()->user()->isAdmin())
                        <button class="btn-icon" title="Editar" onclick="openEditModal('{{ route('admin.departamentos.membros.atualizar', [$departamento, $member]) }}', '{{ $member->pivot->cargo }}', '{{ $member->pivot->regime }}', '{{ $member->pivot->is_principal }}')">
                            <i class="fas fa-pen"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.departamentos.membros.remover', [$departamento, $member]) }}" style="display: inline;"
                              onsubmit="return confirm('Remover este membro do departamento?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon danger" title="Remover">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state">
        <i class="fas fa-user-slash"></i>
        <div>Nenhum membro neste departamento</div>
    </div>
    @endif
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-title">Editar Atribuição</div>
        <form method="POST" id="editForm" action="">
            @csrf
            @method('PUT')
            <div class="modal-grid">
                <div class="form-group">
                    <label class="form-label">Cargo</label>
                    <input type="text" name="cargo" id="edit-cargo" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Regime</label>
                    <select name="regime" id="edit-regime" class="form-select" required>
                        <option value="regular">Regular</option>
                        <option value="especial">Especial</option>
                        <option value="tempo_integral">Tempo Integral</option>
                        <option value="parcial">Parcial</option>
                    </select>
                </div>
                <div class="form-group">
                    <div class="checkbox-group" style="margin-top: 4px;">
                        <input type="checkbox" name="is_principal" value="1" id="edit-principal">
                        <label for="edit-principal">Atribuição principal</label>
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn" onclick="closeEditModal()">Cancelar</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>
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
                valueSpan.textContent = label || 'Pesquisar usuário...';
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

    function openEditModal(action, cargo, regime, isPrincipal) {
        const form = document.getElementById('editForm');
        form.action = action;
        document.getElementById('edit-cargo').value = cargo || '';
        document.getElementById('edit-regime').value = regime || 'regular';
        document.getElementById('edit-principal').checked = isPrincipal === '1';
        document.getElementById('editModal').classList.add('active');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.remove('active');
    }

    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });

    window.addEventListener('load', function() {
        initSearchableSelect('ss-usuario');
    });
</script>
@endsection
