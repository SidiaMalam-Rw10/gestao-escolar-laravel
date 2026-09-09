<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background-color: #0B0F0D; margin: 0; padding: 24px; }
        .box { max-width: 480px; margin: 0 auto; background: #121815; border: 1px solid #1A2420; border-radius: 12px; padding: 28px; color: #ECEFED; }
        .logo { text-align: center; margin-bottom: 20px; }
        .logo img { width: 64px; height: 64px; border-radius: 50%; object-fit: contain; }
        .title { font-size: 18px; font-weight: 700; text-align: center; margin-bottom: 4px; }
        .subtitle { font-size: 12px; color: #8A9A92; text-align: center; margin-bottom: 20px; }
        .text { font-size: 13px; color: #B7C4BD; line-height: 1.6; }
        .button { display: block; text-align: center; margin: 22px 0; }
        .button a { display: inline-block; background: #22C55E; color: #000; font-size: 13px; font-weight: 700; padding: 12px 22px; border-radius: 8px; text-decoration: none; }
        .meta { font-size: 11px; color: #6B7C73; line-height: 1.6; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="box">
        <div class="logo"><img src="{{ \App\Models\Configuracao::logotipoPlataformaUrl() ?? asset('logo.png') }}" alt="Logo"></div>
        <div class="title">{{ \App\Models\Configuracao::nome() }}</div>
        <div class="subtitle">Reposição de palavra-passe</div>

        <p class="text">Olá, <strong>{{ $user->name }}</strong>.</p>
        <p class="text">Recebemos um pedido para repor a palavra-passe da sua conta. Clique no botão abaixo para definir uma nova palavra-passe:</p>

        <div class="button"><a href="{{ $url }}">Repor a minha palavra-passe</a></div>

        <p class="meta">Este link expira em {{ config('auth.passwords.users.expire', 60) }} minutos. Se não foi você que fez este pedido, pode ignorar este email — a sua palavra-passe não será alterada.</p>
    </div>
</body>
</html>