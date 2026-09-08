<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gates de autorização por role
        \Illuminate\Support\Facades\Gate::define('admin', function ($user) {
            return $user->hasRole('admin');
        });

        \Illuminate\Support\Facades\Gate::define('diretor', function ($user) {
            return $user->hasRole('diretor') || $user->hasRole('pctp') || $user->hasRole('proprietario');
        });

        \Illuminate\Support\Facades\Gate::define('ver_salarios', function ($user) {
            return $user->isDiretor() || $user->isFinanceiro();
        });

        \Illuminate\Support\Facades\Gate::define('gerir_salarios', function ($user) {
            return $user->isDiretor();
        });

        \Illuminate\Support\Facades\Gate::define('ver_pagamentos', function ($user) {
            return $user->isFinanceiro() || $user->isAdmin() || $user->isDiretor();
        });

        \Illuminate\Support\Facades\Gate::define('financeiro', function ($user) {
            return $user->hasRole('financeiro');
        });

        \Illuminate\Support\Facades\Gate::define('professor', function ($user) {
            return $user->hasRole('professor');
        });

        \Illuminate\Support\Facades\Gate::define('aluno', function ($user) {
            return $user->hasRole('aluno');
        });

        \Illuminate\Support\Facades\Gate::define('auxiliar', function ($user) {
            return $user->hasRole('auxiliar');
        });

        \Illuminate\Support\Facades\Gate::define('presencas_professores', function ($user) {
            return $user->hasRole('auxiliar') || $user->isAdmin() || $user->isDiretor();
        });

        \Illuminate\Support\Facades\Gate::define('relatorio_presencas_alunos', function ($user) {
            return $user->hasRole('auxiliar') || $user->isAdmin() || $user->isDiretor();
        });

        \Illuminate\Support\Facades\Gate::define('encarregado', function ($user) {
            return $user->hasRole('encarregado');
        });

        \Illuminate\Support\Facades\Gate::define('gerir_avisos', function ($user) {
            return $user->isAdmin() || $user->isDiretor();
        });

        \Illuminate\Support\Facades\Gate::define('gerir_eventos', function ($user) {
            return $user->isAdmin() || $user->isDiretor();
        });

        \Illuminate\Support\Facades\Gate::define('gerir_ficheiros', function ($user) {
            return $user->isAdmin() || $user->isDiretor() || $user->isProfessor();
        });

        \Illuminate\Support\Facades\Gate::define('gerir_configuracoes', function ($user) {
            return $user->isAdmin() || $user->isDiretor();
        });

        \Illuminate\Support\Facades\Gate::define('dono', function ($user) {
            return $user->isProprietario();
        });

        \Illuminate\Support\Facades\Gate::define('consultar', function ($user) {
            return in_array($user->role, ['admin', 'diretor', 'financeiro', 'auxiliar', 'pctp', 'funcionario', 'proprietario'])
                || array_intersect($user->roles ?? [], ['admin', 'diretor', 'financeiro', 'auxiliar', 'pctp', 'funcionario', 'proprietario']);
        });

        // Sino de notificações de avisos (layout partilhado)
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $avisos = $user->avisosRelevantesQuery()
                    ->with('remetente')
                    ->latest()
                    ->limit(15)
                    ->get();

                $lidosIds = $user->avisosLidos()
                    ->whereIn('aviso_id', $avisos->pluck('id'))
                    ->pluck('aviso_id');

                $view->with('notifAvisos', $avisos)
                    ->with('notifAvisosLidosIds', $lidosIds)
                    ->with('notifAvisosNaoLidos', $avisos->whereNotIn('id', $lidosIds)->count())
                    ->with('notifMensagensNaoLidas', $user->mensagensNaoLidas()->count());
            }
        });
    }
}
