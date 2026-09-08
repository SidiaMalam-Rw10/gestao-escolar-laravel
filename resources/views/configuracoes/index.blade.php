@extends('layouts.app')

@section('title', 'Configurações da Escola')
@section('page-title', 'Configurações')

@section('content')
@php use App\Models\Configuracao; @endphp
<style>
    .cfg-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:hidden}
    .cfg-tabs{display:flex;border-bottom:1px solid var(--border-color);background:rgba(255,255,255,.02)}
    .cfg-tab{padding:14px 18px;font-size:13px;font-weight:600;color:var(--text-secondary);cursor:pointer;border:none;background:none;display:flex;align-items:center;gap:8px;transition:all .15s;border-bottom:2px solid transparent}
    .cfg-tab:hover{color:var(--text-primary);background:var(--bg-hover)}
    .cfg-tab.active{color:var(--accent-green);border-bottom-color:var(--accent-green)}
    .cfg-panel{display:none;padding:24px;max-width:640px}
    .cfg-panel.active{display:block}
    .cfg-hint{font-size:11px;color:var(--text-secondary);margin-top:5px}
    .form-group{margin-bottom:16px}
    .form-label{display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:var(--text-secondary)}
    .form-input,.form-select,.form-textarea{width:100%;padding:10px 14px;background:var(--bg-input,var(--bg-card));border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s}
    .form-input:focus,.form-select:focus,.form-textarea:focus{outline:none;border-color:var(--accent-green)}
    .form-input::-webkit-calendar-picker-indicator{filter:invert(.6)}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .form-error{color:#FCA5A5;font-size:11px;margin-top:4px}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn-primary:hover{background:#1ea34e}
    .cfg-check{display:flex;align-items:center;gap:10px;cursor:pointer}
    .cfg-check input{width:16px;height:16px;accent-color:var(--accent-green)}
    .cfg-logo{margin-bottom:16px;display:flex;align-items:center;gap:16px}
    .cfg-logo img{width:64px;height:64px;object-fit:contain;border-radius:8px;border:1px solid var(--border-color);background:var(--bg-main)}
    .cfg-logo .file-pick{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:8px 14px;border-radius:6px;font-size:12px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:all .15s}
    .cfg-logo .file-pick:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    @media(max-width:640px){.form-row{grid-template-columns:1fr}.cfg-tab{flex:1;justify-content:center;padding:12px}@media(max-width:640px){.cfg-tab span{display:none}}}
</style>

@php
    $c = fn ($k, $d = '') => $configs[$k] ?? $d;
@endphp

<section class="cfg-card">
    <div class="cfg-tabs">
        <button type="button" class="cfg-tab active" data-cfg="escola"><i class="fas fa-school"></i><span>Dados da escola</span></button>
        <button type="button" class="cfg-tab" data-cfg="periodo"><i class="fas fa-calendar-alt"></i><span>Períodos letivos</span></button>
        <button type="button" class="cfg-tab" data-cfg="sistema"><i class="fas fa-cogs"></i><span>Sistema</span></button>
    </div>

    {{-- Dados da escola --}}
    <div class="cfg-panel active" data-cfg-panel="escola">
        <form method="POST" action="{{ route('configuracoes.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="grupo" value="escola">

            <div class="cfg-logo">
                @if($c('escola.logotipo'))
                <img src="{{ asset('storage/' . $c('escola.logotipo')) }}" alt="Logotipo atual" id="cfg-logo-preview">
                @else
                <img src="{{ asset('logo.png') }}" alt="Logotipo atual (padrão)" id="cfg-logo-preview">
                @endif
                <label class="file-pick" for="cfg-logotipo-input"><i class="fas fa-upload"></i> Carregar logotipo</label>
                <input type="file" name="logotipo" id="cfg-logotipo-input" style="display:none" accept="image/jpeg,image/png,image/webp">
            </div>

            <div class="form-group">
                <label class="form-label" for="c-nome">Nome da escola</label>
                <input type="text" id="c-nome" name="nome" class="form-input" value="{{ old('nome', $c('escola.nome')) }}" maxlength="150">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="c-contacto">Telefone</label>
                    <input type="text" id="c-contacto" name="contacto" class="form-input" value="{{ old('contacto', $c('escola.contacto')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="c-email">E-mail</label>
                    <input type="email" id="c-email" name="email" class="form-input" value="{{ old('email', $c('escola.email')) }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="c-endereco">Morada</label>
                <input type="text" id="c-endereco" name="endereco" class="form-input" value="{{ old('endereco', $c('escola.endereco')) }}">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="c-ano">Ano letivo em curso</label>
                    <input type="text" id="c-ano" name="ano_letivo" class="form-input" value="{{ old('ano_letivo', $c('escola.ano_letivo')) }}" placeholder="2026/2027">
                    <div class="cfg-hint">Ex.: 2026/2027 — aparece no cabeçalho do sistema.</div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="c-moeda">Moeda</label>
                    <input type="text" id="c-moeda" name="moeda" class="form-input" value="{{ old('moeda', $c('escola.moeda')) }}" placeholder="Xof">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="c-propina">Propina mensal indicativa</label>
                <input type="number" id="c-propina" name="propina_mensal" class="form-input" value="{{ old('propina_mensal', $c('escola.propina_mensal')) }}" step="0.01" min="0">
                <div class="cfg-hint">Valor por defeito sugerido nos formulários de pagamento.</div>
            </div>
            @error('logotipo')<div class="form-error">{{ $message }}</div>@enderror
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Guardar</button>
        </form>
    </div>

    {{-- Períodos letivos --}}
    <div class="cfg-panel" data-cfg-panel="periodo">
        <form method="POST" action="{{ route('configuracoes.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="grupo" value="periodo">

            <div class="cfg-hint" style="margin-bottom:16px"><i class="fas fa-info-circle"></i> Datas de matrículas e períodos de férias para o ano letivo atual.</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="p-mi">Início de matrículas</label>
                    <input type="date" id="p-mi" name="matriculas_inicio" class="form-input" value="{{ old('matriculas_inicio', $c('periodo.matriculas_inicio')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="p-mf">Fim de matrículas</label>
                    <input type="date" id="p-mf" name="matriculas_fim" class="form-input" value="{{ old('matriculas_fim', $c('periodo.matriculas_fim')) }}">
                </div>
            </div>
            <div class="cfg-hint" style="margin:8px 0 12px;font-weight:600">Férias de inverno</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="p-fi">Início</label>
                    <input type="date" id="p-fi" name="ferias_inverno_inicio" class="form-input" value="{{ old('ferias_inverno_inicio', $c('periodo.ferias_inverno_inicio')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="p-ff">Fim</label>
                    <input type="date" id="p-ff" name="ferias_inverno_fim" class="form-input" value="{{ old('ferias_inverno_fim', $c('periodo.ferias_inverno_fim')) }}">
                </div>
            </div>
            <div class="cfg-hint" style="margin:8px 0 12px;font-weight:600">Férias de verão</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="p-vi">Início</label>
                    <input type="date" id="p-vi" name="ferias_verao_inicio" class="form-input" value="{{ old('ferias_verao_inicio', $c('periodo.ferias_verao_inicio')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="p-vf">Fim</label>
                    <input type="date" id="p-vf" name="ferias_verao_fim" class="form-input" value="{{ old('ferias_verao_fim', $c('periodo.ferias_verao_fim')) }}">
                </div>
            </div>
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Guardar</button>
        </form>
    </div>

    {{-- Sistema --}}
    <div class="cfg-panel" data-cfg-panel="sistema">
        <form method="POST" action="{{ route('configuracoes.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="grupo" value="sistema">

            <div class="cfg-hint" style="margin-bottom:16px"><i class="fas fa-info-circle"></i> Regras de avisos, notificações e limites do sistema.</div>
            <div class="form-group">
                <label class="cfg-check">
                    <input type="checkbox" name="avisos_obrigar_leitura" value="1" @checked(Configuracao::booleano('sistema.avisos_obrigar_leitura'))>
                    <span>Obrigar leitura dos avisos para todos os destinatários</span>
                </label>
            </div>
            <div class="form-group">
                <label class="cfg-check">
                    <input type="checkbox" name="avisos_notificar_email" value="1" @checked(Configuracao::booleano('sistema.avisos_notificar_email'))>
                    <span>Enviar notificação por e-mail quando um aviso é publicado</span>
                </label>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="s-lembrete">Lembrete de pagamentos (dias antes)</label>
                    <input type="number" id="s-lembrete" name="pagamentos_lembrete_dias" class="form-input" value="{{ old('pagamentos_lembrete_dias', $c('sistema.pagamentos_lembrete_dias')) }}" min="0" max="90">
                </div>
                <div class="form-group">
                    <label class="form-label" for="s-turma">Limite de alunos por turma</label>
                    <input type="number" id="s-turma" name="turmas_limite_alunos" class="form-input" value="{{ old('turmas_limite_alunos', $c('sistema.turmas_limite_alunos')) }}" min="1" max="100">
                    <div class="cfg-hint">Usado como sugestão ao criar turmas.</div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="s-faltas">Faltas por mês para alertar os encarregados</label>
                <input type="number" id="s-faltas" name="presenca_limiar_faltas" class="form-input" value="{{ old('presenca_limiar_faltas', $c('sistema.presenca_limiar_faltas')) }}" min="1" max="30">
                <div class="cfg-hint">O encarregado recebe alerta quando um filho atinge este número de faltas num mês.</div>
            </div>
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Guardar</button>
        </form>
    </div>
</section>

<script>
    (function () {
        const tabs = document.querySelectorAll('.cfg-tab');
        const panels = document.querySelectorAll('.cfg-panel');
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(t => t.classList.remove('active'));
                panels.forEach(p => p.classList.remove('active'));
                tab.classList.add('active');
                const el = document.querySelector('.cfg-panel[data-cfg-panel="' + tab.dataset.cfg + '"]');
                if (el) el.classList.add('active');
            });
        });

        const logoInput = document.getElementById('cfg-logotipo-input');
        if (logoInput) {
            logoInput.addEventListener('change', function () {
                const f = this.files && this.files[0];
                if (!f) return;
                document.getElementById('cfg-logo-preview').src = URL.createObjectURL(f);
            });
        }
    })();
</script>
@endsection