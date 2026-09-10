<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar palavra-passe</title>
    @include('partials.pwa')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        :root { --bg-main: #0B0F0D; --bg-card: #121815; --bg-input: #151D19; --border-color: #1A2420; --text-primary: #ECEFED; --text-secondary: #8A9A92; --accent-green: #22C55E; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        body { background-color: var(--bg-main); color: var(--text-primary); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; background-image: radial-gradient(circle at 20% 30%, rgba(34, 197, 94, 0.03) 0%, transparent 50%), radial-gradient(circle at 80% 70%, rgba(34, 197, 94, 0.05) 0%, transparent 50%); }
        .auth-card { width: 100%; max-width: 440px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 32px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); }
        .logo-section { text-align: center; margin-bottom: 20px; }
        .logo-icon { width: 70px; height: 70px; border-radius: 50%; overflow: hidden; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 14px; box-shadow: 0 8px 24px rgba(34, 197, 94, 0.2); }
        .logo-title { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .logo-subtitle { font-size: 13px; color: var(--text-secondary); }
        .card-title { font-size: 13px; color: var(--text-secondary); margin-bottom: 20px; line-height: 1.5; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; }
        .form-label i { color: var(--accent-green); margin-right: 6px; }
        .form-input { width: 100%; padding: 12px 14px; background: var(--bg-input); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 14px; transition: all 0.2s; }
        .form-input:focus { outline: none; border-color: var(--accent-green); box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1); }
        .form-input::placeholder { color: var(--text-secondary); }
        .btn-login { width: 100%; padding: 14px; background: var(--accent-green); color: #000; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3); }
        .btn-login:hover { background: #1ea34e; transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); }
        .alert { padding: 12px 14px; border-radius: 8px; margin-bottom: 18px; display: flex; align-items: center; gap: 10px; font-size: 13px; }
        .alert-error { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #FCA5A5; }
        .alert-success { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); color: #86EFAC; }
        .back-link { display: block; text-align: center; margin-top: 20px; font-size: 13px; color: var(--text-secondary); text-decoration: none; }
        .back-link:hover { color: var(--accent-green); }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="logo-section">
            <div class="logo-icon"><img src="{{ \App\Models\Configuracao::logotipoPlataformaUrl() ?? \App\Models\Configuracao::logotipoUrl() ?? asset('logo.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;"></div>
            <h1 class="logo-title">{{ \App\Models\Configuracao::nome() }}</h1>
            <p class="logo-subtitle">Recuperar palavra-passe</p>
        </div>

        @if(session('status'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i><span>{{ session('status') }}</span></div>
        @endif

        @if($errors->any())
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i><span>{{ $errors->first() }}</span></div>
        @endif

        <p class="card-title">Indique o utilizador ou o email da sua conta e enviaremos um link para repor a palavra-passe.</p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label class="form-label"><i class="fas fa-user"></i>Utilizador ou email</label>
                <input type="text" name="identificador" value="{{ old('identificador') }}" required autofocus placeholder="Ex.: mario.silva ou email@escola.pt" class="form-input">
            </div>
            <button type="submit" class="btn-login"><i class="fas fa-paper-plane" style="margin-right: 6px;"></i>Enviar link de reposição</button>
        </form>

        <a href="{{ route('login') }}" class="back-link"><i class="fas fa-arrow-left"></i> Voltar ao login</a>
    </div>
</body>
</html>