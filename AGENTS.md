<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>

<session-summary>
## Objective
- Implementar funcionalidades no sistema de gestão escolar Laravel: suporte a múltiplas funções por utilizador (ex.: diretor também professor), cargo PCTP com nível de diretor, e gestão de Encarregados de Educação com conta de login para acompanhar crianças (notas, presenças e avisos). Última parte concluída: CRUD de avisos + acesso/login do encarregado.

## Important Details
- Projeto: `C:\Users\Rowman Indjai Quade\Desktop\gestao-escolar-laravel`; PowerShell (`;` funciona, `&&` falha); driver MySQL confirmado.
- `users.role` é ENUM com: `admin, diretor, financeiro, professor, aluno, auxiliar, pctp, encarregado`.
- Multi-role: coluna `roles` (JSON) em users; `hasRole()`; `isDiretor()` retorna true também para `pctp`; scopes `scopeAlunos()`/`scopeProfessores()` usam closure com `orWhereJsonContains`.
- Ordem de rotas CRÍTICA: grupo admin (com rotas `*/create`) registado ANTES do grupo `can:consultar` (com `*/{id}`).
- Tinker `--execute` falha com parse errors — usar ficheiro temporário e `php artisan tinker arquivo.php`.
- Encarregados: tabela sem login + `users.encarregado_id`; nova coluna `encarregados.user_id` (FK nullable para users = conta de acesso).
- Avisos: tabela/migração já existente sem CRUD; `destinatario_tipo` enum `todos/alunos/professores/turma/individual`; `turma_id`/`destinatario_id`; pivot `aviso_user` para leitura por utilizador (`syncWithoutDetaching` com `lido`/`lido_em`).
- Presenças: agregados mensais (`presencas`, `faltas`, `justificadas`).
- Login usa `username`; `AuthController` não verifica `is_active`.
- DashboardController reescrito para dados por `hasRole()` (não switch) — corrigiu "Undefined variable $horarios".
- Usuários demo: `mario.silva` (diretor, roles=["professor"]), `enc.demo`/`enc123456` (encarregado), `admin/admin123`, `financeiro/financeiro123`, `prof.carlos/professor123`, `pedro.almeida/aluno123`, `auxiliar/auxiliar123`.
- Página de login lista credenciais de teste mas com nomes de utilizador desatualizados/incorretos (ex.: "diretor/diretor123" sem ser mario.silva) — discrepancy conhecido, não corrigida.

## Work State
### Completed
- Multi-role completo (migration `2026_09_04_224246_add_roles_to_users_table`, cast, hasRole, scopes com closure, gates por hasRole, controllers, views com checkboxes/tags).
- Fix dashboard `$horarios`.
- PCTP completo (enum, isPctp, isDiretor inclui pctp, gates `diretor`/`consultar`, dashboard, sidebar, forms, tags, filtro).
- CRUD Encarregados completo (`EncarregadoController`, rotas admin+consultar, views, select pesquisável, sidebar, link em aluno show).
- Acesso Encarregado completo: migrations rodadas; models (`User::perfilEncarregado`/`avisosLidos`/`isEncarregado`/`isPctp`; `Encarregado::user`; `Aviso::lidosPor`/`foiLidoPor`); `validarEncarregado`/`criarContaAcesso`/`atualizarContaAcesso` (destroy apaga user, cascade remove encarregado); seção "Acesso ao Sistema" nas views; DashboardController + dashboard encarregado (filhos, médias, presenças, avisos); `EncarregadoAvisoController@marcarLido` + rota `encarregado.avisos.lido`; gates `encarregado` e `gerir_avisos` (admin/diretor).
- CRUD Avisos: `AvisoController` (index/create/store/destroy), rotas `admin.avisos.*`, views `avisos/index` e `avisos/create`, link "Avisos" na sidebar (admin/diretor), secção sidebar encarregado.
- Acompanhamento do filho pelo encarregado: rotas `encarregado.filhos.{notas,horario,pagamentos,presencas}`; `EncarregadoController` com `validarFilho()` (403 se aluno não é seu filho→`perfilEncarregado->alunos`); vistas `encarregados/filho_{notas,horario,pagamentos,presencas}.blade.php` + parcial `_filho_header` (selector de filho + abas); sidebar com "Notas/Horário/Pagamentos do Filho" e "Faltas & Presenças"; presencas: agregados mensais `tipo=aluno`, taxa de assiduidade, alertas quando `faltas>=3` num mês. Verificado: php -l, view:cache, render tinker OK.
- Dashboard do encarregado = CENTRO DE ALERTAS (decluttered): só métricas (Filhos/Avisos não lidos/Faltas ano/Em dívida) + cartões "Alertas de Faltas" (faltas≥3/mês) e "Alertas de Pagamentos" (pendente/atrasado, ligam às páginas do filho) + secção de Avisos. DashboardController passa `{$filhos,$alertasFaltas,$alertasPagamentos,$totalFaltasAno,$dividaTotal,$avisos,$avisosNaoLidos}` (presencas/pagamentos carregados com `where ano=Y`). Verificado por render tinker.
- Users: role `encarregado` em validação store/update, opções create/edit, filtro e tags (`.tag-encarregado`) em index/show.
- Verificado: php -l OK, `view:cache`/`view:clear` OK, tinker (criar encarregado+conta, associar filho, avisos relevantes, marcar lido via pivot OK), render do dashboard do encarregado OK (contains "Filhos a acompanhar" e "Avisos para os seus filhos").

### Active
- (nenhum)

### Blocked
- (nenhum)

## Next Move
- (nenhum pendente) — possíveis próximos passos: corrigir credenciais na página de login; testar login HTTP real do encarregado (`php artisan serve`); secção em tempo real de avisos.

## Relevant Files
- `app/Http/Controllers/{EncarregadoController,EncarregadoAvisoController,AvisoController,DashboardController,UserController}.php`
- `routes/web.php` — grupos admin/consultar/`can:encarregado`/`can:gerir_avisos`.
- `app/Models/{User,Encarregado,Aviso}.php` — hasRole, scopes, perfilEncarregado, avisosLidos, isEncarregado, isPctp, lidosPor, foiLidoPor.
- `app/Providers/AppServiceProvider.php` — gates.
- `resources/views/encarregados/{index,create,edit,show}.blade.php`
- `resources/views/avisos/{index,create}.blade.php`
- `resources/views/dashboard.blade.php`
- `resources/views/layouts/app.blade.php`
- Migrations: `2026_09_04_224246_add_roles_to_users_table`, `2026_09_04_230245_add_pctp_to_users_role_column`, `2026_09_04_232955_add_encarregado_role_to_users_role_column`, `2026_09_04_232956_add_user_id_to_encarregados_table`, `2026_09_04_232957_create_aviso_user_table`.
</session-summary>
