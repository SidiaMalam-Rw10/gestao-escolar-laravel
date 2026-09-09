<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir palavra-passe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        :root { --bg-main: #0B0F0D; --bg-card: #121815; --bg-input: #151D19; --border-color: #1A2420; --text-primary: #ECEFED; --text-secondary: #8A9A92; --accent-green: #22C55E; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        body { background-color: var(--bg-main); color: var(--text-primary); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; background-image: radial-gradient(circle at 20% 30%, rgba(34, 197, 94, 0.03) 0%, transparent 50%), radial-gradient(circle at 80% 70%, rgba(34, 197, 94, 0.05) 0%, transparent 50%); }
        .auth-card { width: 100%; max-width: 440px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 32px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); }
        .logo-section { text-align: center; margin-bottom: 20px; }
        .logo-icon { width: 70px; height: 70px; border-radius: 50%; overflow: hidden; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 14px; box-shadow: 0 8px 24px rgba(34, 197, 94, 0.2); }
        .logo-title { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .logo-subtitle { font-size: 13px; color: var(--text-secondary); margin-bottom: 6px; }
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
        .alert-warning { background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.3); color: #FCD34D; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="logo-section">
            <div class="logo-icon"><img src="{{ \App\Models\Configuracao::logotipoUrl() ?? \App\Models\Configuracao::logotipoPlataformaUrl() ?? asset('logo.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;"></div>
            <h1 class="logo-title">{{ \App\Models\Configuracao::nome() }}</h1>
            <p class="logo-subtitle">Bem-vindo(a)</p>
        </div>

        <div class="alert alert-warning">
            <i class="fas fa-shield-alt"></i>
            <span>No seu primeiro acesso terá de definir uma palavra-passe pessoal antes de continuar.</span>
        </div>

        @if($errors->any())
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i><span>{{ $errors->first() }}</span></div>
        @endif

        <form method="POST" action="{{ route('password.primeira.alterar') }}">
            @csrf

            <div class="form-group">
                <label class="form-label"><i class="fas fa-lock"></i>Palavra-passe atual</label>
                <input type="password" name="password_atual" required autofocus placeholder="A palavra-passe provisória que recebeu" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-lock"></i>Nova palavra-passe</label>
                <input type="password" name="password" required placeholder="Mínimo de 8 caracteres" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-lock"></i>Confirmar nova palavra-passe</label>
                <input type="password" name="password_confirmation" required placeholder="Repita a nova palavra-passe" class="form-input">
            </div>

            <button type="submit" class="btn-login"><i class="fas fa-key" style="margin-right: 6px;"></i>Definir palavra-passe</button>
        </form>
    </div>
</body>
</html>