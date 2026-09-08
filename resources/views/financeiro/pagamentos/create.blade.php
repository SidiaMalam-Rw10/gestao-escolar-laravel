@extends('layouts.app')

@section('title', 'Registar Pagamento')
@section('page-title', 'Registar Pagamento')

@section('content')
<style>
    .form-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:hidden;max-width:860px}
    .form-card-header{display:flex;align-items:center;gap:10px;padding:14px 20px;border-bottom:1px solid var(--border-color)}
    .form-card-header i{color:var(--accent-green);font-size:15px}
    .form-card-header .title{font-weight:600;font-size:13px}
    .form-card-header .sub{font-size:11px;color:var(--text-secondary);margin-top:2px}
    .form-card-body{padding:24px}
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
    .field{display:flex;flex-direction:column;gap:6px}
    .field label{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--text-secondary)}
    .field select,.field input,.field textarea{padding:9px 14px;background:var(--bg-input,#151D19);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;font-family:inherit}
    .field select:focus,.field input:focus,.field textarea:focus{outline:none;border-color:var(--accent-green)}
    .full{grid-column:1/-1}
    .btn{background:var(--accent-green);color:#000;border:none;padding:11px 22px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:background .15s}
    .btn:hover{background:#1ea34e}
    .checkbox-line{display:flex;align-items:center;gap:10px;font-size:13px;color:var(--text-secondary)}
    .checkbox-line input{width:16px;height:16px;accent-color:var(--accent-green);cursor:pointer}
    .info-banner{padding:12px 16px;background:rgba(96,165,250,.08);border:1px solid rgba(96,165,250,.25);border-radius:8px;margin-bottom:20px;font-size:12.5px;color:#93C5FD;display:flex;align-items:center;gap:8px}
    .error-msg{font-size:11px;color:#FCA5A5;margin-top:4px}
    @media(max-width:640px){.form-grid{grid-template-columns:1fr}}
</style>

<div class="info-banner">
    <i class="fas fa-info-circle"></i>
    Ao registar um pagamento, o sistema gera automaticamente um recibo e, se o aluno tiver um encarregado com conta de acesso, envia-lhe uma notificação com os dados do recibo.
</div>

<div class="form-card">
    <div class="form-card-header">
        <i class="fas fa-money-bill-wave"></i>
        <div>
            <div class="title">Registar pagamento</div>
            <div class="sub">Preencha os dados do pagamento do aluno</div>
        </div>
    </div>
    <div class="form-card-body">
        <form method="POST" action="{{ route('financeiro.pagamentos.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field full">
                    <label for="turma_id">Turma (para filtrar alunos)</label>
                    <select id="turma_id">
                        <option value="">— Todas as turmas —</option>
                        @foreach($turmas as $t)
                        <option value="{{ $t->id }}" data-propina="{{ $t->propina_mensal }}">{{ $t->nome_turma }} — {{ $t->nivel }} ({{ $t->alunos->count() }} alunos)</option>
                        @endforeach
                    </select>
                </div>

                <div class="field full">
                    <label for="aluno_id">Aluno</label>
                    <select name="aluno_id" id="aluno_id" required>
                        <option value="">— Selecionar aluno —</option>
                        @foreach($alunos as $al)
                        <option value="{{ $al->id }}" data-turma="{{ $al->turma_id }}" data-propina="{{ $al->turma?->propina_mensal }}" data-encarregado="{{ $al->encarregado_id ? 'sim' : 'nao' }}">
                            {{ $al->name }} — {{ $al->turma?->nome_turma ?? 'Sem turma' }} ({{ $al->numero ?? '—' }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>Mês</label>
                    <select name="mes" required>
                        @foreach($meses as $k => $m)
                        <option value="{{ $k }}" {{ $k === $mes ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>Ano</label>
                    <select name="ano" required>
                        @foreach([$ano, $ano - 1] as $a)
                        <option value="{{ $a }}" {{ $a === $ano ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>Valor (Xof)</label>
                    <input type="number" name="valor" id="valor" step="0.01" min="0" required placeholder="0.00">
                    <div class="error-msg" id="valor-hint"></div>
                </div>

                <div class="field">
                    <label>Data de pagamento</label>
                    <input type="date" name="data_pagamento" value="{{ now()->format('Y-m-d') }}" required>
                </div>

                <div class="field">
                    <label>Método</label>
                    <select name="metodo_pagamento" required>
                        <option value="">— Selecionar —</option>
                        <option value="Dinheiro">Dinheiro</option>
                        <option value="Transferência bancária">Transferência bancária</option>
                        <option value="Multicaixa">Multicaixa</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>

                <div class="field full">
                    <label>Observações (opcional)</label>
                    <textarea name="observacoes" rows="3" placeholder="Observações gerais do pagamento"></textarea>
                </div>

                <div class="field full">
                    <label class="checkbox-line" style="text-transform:none;letter-spacing:0">
                        <input type="checkbox" name="enviar_notificacao" value="1" checked>
                        Notificar o encarregado de educação (se existir conta de acesso)
                    </label>
                </div>
            </div>

            <div style="margin-top:24px;display:flex;gap:12px;align-items:center">
                <button type="submit" class="btn"><i class="fas fa-check-circle"></i> Registar pagamento</button>
                <a href="{{ route('financeiro.pagamentos.index') }}" style="font-size:13px;color:var(--text-secondary);text-decoration:none">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
    const turmaSelect = document.getElementById('turma_id');
    const alunoSelect = document.getElementById('aluno_id');
    const valorInput = document.getElementById('valor');
    const valorHint = document.getElementById('valor-hint');

    function filtrarAlunos() {
        const turmaId = turmaSelect.value;
        Array.from(alunoSelect.options).forEach(opt => {
            if (!opt.value) return;
            opt.style.display = (opt.dataset.turma === turmaId) ? '' : 'none';
        });
        if (turmaId) {
            const opt = Array.from(alunoSelect.options).find(o => o.dataset.turma === turmaId);
            if (opt) alunoSelect.selectedIndex = opt.index;
        }
        preencherValor();
    }

    function preencherValor() {
        const opt = alunoSelect.selectedOptions[0];
        const propina = opt && opt.dataset.propina;
        if (propina && parseFloat(propina) > 0) {
            valorInput.value = parseFloat(propina).toFixed(2);
            valorHint.textContent = 'Propina mensal da turma: ' + parseFloat(propina).toFixed(2) + ' Xof';
        } else {
            valorHint.textContent = '';
        }
    }

    turmaSelect.addEventListener('change', filtrarAlunos);
    alunoSelect.addEventListener('change', preencherValor);
</script>
@endsection