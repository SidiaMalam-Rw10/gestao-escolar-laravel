@extends('layouts.app')

@section('title', 'Nova Escola')
@section('page-title', 'Painel MiScool — Nova Escola')

@section('content')
<style>
    .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
    .page-title{font-size:20px;font-weight:700}
    .btn{background:var(--bg-card);border:1px solid var(--border-color);color:var(--text-primary);padding:8px 14px;border-radius:6px;cursor:pointer;font-size:12px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:all .15s}
    .btn:hover{border-color:rgba(255,255,255,.15);background:var(--bg-hover)}
    .card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;margin-bottom:16px;max-width:680px}
    .card-header{padding:16px 20px;border-bottom:1px solid var(--border-color);font-size:14px;font-weight:600}
    .form-body{padding:20px}
    .form-group{margin-bottom:16px}
    .form-group label{display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:var(--text-primary)}
    .form-group .hint{font-size:11px;color:var(--text-secondary);margin-top:4px}
    .form-control{width:100%;padding:10px 14px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);font-size:13px;transition:border-color .15s}
    .form-control:focus{outline:none;border-color:var(--accent-green)}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    .btn-primary{background:var(--accent-green);color:#000;border:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s}
    .btn-primary:hover{background:#1ea34e}
    .alert-error{padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#FCA5A5;border-radius:8px;margin-bottom:16px;font-size:13px}
    .errors{border:1px solid rgba(239,68,68,.4);background:rgba(239,68,68,.08);border-radius:8px;padding:12px 16px;margin-bottom:20px}
    .errors ul{margin:0;padding-left:18px;color:#FCA5A5;font-size:12px}
    .errors li{margin:2px 0}
    @media(max-width:640px){.form-row{grid-template-columns:1fr}}
</style>

<div class="page-header">
    <div class="page-title">Criar nova Escola</div>
    <a href="{{ route('central.escolas.index') }}" class="btn"><i class="fas fa-arrow-left"></i> Voltar</a>
</div>

@if($errors->any())
    <div class="errors"><ul>@foreach($errors->all() as $erro)<li>{{ $erro }}</li>@endforeach</ul></div>
@endif

<form method="POST" action="{{ route('central.escolas.store') }}">
    @csrf
    <div class="card">
        <div class="card-header">Dados da escola</div>
        <div class="form-body">
            <div class="form-group">
                <label for="nome">Nome da escola *</label>
                <input type="text" id="nome" name="nome" class="form-control" value="{{ old('nome') }}" required maxlength="150">
                <div class="hint">Será criada uma base de dados própria a partir do nome (ex.: <span class="mono" style="font-family:monospace">miscool_&lt;nome&gt;</span>).</div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="contacto">Telefone</label>
                    <input type="text" id="contacto" name="contacto" class="form-control" value="{{ old('contacto') }}" maxlength="30">
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" maxlength="150">
                </div>
            </div>
            <div class="form-group">
                <label for="endereco">Endereço</label>
                <input type="text" id="endereco" name="endereco" class="form-control" value="{{ old('endereco') }}" maxlength="255">
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Conta de administrador da escola</div>
        <div class="form-body">
            <p style="font-size:12px;color:var(--text-secondary);margin:0 0 16px">Esta será a conta de acesso inicial da escola (sem subdomínio próprio). Depois de criada, a escola pode adicionar utilizadores e configurar o seu nome, moeda e logotipo.</p>
            <div class="form-group">
                <label for="admin_nome">Nome do administrador *</label>
                <input type="text" id="admin_nome" name="admin_nome" class="form-control" value="{{ old('admin_nome') }}" required maxlength="150">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="admin_username">Nome de utilizador (username) *</label>
                    <input type="text" id="admin_username" name="admin_username" class="form-control" value="{{ old('admin_username') }}" required maxlength="50">
                </div>
                <div class="form-group">
                    <label for="admin_password">Palavra-passe *</label>
                    <input type="password" id="admin_password" name="admin_password" class="form-control" required minlength="8">
                    <div class="hint">Mínimo de 8 caracteres.</div>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:12px">
        <button type="submit" class="btn-primary" style="padding:10px 24px"><i class="fas fa-check"></i> Criar escola e base de dados</button>
        <a href="{{ route('central.escolas.index') }}" class="btn">Cancelar</a>
    </div>
</form>
@endsection