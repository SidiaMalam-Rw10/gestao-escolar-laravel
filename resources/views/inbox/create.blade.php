@extends('layouts.app')

@section('title', 'Nova Mensagem')
@section('page-title', 'Nova Mensagem')

@section('content')
<style>
    .form-card{max-width:680px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:24px}
    .form-group{margin-bottom:18px}
    .form-label{display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:var(--text-secondary)}
    .form-input,.form-textarea,.form-select{width:100%;padding:10px 14px;background:var(--bg-input,var(--bg-card));border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s}
    .form-input:focus,.form-textarea:focus,.form-select:focus{outline:none;border-color:var(--accent-green)}
    .form-textarea{min-height:180px;resize:vertical}
    .form-error{color:#FCA5A5;font-size:11px;margin-top:4px}
    .form-hint{font-size:11px;color:var(--text-secondary);margin-top:5px}
    .destinatarios{max-height:280px;overflow-y:auto;border:1px solid var(--border-color);border-radius:8px;padding:6px}
    .dest-opcao{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:6px;cursor:pointer;transition:background .12s}
    .dest-opcao:hover{background:var(--bg-hover)}
    .dest-opcao input{accent-color:var(--accent-green);flex-shrink:0}
    .dest-avatar{width:28px;height:28px;border-radius:50%;background:var(--bg-hover);color:var(--accent-green);display:grid;place-items:center;font-size:11px;font-weight:700;flex-shrink:0}
    .dest-info{flex:1;min-width:0}
    .dest-nome{font-size:12px;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .dest-funcao{font-size:10px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.04em}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s;text-decoration:none}
    .btn-primary:hover{background:#1ea34e}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:10px 18px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .alert-danger{padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;border-radius:8px;margin-bottom:20px;font-size:13px}
</style>

@if($errors->any())
<div class="alert-danger">
    @foreach($errors->all() as $erro)<div>{{ $erro }}</div>@endforeach
</div>
@endif

<div class="form-card">
    <form method="POST" action="{{ route('inbox.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">Destinatários <span style="color:#FCA5A5">*</span></label>
            <div class="destinatarios">
                @forelse($destinatarios as $destinatario)
                <label class="dest-opcao">
                    <input type="checkbox" name="destinatarios[]" value="{{ $destinatario->id }}"
                           {{ (is_array(old('destinatarios')) && in_array($destinatario->id, old('destinatarios'))) || (int) $preselect === (int) $destinatario->id ? 'checked' : '' }}>
                    <span class="dest-avatar">{{ strtoupper(substr($destinatario->name, 0, 1)) }}</span>
                    <span class="dest-info">
                        <span class="dest-nome">{{ $destinatario->name }}</span>
                        <span class="dest-funcao">{{ $destinatario->role }}</span>
                    </span>
                </label>
                @empty
                <div style="color:var(--text-secondary);font-size:12px;padding:12px">Não há outros utilizadores ativos.</div>
                @endforelse
            </div>
            @error('destinatarios')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Assunto</label>
            <input type="text" name="assunto" value="{{ old('assunto', $assunto) }}" class="form-input" placeholder="Assunto da mensagem" required>
            @error('assunto')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Mensagem</label>
            <textarea name="corpo" class="form-textarea" placeholder="Escreva a mensagem..." required>{{ old('corpo') }}</textarea>
            @error('corpo')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div style="display:flex;gap:10px">
            <button type="submit" class="btn-primary"><i class="fas fa-paper-plane"></i> Enviar</button>
            <a href="{{ route('inbox.index') }}" class="btn"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </form>
</div>
@endsection