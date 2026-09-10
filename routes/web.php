<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\TurmaController;
use App\Http\Controllers\EncarregadoController;
use App\Http\Controllers\EncarregadoAvisoController;
use App\Http\Controllers\AvisoController;
use App\Http\Controllers\PaginaController;
use App\Http\Controllers\AlunoAreaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ProfessorAreaController;
use App\Http\Controllers\ProfessorPresencaController;
use App\Http\Controllers\AuxiliarPresencaController;
use App\Http\Controllers\AuxiliarAlunoPresencaController;
use App\Http\Controllers\DiretorSalarioController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\AtividadeController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\FicheiroController;
use App\Http\Controllers\MensagemController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\EscolaController;
use App\Http\Controllers\CentralUserController;
use App\Http\Controllers\CentralConfiguracaoController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PrimeiraPasswordController;
use App\Http\Controllers\ManifestController;

// Redirecionamento da raiz para login
Route::get('/', function () {
    return redirect('/login');
});

// Manifest PWA (aplicação instalável)
Route::get('/manifest.webmanifest', [ManifestController::class, 'index']);

// Rotas de autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Recuperação de palavra-passe
    Route::get('/esquecer-password', [PasswordResetController::class, 'solicitar'])->name('password.request');
    Route::post('/esquecer-password', [PasswordResetController::class, 'enviarLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'repor'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'efetuarReposicao'])->name('password.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Marcar aviso como lido (sino de notificações - qualquer utilizador)
Route::post('/avisos/{aviso}/lido', [AvisoController::class, 'marcarLido'])->name('avisos.lido');

// Histórico de avisos (qualquer utilizador autenticado)
Route::get('/avisos', [AvisoController::class, 'historico'])->name('avisos.historico');

// Rotas protegidas
Route::middleware('auth')->group(function () {
    // Primeiro acesso: definir palavra-passe
    Route::get('/password/primeira-vez', [PrimeiraPasswordController::class, 'show'])->name('password.primeira');
    Route::post('/password/primeira-vez', [PrimeiraPasswordController::class, 'alterar'])->name('password.primeira.alterar');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Páginas informativas (Horário, Atividades, Sobre a Escola) - todos visualizam
    Route::get('/paginas/horario', [PaginaController::class, 'horario'])->name('paginas.horario');
    Route::get('/paginas/atividades', [PaginaController::class, 'atividades'])->name('paginas.atividades');
    Route::get('/paginas/sobre', [PaginaController::class, 'sobre'])->name('paginas.sobre');

    // Feedback & Reportar problema - todos os utilizadores autenticados
    Route::get('/feedback', [FeedbackController::class, 'criar'])->name('feedbacks.criar');
    Route::post('/feedback', [FeedbackController::class, 'guardar'])->name('feedbacks.guardar');
    Route::get('/feedback/meus', [FeedbackController::class, 'meus'])->name('feedbacks.meus');

    // Gestão de feedbacks - admin e diretor/PCTP
    Route::middleware('can:gerir_avisos')->prefix('admin')->name('admin.')->group(function () {
        Route::get('feedbacks', [FeedbackController::class, 'index'])->name('feedbacks.index');
        Route::put('feedbacks/{feedback}', [FeedbackController::class, 'atualizar'])->name('feedbacks.atualizar');
        Route::delete('feedbacks/{feedback}', [FeedbackController::class, 'destroy'])->name('feedbacks.destroy');
    });

    // Rotas Admin (escrita/gestão completa) - apenas admin
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        Route::get('alunos/create', [AlunoController::class, 'create'])->name('alunos.create');
        Route::post('alunos', [AlunoController::class, 'store'])->name('alunos.store');
        Route::get('alunos/{aluno}/edit', [AlunoController::class, 'edit'])->name('alunos.edit');
        Route::put('alunos/{aluno}', [AlunoController::class, 'update'])->name('alunos.update');
        Route::delete('alunos/{aluno}', [AlunoController::class, 'destroy'])->name('alunos.destroy');
        Route::patch('alunos/{aluno}/toggle-status', [AlunoController::class, 'toggleStatus'])->name('alunos.toggle-status');

        Route::get('professores/create', [ProfessorController::class, 'create'])->name('professores.create');
        Route::post('professores', [ProfessorController::class, 'store'])->name('professores.store');
        Route::get('professores/{professor}/edit', [ProfessorController::class, 'edit'])->name('professores.edit');
        Route::put('professores/{professor}', [ProfessorController::class, 'update'])->name('professores.update');
        Route::delete('professores/{professor}', [ProfessorController::class, 'destroy'])->name('professores.destroy');
        Route::patch('professores/{professor}/toggle-status', [ProfessorController::class, 'toggleStatus'])->name('professores.toggle-status');

        Route::get('turmas/create', [TurmaController::class, 'create'])->name('turmas.create');
        Route::post('turmas', [TurmaController::class, 'store'])->name('turmas.store');
        Route::get('turmas/{turma}/edit', [TurmaController::class, 'edit'])->name('turmas.edit');
        Route::put('turmas/{turma}', [TurmaController::class, 'update'])->name('turmas.update');
        Route::delete('turmas/{turma}', [TurmaController::class, 'destroy'])->name('turmas.destroy');

        Route::get('departamentos/create', [DepartamentoController::class, 'create'])->name('departamentos.create');
        Route::post('departamentos', [DepartamentoController::class, 'store'])->name('departamentos.store');
        Route::get('departamentos/{departamento}/edit', [DepartamentoController::class, 'edit'])->name('departamentos.edit');
        Route::put('departamentos/{departamento}', [DepartamentoController::class, 'update'])->name('departamentos.update');
        Route::delete('departamentos/{departamento}', [DepartamentoController::class, 'destroy'])->name('departamentos.destroy');
        Route::post('departamentos/{departamento}/membros', [DepartamentoController::class, 'adicionarMembro'])->name('departamentos.membros.adicionar');
        Route::put('departamentos/{departamento}/membros/{user}', [DepartamentoController::class, 'atualizarMembro'])->name('departamentos.membros.atualizar');
        Route::delete('departamentos/{departamento}/membros/{user}', [DepartamentoController::class, 'removerMembro'])->name('departamentos.membros.remover');

        Route::get('encarregados/create', [EncarregadoController::class, 'create'])->name('encarregados.create');
        Route::post('encarregados', [EncarregadoController::class, 'store'])->name('encarregados.store');
        Route::get('encarregados/{encarregado}/edit', [EncarregadoController::class, 'edit'])->name('encarregados.edit');
        Route::put('encarregados/{encarregado}', [EncarregadoController::class, 'update'])->name('encarregados.update');
        Route::delete('encarregados/{encarregado}', [EncarregadoController::class, 'destroy'])->name('encarregados.destroy');
        Route::post('encarregados/{encarregado}/associar-aluno', [EncarregadoController::class, 'associarAluno'])->name('encarregados.associar-aluno');
        Route::delete('encarregados/{encarregado}/associar-aluno/{aluno}', [EncarregadoController::class, 'desassociarAluno'])->name('encarregados.desassociar-aluno');

        Route::get('paginas', [PaginaController::class, 'index'])->name('paginas.index');
        Route::get('paginas/create', [PaginaController::class, 'create'])->name('paginas.create');
        Route::post('paginas', [PaginaController::class, 'store'])->name('paginas.store');
        Route::get('paginas/{pagina}/edit', [PaginaController::class, 'edit'])->name('paginas.edit');
        Route::put('paginas/{pagina}', [PaginaController::class, 'update'])->name('paginas.update');
        Route::delete('paginas/{pagina}', [PaginaController::class, 'destroy'])->name('paginas.destroy');

        // Gestão de Horários - admin
        Route::get('horarios', [HorarioController::class, 'index'])->name('horarios.index');
        Route::get('horarios/turma/{turma}', [HorarioController::class, 'turma'])->name('horarios.turma');
        Route::post('horarios/turma/{turma}', [HorarioController::class, 'turmaStore'])->name('horarios.turma.store');
        Route::get('horarios/professor/{professor}', [HorarioController::class, 'professor'])->name('horarios.professor');
        Route::post('horarios/professor/{professor}', [HorarioController::class, 'professorStore'])->name('horarios.professor.store');
        Route::delete('horarios/{horario}', [HorarioController::class, 'destroy'])->name('horarios.destroy');

        Route::get('atividades', [AtividadeController::class, 'index'])->name('atividades.index');
    });

    // Rotas de consulta (admin, diretor, financeiro, auxiliar) - apenas leitura
    Route::middleware('can:consultar')->prefix('admin')->name('admin.')->group(function () {
        Route::get('alunos', [AlunoController::class, 'index'])->name('alunos.index');
        Route::get('alunos/{aluno}', [AlunoController::class, 'show'])->name('alunos.show');

        Route::get('professores', [ProfessorController::class, 'index'])->name('professores.index');
        Route::get('professores/{professor}', [ProfessorController::class, 'show'])->name('professores.show');

        Route::get('turmas', [TurmaController::class, 'index'])->name('turmas.index');
        Route::get('turmas/{turma}', [TurmaController::class, 'show'])->name('turmas.show');

        Route::get('departamentos', [DepartamentoController::class, 'index'])->name('departamentos.index');
        Route::get('departamentos/{departamento}', [DepartamentoController::class, 'show'])->name('departamentos.show');
        Route::get('departamentos/{departamento}/membros', [DepartamentoController::class, 'gerirMembros'])->name('departamentos.membros');

        Route::get('encarregados', [EncarregadoController::class, 'index'])->name('encarregados.index');
        Route::get('encarregados/{encarregado}', [EncarregadoController::class, 'show'])->name('encarregados.show');
    });

    // Rotas Salários (Diretor/PCTP gerem; Financeiro vê, sem acesso ao formulário)
    Route::middleware('can:ver_salarios')->prefix('diretor')->name('diretor.')->group(function () {
        Route::get('salarios', [DiretorSalarioController::class, 'index'])->name('salarios.index');

        Route::middleware('can:gerir_salarios')->group(function () {
            Route::put('salarios/{professor}', [DiretorSalarioController::class, 'update'])->name('salarios.update');
        });
    });
    
    // Rotas Financeiro
    Route::middleware('can:financeiro')->prefix('financeiro')->name('financeiro.')->group(function () {
        Route::get('pagamentos/criar', [FinanceiroController::class, 'create'])->name('pagamentos.create');
        Route::post('pagamentos', [FinanceiroController::class, 'store'])->name('pagamentos.store');
    });

    // Pagamentos/Relatórios/Recibos - financeiro, admin e diretor (leitura)
    Route::middleware('can:ver_pagamentos')->prefix('financeiro')->name('financeiro.')->group(function () {
        Route::get('pagamentos', [FinanceiroController::class, 'index'])->name('pagamentos.index');
        Route::get('pagamentos/{pagamento}/recibo', [FinanceiroController::class, 'recibo'])->name('pagamentos.recibo');
        Route::get('pagamentos/{pagamento}/recibo/pdf', [FinanceiroController::class, 'reciboPdf'])->name('pagamentos.recibo.pdf');
        Route::get('relatorios', [FinanceiroController::class, 'relatorios'])->name('relatorios');
    });

    // Recibos acessíveis ao aluno/encarregado (financeiro/diretor/admin incluídos)
    Route::middleware('auth')->prefix('recibos')->name('recibos.')->group(function () {
        Route::get('{pagamento}', [FinanceiroController::class, 'recibo'])->name('show');
        Route::get('{pagamento}/pdf', [FinanceiroController::class, 'reciboPdf'])->name('pdf');
    });
    
    // Rotas Professor
    Route::middleware('can:professor')->prefix('professor')->name('professor.')->group(function () {
        Route::get('meu-horario', [ProfessorAreaController::class, 'meuHorario'])->name('meu-horario');
        Route::get('minhas-turmas', [ProfessorAreaController::class, 'minhasTurmas'])->name('minhas-turmas');
        Route::get('presencas', [ProfessorPresencaController::class, 'index'])->name('presencas');
        Route::post('presencas/marcar', [ProfessorPresencaController::class, 'marcar'])->name('presencas.marcar');
        Route::post('presencas/desfazer', [ProfessorPresencaController::class, 'desfazer'])->name('presencas.desfazer');
        Route::get('presencas/{aluno}/pdf', [ProfessorPresencaController::class, 'pdf'])->name('presencas.pdf');
    });
    
    // Rotas Aluno
    Route::middleware('can:aluno')->prefix('aluno')->name('aluno.')->group(function () {
        Route::get('minhas-notas', [AlunoAreaController::class, 'notas'])->name('minhas.notas');
        Route::get('meu-horario', [AlunoAreaController::class, 'horario'])->name('minhas.horario');
        Route::get('meus-pagamentos', [AlunoAreaController::class, 'pagamentos'])->name('minhas.pagamentos');
        Route::get('minhas-presencas', [AlunoAreaController::class, 'presencas'])->name('minhas.presencas');
    });

    // Rotas Encarregado de Educação
    Route::middleware('can:encarregado')->prefix('encarregado')->name('encarregado.')->group(function () {
        Route::post('avisos/{aviso}/lido', [EncarregadoAvisoController::class, 'marcarLido'])->name('avisos.lido');
        Route::get('filhos/{aluno}/notas', [EncarregadoController::class, 'filhoNotas'])->name('filhos.notas');
        Route::get('filhos/{aluno}/horario', [EncarregadoController::class, 'filhoHorario'])->name('filhos.horario');
        Route::get('filhos/{aluno}/pagamentos', [EncarregadoController::class, 'filhoPagamentos'])->name('filhos.pagamentos');
        Route::get('filhos/{aluno}/presencas', [EncarregadoController::class, 'filhoPresencas'])->name('filhos.presencas');
    });

    // Rotas Auxiliar - Gestão de presenças dos professores
    Route::middleware('can:presencas_professores')->prefix('auxiliar')->name('auxiliar.')->group(function () {
        Route::get('presencas/professores', [AuxiliarPresencaController::class, 'index'])->name('presencas.professores.index');
        Route::post('presencas/professores/marcar', [AuxiliarPresencaController::class, 'marcar'])->name('presencas.professores.marcar');
        Route::post('presencas/professores/desfazer', [AuxiliarPresencaController::class, 'desfazer'])->name('presencas.professores.desfazer');
        Route::get('presencas/professores/historico', [AuxiliarPresencaController::class, 'historico'])->name('presencas.professores.historico');
        Route::get('presencas/professores/{professor}/pdf', [AuxiliarPresencaController::class, 'pdf'])->name('presencas.professores.pdf');
    });

    // Rotas Auxiliar - Relatório de presenças/faltas dos alunos (apenas leitura)
    Route::middleware('can:relatorio_presencas_alunos')->prefix('auxiliar')->name('auxiliar.')->group(function () {
        Route::get('presencas/alunos', [AuxiliarAlunoPresencaController::class, 'index'])->name('presencas.alunos.index');
        Route::get('presencas/alunos/{turma}/pdf', [AuxiliarAlunoPresencaController::class, 'pdf'])->name('presencas.alunos.pdf');
    });

    // Rotas de avisos (admin e diretor/PCTP podem publicar)
    Route::middleware('can:gerir_avisos')->prefix('admin')->name('admin.')->group(function () {
        Route::get('avisos', [AvisoController::class, 'index'])->name('avisos.index');
        Route::get('avisos/create', [AvisoController::class, 'create'])->name('avisos.create');
        Route::post('avisos', [AvisoController::class, 'store'])->name('avisos.store');
        Route::delete('avisos/{aviso}', [AvisoController::class, 'destroy'])->name('avisos.destroy');
    });

    // Calendário de atividades - todos autenticados visualizam
    Route::get('/calendario', [EventoController::class, 'index'])->name('calendario.index');

    // Gestão de eventos do calendário (admin e diretor/PCTP)
    Route::middleware('can:gerir_eventos')->prefix('admin')->name('admin.')->group(function () {
        Route::get('eventos/create', [EventoController::class, 'create'])->name('eventos.create');
        Route::post('eventos', [EventoController::class, 'store'])->name('eventos.store');
        Route::get('eventos/{evento}/edit', [EventoController::class, 'edit'])->name('eventos.edit');
        Route::put('eventos/{evento}', [EventoController::class, 'update'])->name('eventos.update');
        Route::delete('eventos/{evento}', [EventoController::class, 'destroy'])->name('eventos.destroy');
    });

    // Biblioteca de ficheiros - todos autenticados visualizam e descarregam
    Route::get('/ficheiros', [FicheiroController::class, 'index'])->name('ficheiros.index');
    Route::get('/ficheiros/{ficheiro}/download', [FicheiroController::class, 'download'])->name('ficheiros.download');

    // Gestão de ficheiros (admin, diretor/PCTP e professor)
    Route::middleware('can:gerir_ficheiros')->prefix('admin')->name('admin.')->group(function () {
        Route::get('ficheiros/create', [FicheiroController::class, 'create'])->name('ficheiros.create');
        Route::post('ficheiros', [FicheiroController::class, 'store'])->name('ficheiros.store');
        Route::delete('ficheiros/{ficheiro}', [FicheiroController::class, 'destroy'])->name('ficheiros.destroy');
    });

    // Inbox - mensagens internas (todos os autenticados)
    Route::prefix('inbox')->name('inbox.')->group(function () {
        Route::get('/', [MensagemController::class, 'index'])->name('index');
        Route::get('/nova', [MensagemController::class, 'create'])->name('create');
        Route::post('/', [MensagemController::class, 'store'])->name('store');
        Route::get('/{mensagem}', [MensagemController::class, 'show'])->name('show');
        Route::delete('/{mensagem}', [MensagemController::class, 'destroy'])->name('destroy');
    });

    // Perfil do utilizador autenticado
    Route::prefix('perfil')->name('perfil.')->group(function () {
        Route::get('/', [PerfilController::class, 'index'])->name('index');
        Route::put('/', [PerfilController::class, 'update'])->name('update');
        Route::put('/password', [PerfilController::class, 'password'])->name('password');
        Route::delete('/foto', [PerfilController::class, 'removerFoto'])->name('foto.remover');
    });

    // Configurações da escola (admin e diretor/PCTP)
    Route::middleware('can:gerir_configuracoes')->prefix('configuracoes')->name('configuracoes.')->group(function () {
        Route::get('/', [ConfiguracaoController::class, 'index'])->name('index');
        Route::put('/', [ConfiguracaoController::class, 'update'])->name('update');
    });

    // Painel MiScool (dono do aplicativo)
    Route::middleware('can:dono')->prefix('central')->name('central.')->group(function () {
        Route::get('escolas', [EscolaController::class, 'index'])->name('escolas.index');
        Route::get('escolas/nova', [EscolaController::class, 'create'])->name('escolas.create');
        Route::post('escolas', [EscolaController::class, 'store'])->name('escolas.store');
        Route::get('escolas/{escola}/editar', [EscolaController::class, 'edit'])->name('escolas.edit');
        Route::put('escolas/{escola}', [EscolaController::class, 'update'])->name('escolas.update');
        Route::delete('escolas/{escola}', [EscolaController::class, 'destroy'])->name('escolas.destroy');

        Route::get('usuarios', [CentralUserController::class, 'index'])->name('usuarios.index');
        Route::get('usuarios/nova', [CentralUserController::class, 'create'])->name('usuarios.create');
        Route::post('usuarios', [CentralUserController::class, 'store'])->name('usuarios.store');
        Route::get('usuarios/{usuario}', [CentralUserController::class, 'show'])->name('usuarios.show');
        Route::get('usuarios/{usuario}/editar', [CentralUserController::class, 'edit'])->name('usuarios.edit');
        Route::put('usuarios/{usuario}', [CentralUserController::class, 'update'])->name('usuarios.update');
        Route::post('usuarios/{usuario}/toggle', [CentralUserController::class, 'toggleAtivo'])->name('usuarios.toggle');
        Route::delete('usuarios/{usuario}', [CentralUserController::class, 'destroy'])->name('usuarios.destroy');

        Route::get('configuracoes', [CentralConfiguracaoController::class, 'index'])->name('configuracoes.index');
        Route::put('configuracoes', [CentralConfiguracaoController::class, 'update'])->name('configuracoes.update');
    });
});

