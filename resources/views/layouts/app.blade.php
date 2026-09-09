<!DOCTYPE html>
@php use App\Models\Configuracao; @endphp
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', Configuracao::obter('escola.nome', 'Sistema de Gestão Escolar'))</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        :root {
            --bg-main: #0A0E0D;
            --bg-sidebar: #0F1311;
            --bg-card: #141A17;
            --bg-hover: #1A2420;
            --border-color: #1E2823;
            --text-primary: #E8EBE9;
            --text-secondary: #7A8A82;
            --accent-green: #22C55E;
            --accent-yellow: #EAB308;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        body {
            background: var(--bg-main);
            color: var(--text-primary);
            display: flex;
            height: 100vh;
            overflow: hidden;
            font-size: 13px;
            position: relative;
        }

        [x-cloak] { display: none !important; }

        /* Sidebar */
        aside {
            width: 260px;
            background: var(--bg-sidebar);
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 50;
            flex-shrink: 0;
            border-right: 1px solid var(--border-color);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 20px 16px 20px;
            font-weight: 600;
            font-size: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .logo-icon {
            width: 24px;
            height: 24px;
            background: var(--accent-green);
            color: #000;
            border-radius: 4px;
            display: grid;
            place-items: center;
            font-weight: 900;
            font-size: 14px;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.3;
        }

        .logo-title {
            font-size: 14px;
            font-weight: 700;
        }

        .logo-subtitle {
            font-size: 11px;
            color: var(--text-secondary);
            font-weight: 400;
        }

        .search-box {
            margin: 16px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 8px 12px;
            color: var(--text-secondary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            font-size: 12px;
        }

        .search-box:hover {
            border-color: rgba(255, 255, 255, 0.15);
        }

        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 8px 0;
        }

        .nav-section {
            margin-bottom: 20px;
        }

        .nav-title {
            color: var(--text-secondary);
            font-size: 10px;
            text-transform: uppercase;
            padding: 12px 20px 8px 20px;
            font-weight: 600;
            letter-spacing: 0.8px;
        }

        .nav-item {
            padding: 8px 20px;
            color: var(--text-secondary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            transition: all 0.15s;
            position: relative;
        }

        .nav-item:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
        }

        .nav-item.active {
            background: var(--bg-card);
            color: var(--text-primary);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--accent-green);
        }

        .nav-item i {
            width: 16px;
            font-size: 14px;
        }

        /* Main Content */
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
            z-index: 0;
        }

        header {
            background: var(--bg-sidebar);
            border-bottom: 1px solid var(--border-color);
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .header-title {
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-link {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.15s;
        }

        .header-link:hover {
            background: var(--bg-card);
            color: var(--text-primary);
        }

        .header-link i {
            font-size: 12px;
        }

        .user-menu {
            position: relative;
        }

        .user-button {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 6px 10px;
            cursor: pointer;
            transition: all 0.15s;
        }

        .user-button:hover {
            border-color: rgba(255, 255, 255, 0.15);
        }

        .user-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--accent-green);
            color: #000;
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 11px;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .flash {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash-success {
            background: rgba(34, 197, 94, 0.12);
            border: 1px solid rgba(34, 197, 94, 0.4);
            color: #86EFAC;
        }

        .flash-error {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #FCA5A5;
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            min-width: 200px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            z-index: 100;
        }

        .dropdown-item {
            padding: 10px 16px;
            color: var(--text-primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.15s;
            font-size: 13px;
        }

        .dropdown-item:hover {
            background: var(--bg-hover);
        }

        .dropdown-item button {
            background: none;
            border: none;
            color: inherit;
            width: 100%;
            text-align: left;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Sino de avisos */
        .bell-button {
            background: none;
            border: none;
            cursor: pointer;
            font: inherit;
            position: relative;
        }

        .bell-badge {
            background: #EF4444;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            min-width: 16px;
            height: 16px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
        }

        .header-link .bell-badge {
            position: absolute;
            top: -4px;
            right: -6px;
            display: inline-flex;
        }

        .bell-dropdown {
            width: 360px;
            min-width: 360px;
            padding: 0;
            overflow: hidden;
        }

        .bell-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
            font-size: 13px;
            font-weight: 600;
        }

        .bell-header small {
            font-weight: 400;
            font-size: 11px;
            color: var(--text-secondary);
        }

        .bell-list {
            max-height: 380px;
            overflow-y: auto;
        }

        .bell-empty {
            padding: 32px 16px;
            text-align: center;
            color: var(--text-secondary);
            font-size: 12px;
        }

        .bell-item {
            border-bottom: 1px solid var(--border-color);
        }

        .bell-item:last-child {
            border-bottom: none;
        }

        .bell-item-main {
            width: 100%;
            background: none;
            border: none;
            color: inherit;
            text-align: left;
            cursor: pointer;
            display: flex;
            gap: 10px;
            padding: 12px 16px;
            font: inherit;
        }

        .bell-item-main:hover {
            background: var(--bg-hover);
        }

        .bell-item.unread .bell-item-main {
            background: rgba(34, 197, 94, 0.04);
        }

        .bell-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #EF4444;
            margin-top: 6px;
            flex-shrink: 0;
        }

        .bell-item.read .bell-dot {
            background: var(--border-color);
        }

        .bell-item-txt {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .bell-item-title {
            font-size: 13px;
            font-weight: 600;
        }

        .bell-item-sub {
            font-size: 11px;
            color: var(--text-secondary);
        }

        .content {
            flex: 1;
            overflow-y: auto;
            padding: 24px 32px;
            position: relative;
            z-index: 0;
            max-width: 100%;
        }

        /* Responsive */
        @media (max-width: 768px) {
            aside {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                z-index: 100;
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            aside.mobile-open {
                transform: translateX(0);
            }

            .mobile-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.7);
                z-index: 90;
            }

            .content {
                padding: 16px;
            }

            .header-link span {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div x-data="{ sidebarOpen: false, userMenuOpen: false }" style="display: flex; width: 100%; height: 100vh;">
        @auth
        <!-- Mobile Backdrop -->
        <div x-show="sidebarOpen"
             @click="sidebarOpen = false"
             class="mobile-backdrop"
             x-cloak></div>

        <!-- Sidebar -->
        <aside :class="{ 'mobile-open': sidebarOpen }">
            <div class="logo">
                <div class="logo-icon" style="background:{{ Configuracao::obter('plataforma.logotipo') ? 'transparent' : (Configuracao::obter('escola.logotipo') ? 'transparent' : 'var(--accent-green)') }}">
                    <img src="{{ Configuracao::obter('plataforma.logotipo') ? asset('storage/' . Configuracao::obter('plataforma.logotipo')) : (Configuracao::obter('escola.logotipo') ? asset('storage/' . Configuracao::obter('escola.logotipo')) : asset('logo.png')) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 4px;">
                </div>
                <div class="logo-text">
                    <div class="logo-title">{{ Configuracao::obter('escola.nome', 'MiScool') }}</div>
                    <div class="logo-subtitle">By RW-10</div>
                </div>
            </div>

            <div class="search-box">
                <span>Pesquisar</span>
                <small style="color: var(--text-secondary); font-size: 10px;">Ctrl K</small>
            </div>

            <div class="sidebar-content">
                <div class="nav-section">
                    <div class="nav-title">Visão geral</div>
                    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                @if(!auth()->user()->isProprietario())
                <div class="nav-section">
                    <div class="nav-title">Comunicação</div>
                    <a href="{{ route('avisos.historico') }}" class="nav-item {{ request()->routeIs('avisos.historico') ? 'active' : '' }}">
                        <i class="fas fa-bullhorn"></i>
                        <span>Histórico de Avisos</span>
                        @if($notifAvisosNaoLidos > 0)
                        <span class="bell-badge" style="margin-left: auto;">{{ $notifAvisosNaoLidos }}</span>
                        @endif
                    </a>
                </div>

                @can('consultar')
                <div class="nav-section">
                    <div class="nav-title">Gestão</div>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-user-cog"></i>
                        <span>Usuários</span>
                    </a>
                    @endif
                    <a href="{{ route('admin.alunos.index') }}" class="nav-item {{ request()->routeIs('admin.alunos.*') ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Alunos</span>
                    </a>
                    <a href="{{ route('admin.professores.index') }}" class="nav-item {{ request()->routeIs('admin.professores.*') ? 'active' : '' }}">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Professores</span>
                    </a>
                    <a href="{{ route('admin.turmas.index') }}" class="nav-item {{ request()->routeIs('admin.turmas.*') ? 'active' : '' }}">
                        <i class="fas fa-school"></i>
                        <span>Turmas</span>
                    </a>
                    <a href="{{ route('admin.departamentos.index') }}" class="nav-item {{ request()->routeIs('admin.departamentos.*') ? 'active' : '' }}">
                        <i class="fas fa-building"></i>
                        <span>Departamentos</span>
                    </a>
                    <a href="{{ route('admin.encarregados.index') }}" class="nav-item {{ request()->routeIs('admin.encarregados.*') ? 'active' : '' }}">
                        <i class="fas fa-user-tie"></i>
                        <span>Encarregados</span>
                    </a>
                </div>
                @endcan

                @if(auth()->user()->isAdmin())
                <div class="nav-section">
                    <div class="nav-title">Gestão de horários</div>
                    <a href="{{ route('admin.horarios.index') }}#turmas" class="nav-item {{ request()->routeIs('admin.horarios.turma*') ? 'active' : '' }}" title="Horários das turmas (visíveis aos alunos)">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Horário de Turmas</span>
                    </a>
                    <a href="{{ route('admin.horarios.index') }}#professores" class="nav-item {{ request()->routeIs('admin.horarios.professor*') ? 'active' : '' }}" title="Horários dos professores (visíveis ao próprio professor)">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Horário de Professores</span>
                    </a>
                </div>
                @endif

                @if(!auth()->user()->isAdmin() && (auth()->user()->isFinanceiro() || auth()->user()->isDiretor()))
                <div class="nav-section">
                    <div class="nav-title">
                        Gestão financeira
                        @if(!auth()->user()->isFinanceiro())
                        <span style="font-weight:400;color:var(--text-secondary);font-size:9px;text-transform:none;letter-spacing:0;margin-left:4px">(somente leitura)</span>
                        @endif
                    </div>
                    @if(auth()->user()->isFinanceiro())
                    <a href="{{ route('financeiro.pagamentos.index') }}" class="nav-item {{ request()->routeIs('financeiro.pagamentos.*') && !request()->routeIs('financeiro.pagamentos.create') && !request()->routeIs('financeiro.pagamentos.store') ? 'active' : '' }}">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Pagamentos</span>
                    </a>
                    @else
                    <a href="{{ route('financeiro.pagamentos.index') }}" class="nav-item {{ request()->routeIs('financeiro.pagamentos.index') ? 'active' : '' }}">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Pagamentos</span>
                    </a>
                    @endif
                    @if(auth()->user()->isFinanceiro())
                    <a href="{{ route('financeiro.relatorios') }}" class="nav-item {{ request()->routeIs('financeiro.relatorios') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i>
                        <span>Relatórios</span>
                    </a>
                    @else
                    <a href="{{ route('financeiro.relatorios') }}" class="nav-item {{ request()->routeIs('financeiro.relatorios') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i>
                        <span>Relatórios</span>
                    </a>
                    @endif
                </div>
                @endif

                @can('ver_salarios')
                <div class="nav-section">
                    <div class="nav-title">Recursos Humanos</div>
                    <a href="{{ route('diretor.salarios.index') }}" class="nav-item {{ request()->routeIs('diretor.salarios.*') ? 'active' : '' }}">
                        <i class="fas fa-money-check-alt"></i>
                        <span>Gestão de Salários</span>
                        @if(auth()->user()->isFinanceiro())
                        <span style="font-weight:400;color:var(--text-secondary);font-size:9px;text-transform:none;letter-spacing:0;margin-left:auto">(consulta)</span>
                        @endif
                    </a>
                </div>
                @endcan

                @if(auth()->user()->isAuxiliar())
                <div class="nav-section">
                    <div class="nav-title">Presenças</div>
                    <a href="{{ route('auxiliar.presencas.professores.index') }}" class="nav-item {{ request()->routeIs('auxiliar.presencas.professores.index') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Folha dos Professores</span>
                    </a>
                    <a href="{{ route('auxiliar.presencas.professores.historico') }}" class="nav-item {{ request()->routeIs('auxiliar.presencas.professores.historico') ? 'active' : '' }}">
                        <i class="fas fa-history"></i>
                        <span>Histórico de Faltas</span>
                    </a>
                    <a href="{{ route('auxiliar.presencas.alunos.index') }}" class="nav-item {{ request()->routeIs('auxiliar.presencas.alunos.*') ? 'active' : '' }}">
                        <i class="fas fa-user-check"></i>
                        <span>Presenças dos Alunos</span>
                    </a>
                </div>
                @endif

                @if(auth()->user()->isAluno())
                <div class="nav-section">
                    <div class="nav-title">Meus dados</div>
                    <a href="{{ route('aluno.minhas.notas') }}" class="nav-item {{ request()->routeIs('aluno.minhas.notas') ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Minhas Notas</span>
                    </a>
                    <a href="{{ route('aluno.minhas.horario') }}" class="nav-item {{ request()->routeIs('aluno.minhas.horario') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Meu Horário</span>
                    </a>
                    <a href="{{ route('aluno.minhas.pagamentos') }}" class="nav-item {{ request()->routeIs('aluno.minhas.pagamentos') ? 'active' : '' }}">
                        <i class="fas fa-money-check"></i>
                        <span>Meus Pagamentos</span>
                    </a>
                    <a href="{{ route('aluno.minhas.presencas') }}" class="nav-item {{ request()->routeIs('aluno.minhas.presencas') ? 'active' : '' }}">
                        <i class="fas fa-user-check"></i>
                        <span>Minhas Faltas & Presenças</span>
                    </a>
                </div>
                @endif

                @if(auth()->user()->isProfessor())
                <div class="nav-section">
                    <div class="nav-title">Meu trabalho</div>
                    <a href="{{ route('professor.meu-horario') }}" class="nav-item {{ request()->routeIs('professor.meu-horario') ? 'active' : '' }}">
                        <i class="fas fa-calendar"></i>
                        <span>Meu Horário</span>
                    </a>
                    <a href="{{ route('professor.minhas-turmas') }}" class="nav-item {{ request()->routeIs('professor.minhas-turmas') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Minhas Turmas</span>
                    </a>
                    <a href="{{ route('professor.presencas') }}" class="nav-item {{ request()->routeIs('professor.presencas') ? 'active' : '' }}">
                        <i class="fas fa-user-check"></i>
                        <span>Presenças</span>
                    </a>
                </div>
                @endif

                @if(auth()->user()->isEncarregado())
                @php $filhoSidebar = auth()->user()->perfilEncarregado?->alunos()->orderBy('name')->get()->first(); @endphp
                @if($filhoSidebar)
                <div class="nav-section">
                    <div class="nav-title">Acompanhar filho</div>
                    <a href="{{ route('encarregado.filhos.notas', $filhoSidebar) }}" class="nav-item {{ request()->routeIs('encarregado.filhos.notas') ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Notas do Filho</span>
                    </a>
                    <a href="{{ route('encarregado.filhos.horario', $filhoSidebar) }}" class="nav-item {{ request()->routeIs('encarregado.filhos.horario') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Horário do Filho</span>
                    </a>
                    <a href="{{ route('encarregado.filhos.pagamentos', $filhoSidebar) }}" class="nav-item {{ request()->routeIs('encarregado.filhos.pagamentos') ? 'active' : '' }}">
                        <i class="fas fa-money-check"></i>
                        <span>Pagamentos do Filho</span>
                    </a>
                    <a href="{{ route('encarregado.filhos.presencas', $filhoSidebar) }}" class="nav-item {{ request()->routeIs('encarregado.filhos.presencas') ? 'active' : '' }}">
                        <i class="fas fa-user-check"></i>
                        <span>Faltas & Presenças</span>
                    </a>
                </div>
                @endif
                @endif

                @if(auth()->user()->isAdmin())
                <div class="nav-section">
                    <div class="nav-title">Gestão de conteúdo</div>
                    <a href="{{ route('admin.paginas.index') }}" class="nav-item {{ request()->routeIs('admin.paginas.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i>
                        <span>Páginas da Escola</span>
                    </a>
                    <a href="{{ route('admin.avisos.index') }}" class="nav-item {{ request()->routeIs('admin.avisos.*') ? 'active' : '' }}">
                        <i class="fas fa-bullhorn"></i>
                        <span>Avisos</span>
                    </a>
                </div>
                @endif

                <div class="nav-section">
                    <div class="nav-title">Informações da escola</div>
                    <a href="{{ route('paginas.horario') }}" class="nav-item {{ request()->routeIs('paginas.horario') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i>
                        <span>Horário</span>
                    </a>
                    <a href="{{ route('paginas.atividades') }}" class="nav-item {{ request()->routeIs('paginas.atividades') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Atividades da Escola</span>
                    </a>
                    <a href="{{ route('paginas.sobre') }}" class="nav-item {{ request()->routeIs('paginas.sobre') ? 'active' : '' }}">
                        <i class="fas fa-school"></i>
                        <span>Sobre a Escola</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-title">Ajuda</div>
                    <a href="{{ route('feedbacks.criar', ['tipo' => 'feedback']) }}" class="nav-item {{ request()->routeIs('feedbacks.criar') && request('tipo') === 'feedback' ? 'active' : '' }}">
                        <i class="fas fa-comment-dots"></i>
                        <span>Feedback</span>
                    </a>
                    <a href="{{ route('feedbacks.criar', ['tipo' => 'problema']) }}" class="nav-item {{ request()->routeIs('feedbacks.criar') && request('tipo') === 'problema' ? 'active' : '' }}">
                        <i class="fas fa-bug"></i>
                        <span>Reportar problema</span>
                    </a>
                    <a href="{{ route('feedbacks.meus') }}" class="nav-item {{ request()->routeIs('feedbacks.meus') ? 'active' : '' }}">
                        <i class="fas fa-inbox"></i>
                        <span>Os meus envios</span>
                    </a>
                    @if(auth()->user()->isAdmin() || auth()->user()->isDiretor())
                    <a href="{{ route('admin.feedbacks.index') }}" class="nav-item {{ request()->routeIs('admin.feedbacks.*') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>
                        <span>Gerir feedbacks</span>
                    </a>
                    @endif
                </div>

                @can('gerir_configuracoes')
                <div class="nav-section">
                    <div class="nav-title">Sistema</div>
                    <a href="{{ route('configuracoes.index') }}" class="nav-item {{ request()->routeIs('configuracoes.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        <span>Configurações</span>
                    </a>
                </div>
                @endcan
                @endif

                @can('dono')
                <div class="nav-section">
                    <div class="nav-title">Painel MiScool</div>
                    <a href="{{ route('central.escolas.index') }}" class="nav-item {{ request()->routeIs('central.escolas.*') ? 'active' : '' }}">
                        <i class="fas fa-school"></i>
                        <span>Escolas registadas</span>
                    </a>
                    <a href="{{ route('central.escolas.create') }}" class="nav-item {{ request()->routeIs('central.escolas.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i>
                        <span>Adicionar escola</span>
                    </a>
                    <a href="{{ route('central.usuarios.index') }}" class="nav-item {{ request()->routeIs('central.usuarios.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Utilizadores da plataforma</span>
                    </a>
                    <a href="{{ route('central.configuracoes.index') }}" class="nav-item {{ request()->routeIs('central.configuracoes.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        <span>Configuração</span>
                    </a>
                </div>
                @endcan
            </div>
        </aside>

        <!-- Main Content -->
        <main>
            <header>
                <div class="header-title">
                    <button @click="sidebarOpen = !sidebarOpen"
                            style="display: none; background: none; border: none; color: var(--text-primary); cursor: pointer; font-size: 18px; margin-right: 12px;"
                            class="mobile-menu-btn">
                        <i class="fas fa-bars"></i>
                    </button>
                    <i class="fas fa-th-large" style="color: var(--text-secondary);"></i>
                    <span>@yield('page-title', 'Dashboard')</span>
                </div>

                <div class="header-right">
                    @if(Configuracao::obter('escola.ano_letivo'))
                    <span class="header-link" style="cursor: default" title="Ano letivo em curso">
                        <i class="far fa-calendar-alt"></i>
                        <span>{{ Configuracao::obter('escola.ano_letivo') }}</span>
                    </span>
                    @endif
                    <a href="{{ route('calendario.index') }}" class="header-link {{ request()->routeIs('calendario.*') ? 'active' : '' }}">
                        <i class="far fa-calendar"></i>
                        <span>Calendrier</span>
                    </a>
                    <a href="{{ route('ficheiros.index') }}" class="header-link {{ request()->routeIs('ficheiros.*') ? 'active' : '' }}">
                        <i class="far fa-folder"></i>
                        <span>Fichiers</span>
                    </a>
                    <a href="{{ route('inbox.index') }}" class="header-link {{ request()->routeIs('inbox.*') ? 'active' : '' }}" style="position:relative">
                        <i class="far fa-envelope"></i>
                        <span>Inbox</span>
                        @if($notifMensagensNaoLidas > 0)
                        <span class="bell-badge">{{ $notifMensagensNaoLidas }}</span>
                        @endif
                    </a>

                    <div style="width: 1px; height: 20px; background: var(--border-color);"></div>

                    <div class="bell-menu" x-data="{ open: false }">
                        <button @click="open = !open" class="header-link bell-button" title="Avisos">
                            <i class="far fa-bell"></i>
                            @if($notifAvisosNaoLidos > 0)
                            <span class="bell-badge">{{ $notifAvisosNaoLidos }}</span>
                            @endif
                        </button>

                        <div x-show="open"
                             @click.away="open = false"
                             x-cloak
                             class="dropdown-menu bell-dropdown">
                            <div class="bell-header">
                                <span><i class="far fa-bell" style="margin-right: 6px; color: var(--text-secondary);"></i>Avisos</span>
                                <small>{{ $notifAvisosNaoLidos }} não lido(s) · {{ $notifAvisos->count() }}</small>
                            </div>
                            <div class="bell-list">
                                @forelse($notifAvisos as $aviso)
                                @php $lido = $notifAvisosLidosIds->contains($aviso->id); @endphp
                                <form method="POST" action="{{ route('avisos.lido', $aviso) }}" class="bell-item {{ $lido ? 'read' : 'unread' }}">
                                    @csrf
                                    <button type="submit" class="bell-item-main">
                                        <span class="bell-dot"></span>
                                        <span class="bell-item-txt">
                                            <span class="bell-item-title">{{ $aviso->titulo }}</span>
                                            <span class="bell-item-sub">
                                                @if(!$lido)<i class="fas fa-circle" style="font-size: 6px; color: #EF4444; margin-right: 4px; vertical-align: middle;"></i>@endif
                                                {{ $aviso->remetente?->name ?? 'Remetente' }} · {{ $aviso->created_at?->diffForHumans() }}
                                            </span>
                                        </span>
                                    </button>
                                </form>
                                @empty
                                <div class="bell-empty">
                                    <i class="far fa-bell slashed" style="font-size: 24px; opacity: 0.4; margin-bottom: 10px; display: block;"></i>
                                    Sem avisos para si neste momento.
                                </div>
                                @endforelse
                            </div>
                            <div style="padding: 10px 16px; border-top: 1px solid var(--border-color);">
                                <a href="{{ route('avisos.historico') }}" class="dropdown-item" style="padding: 8px 0;">
                                    <i class="fas fa-bullhorn"></i>
                                    <span>Ver histórico de avisos</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="user-menu" x-data="{ open: false }">
                        <button @click="open = !open" class="user-button">
                            <div class="user-avatar">
                                @if(auth()->user()->fotoUrl())
                                <img src="{{ auth()->user()->fotoUrl() }}" alt="{{ auth()->user()->name }}">
                                @else
                                {{ auth()->user()->inicial() }}
                                @endif
                            </div>
                            <i class="fas fa-chevron-down" style="font-size: 10px; color: var(--text-secondary);"></i>
                        </button>

                        <div x-show="open"
                             @click.away="open = false"
                             x-cloak
                             class="dropdown-menu">
                            <a href="{{ route('perfil.index') }}" class="dropdown-item {{ request()->routeIs('perfil.*') ? 'active' : '' }}">
                                <i class="fas fa-user"></i>
                                <span>Perfil</span>
                            </a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.atividades.index') }}" class="dropdown-item {{ request()->routeIs('admin.atividades.*') ? 'active' : '' }}">
                                <i class="fas fa-history"></i>
                                <span>Atividades Recentes</span>
                            </a>
                            @endif
                            @can('gerir_configuracoes')
                            <a href="{{ route('configuracoes.index') }}" class="dropdown-item {{ request()->routeIs('configuracoes.*') ? 'active' : '' }}">
                                <i class="fas fa-cog"></i>
                                <span>Configurações</span>
                            </a>
                            @endcan
                            @can('dono')
                            <a href="{{ route('central.escolas.index') }}" class="dropdown-item {{ request()->routeIs('central.escolas.*') ? 'active' : '' }}">
                                <i class="fas fa-school"></i>
                                <span>Painel MiScool — Escolas</span>
                            </a>
                            @endcan
                            <div class="dropdown-item">
                                <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
                                    @csrf
                                    <button type="submit">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Sair</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
            </header>

            <div class="content">
                @if(session('success'))
                <div class="flash flash-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
                @endif
                @if(session('error'))
                <div class="flash flash-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
        @else
        <!-- Guest Content -->
        <main style="flex: 1;">
            @yield('content')
        </main>
        @endauth
    </div>

    <style>
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block !important;
            }
        }
    </style>
</body>
</html>
