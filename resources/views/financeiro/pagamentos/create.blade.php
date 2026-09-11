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
    .sub-label{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--text-secondary);display:block;margin-bottom:6px}
    .inline-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    @media(max-width:640px){.inline-grid{grid-template-columns:1fr}}
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

                <div class="field full">
                    <label>Período a pagar</label>
                    <div class="inline-grid">
                        <div>
                            <span class="sub-label">Mês inicial</span>
                            <select name="mes" id="mes_inicial" required>
                                @foreach($meses as $k => $m)
                                <option value="{{ $k }}" {{ $k === $mes ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <span class="sub-label">Nº de meses a pagar</span>
                            <select name="quantidade_meses" id="quantidade_meses" required>
                                @for($n = 1; $n <= 12; $n++)
                                <option value="{{ $n }}" {{ $n === 1 ? 'selected' : '' }}>{{ $n }} mês{{ $n > 1 ? 'es' : '' }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="error-msg" id="periodo-resumo"></div>
                    @error('quantidade_meses')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
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
    const mesSelect = document.getElementById('mes_inicial');
    const qtdSelect = document.getElementById('quantidade_meses');
    const periodoResumo = document.getElementById('periodo-resumo');

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

    function nomeMes(numero) {
        const opt = Array.from(mesSelect.options).find(o => parseInt(o.value, 10) === numero);
        return opt ? opt.textContent.trim() : '';
    }

    function aplicarMudancas() {
        preencherValor();
        preencherResumo();
    }

    function preencherValor() {
        const opt = alunoSelect.selectedOptions[0];
        const propina = opt && opt.dataset.propina;
        const qtd = parseInt(qtdSelect.value || '1', 10);
        if (propina && parseFloat(propina) > 0) {
            const total = parseFloat(propina) * qtd;
            valorInput.value = total.toFixed(2);
            valorHint.textContent = 'Propina mensal: ' + parseFloat(propina).toFixed(2) + ' Xof × ' + qtd + ' mes(es) = ' + total.toFixed(2) + ' Xof';
        } else {
            valorHint.textContent = '';
        }
        preencherResumo();
    }

    function preencherResumo() {
        const mesIni = parseInt(mesSelect.value || '1', 10);
        const qtd = parseInt(qtdSelect.value || '1', 10);
        const anoInicial = parseInt(document.querySelector('select[name="ano"]')?.value || '', 10);
        const nomes = [];
        for (let i = 0; i < qtd; i++) {
            const mes = ((mesIni - 1 + i) % 12) + 1;
            const ano = anoInicial + Math.floor((mesIni - 1 + i) / 12);
            nomes.push(nomeMes(mes) + '/' + ano);
        }
        const total = parseFloat(valorInput.value || '0');
        if (qtd > 1) {
            periodoResumo.textContent = 'Serão registados ' + qtd + ' meses: ' + nomes.join(', ') + ' · Total: ' + total.toFixed(2) + ' Xof';
        } else {
            periodoResumo.textContent = '';
        }
    }

    turmaSelect.addEventListener('change', filtrarAlunos);
    alunoSelect.addEventListener('change', preencherValor);
    mesSelect.addEventListener('change', aplicarMudancas);
    qtdSelect.addEventListener('change', aplicarMudancas);
    valorInput.addEventListener('input', preencherResumo);
    document.querySelector('select[name="ano"]')?.addEventListener('change', preencherResumo);

    aplicarMudancas();
</script>
@endsection