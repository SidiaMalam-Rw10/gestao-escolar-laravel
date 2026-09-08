@extends('layouts.app')

@section('title', 'Marcar Evento')
@section('page-title', 'Marcar Evento no Calendário')

@section('content')
<style>
    .form-card{max-width:680px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:24px}
    .form-group{margin-bottom:18px}
    .form-label{display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:var(--text-secondary)}
    .form-input,.form-select,.form-textarea{width:100%;padding:10px 14px;background:var(--bg-input,var(--bg-card));border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s}
    .form-input:focus,.form-select:focus,.form-textarea:focus{outline:none;border-color:var(--accent-green)}
    .form-input::-webkit-calendar-picker-indicator{filter:invert(.6)}
    .form-textarea{min-height:110px;resize:vertical}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .form-hint{font-size:11px;color:var(--text-secondary);margin-top:5px}
    .form-error{color:#FCA5A5;font-size:11px;margin-top:4px}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .tipo-opcao{display:flex;align-items:center;gap:8px}
    .tipo-opcao .dot{width:10px;height:10px;border-radius:50%}
    @media(max-width:640px){.form-row{grid-template-columns:1fr}}
</style>

<div class="form-card">
    <form method="POST" action="{{ isset($evento) ? route('admin.eventos.update', $evento) : route('admin.eventos.store') }}">
        @csrf
        @if(isset($evento)) @method('PUT') @endif

        <div class="form-group">
            <label class="form-label">Título do evento</label>
            <input type="text" name="titulo" value="{{ old('titulo', $evento->titulo ?? '') }}" class="form-input @error('titulo') is-invalid @enderror" placeholder="Ex: Reunião de Pais e Encarregados" required>
            @error('titulo')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-select" id="tipo-select">
                @foreach(\App\Models\Evento::TIPOS as $tipo => $legenda)
                <option value="{{ $tipo }}" {{ old('tipo', $evento->tipo ?? 'atividade') === $tipo ? 'selected' : '' }}>{{ $legenda }}</option>
                @endforeach
            </select>
            @error('tipo')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Data e hora de início</label>
                <input type="datetime-local" name="data_inicio" value="{{ old('data_inicio', $evento?->data_inicio?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}" class="form-input" required>
                @error('data_inicio')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Data e hora de fim <span style="font-weight:400">(opcional)</span></label>
                <input type="datetime-local" name="data_fim" value="{{ old('data_fim', $evento?->data_fim?->format('Y-m-d\TH:i') ?? '') }}" class="form-input">
                <div class="form-hint">Só preencha para eventos com mais de um dia.</div>
                @error('data_fim')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Local <span style="font-weight:400">(opcional)</span></label>
                <input type="text" name="local" value="{{ old('local', $evento->local ?? '') }}" class="form-input" placeholder="Ex: Salão de reuniões">
                @error('local')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Cor <span style="font-weight:400">(opcional)</span></label>
                <input type="color" name="cor" value="{{ old('cor', $evento->cor ?? '#EAB308') }}" class="form-input" style="height:42px;padding:4px;cursor:pointer">
                <div class="form-hint">Se não escolher, usa a cor do tipo.</div>
                @error('cor')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Descrição <span style="font-weight:400">(opcional)</span></label>
            <textarea name="descricao" class="form-textarea" placeholder="Detalhes do evento...">{{ old('descricao', $evento->descricao ?? '') }}</textarea>
            @error('descricao')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div style="display:flex;gap:10px">
            <button type="submit" class="btn-primary"><i class="fas fa-check"></i> {{ isset($evento) ? 'Guardar alterações' : 'Marcar evento' }}</button>
            <a href="{{ route('calendario.index') }}" class="btn"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </form>
</div>
@endsection