<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Gestão Escolar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        :root {
            --bg-main: #0B0F0D;
            --bg-card: #121815;
            --bg-input: #151D19;
            --border-color: #1A2420;
            --text-primary: #ECEFED;
            --text-secondary: #8A9A92;
            --accent-green: #22C55E;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image: radial-gradient(circle at 20% 30%, rgba(34, 197, 94, 0.03) 0%, transparent 50%),
                              radial-gradient(circle at 80% 70%, rgba(34, 197, 94, 0.05) 0%, transparent 50%);
        }

        .login-container {
            max-width: 960px;
            width: 100%;
        }

        .login-layout {
            display: block;
        }

        .login-image {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .login-image img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .login-form-side {
            max-width: 440px;
            width: 100%;
            margin: 0 auto;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 8px 24px rgba(34, 197, 94, 0.2);
        }

        .logo-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .logo-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .form-label i {
            color: var(--accent-green);
            margin-right: 6px;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent-green);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }

        .form-input::placeholder {
            color: var(--text-secondary);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            cursor: pointer;
        }

        .checkbox-group label {
            font-size: 13px;
            color: var(--text-secondary);
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--accent-green);
            color: #000;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }

        .btn-login:hover {
            background: #1ea34e;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(34, 197, 94, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #FCA5A5;
        }

        .test-credentials {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--border-color);
        }

        .test-credentials-title {
            text-align: center;
            font-size: 11px;
            color: var(--text-secondary);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .credentials-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            font-size: 11px;
        }

        .credential-item {
            padding: 10px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            text-align: center;
        }

        .credential-item strong {
            display: block;
            color: var(--text-primary);
            margin-bottom: 2px;
        }

        .credential-item.full-width {
            grid-column: span 2;
        }

        .footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        @media (max-width: 768px) {
            .login-layout {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .login-image {
                max-height: 260px;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 24px;
            }

            .credentials-grid {
                grid-template-columns: 1fr;
            }

            .credential-item.full-width {
                grid-column: span 1;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-layout">
            <!-- Imagem -->
            <!--<div class="login-image">
                <img src="{{ asset('Image.jpeg') }}" alt="Escola">
            </div>-->

            <!-- Formulário -->
            <div class="login-form-side">
                <!-- Logo e Título -->
                <div class="logo-section">
                    <div class="logo-icon"><img src="{{ asset('logo.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;"></div>
                    <h1 class="logo-title">MiScool</h1>
                    <p class="logo-subtitle">Faça login para continuar</p>
                </div>

                <!-- Card de Login -->
                <div class="login-card">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Mensagem de Erro -->
                        @if($errors->any())
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                        @endif

                        <!-- Campo Usuário -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i>Usuário
                            </label>
                            <input
                                type="text"
                                name="username"
                                value="{{ old('username') }}"
                                required
                                autofocus
                                placeholder="Digite seu usuário"
                                class="form-input">
                        </div>

                        <!-- Campo Senha -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-lock"></i>Senha
                            </label>
                            <input
                                type="password"
                                name="password"
                                required
                                placeholder="Digite sua senha"
                                class="form-input">
                        </div>

                        <!-- Lembrar-me -->
                        <div class="checkbox-group">
                            <input
                                type="checkbox"
                                name="remember"
                                id="remember">
                            <label for="remember">Lembrar-me</label>
                        </div>

                        <!-- Botão Login -->
                        <button type="submit" class="btn-login">
                            <i class="fas fa-sign-in-alt" style="margin-right: 6px;"></i>Entrar
                        </button>
                    </form>

                    <!-- Informações de Teste -->
                    <div class="test-credentials">
                        <p class="test-credentials-title">👤 Credenciais de teste</p>
                        <div class="credentials-grid">
                            <div class="credential-item">
                                <strong>Admin</strong>
                                <div>admin / admin123</div>
                            </div>
                            <div class="credential-item">
                                <strong>Diretor</strong>
                                <div>diretor / diretor123</div>
                            </div>
                            <div class="credential-item">
                                <strong>Financeiro</strong>
                                <div>financeiro / financeiro123</div>
                            </div>
                            <div class="credential-item">
                                <strong>Professor</strong>
                                <div>prof.carlos / professor123</div>
                            </div>
<div class="credential-item">
                        <strong>Aluno</strong>
                        <div>pedro.almeida / aluno123</div>
                    </div>
                    <div class="credential-item">
                        <strong>Auxiliar</strong>
                        <div>auxiliar / auxiliar123</div>
                    </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <p class="footer">
                    © {{ date('Y') }} Sistema de Gestão Escolar
                </p>
            </div>
        </div>
    </div>
</body>
</html>
