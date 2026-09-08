<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\Evento;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index(Request $request)
    {
        $ano = min(2100, max(2000, (int) $request->query('ano', now()->year)));
        $mes = min(12, max(1, (int) $request->query('mes', now()->month)));

        $referencia = Carbon::create($ano, $mes, 1);
        $inicio = $referencia->copy()->startOfDay();
        $fim = $referencia->copy()->endOfMonth()->endOfDay();

        $eventos = Evento::with('usuario')
            ->whereBetween('data_inicio', [$inicio, $fim])
            ->orderBy('data_inicio')
            ->get();

        $porDia = [];
        foreach ($eventos as $evento) {
            $dia = (int) $evento->data_inicio->day;
            $porDia[$dia][] = $evento;
        }

        // Períodos letivos (matrículas e férias) definidos nas Configurações
        $periodos = $this->periodosLetivosComoEventos();
        foreach ($periodos as $periodo) {
            $inicioPeriodo = $periodo->data_inicio->copy()->max($inicio);
            $fimPeriodo = $periodo->data_fim->copy()->min($fim);
            for ($d = $inicioPeriodo->copy(); $d->lte($fimPeriodo); $d->addDay()) {
                $porDia[(int) $d->day][] = $periodo;
            }
        }

        $diasNoMes = (int) $referencia->daysInMonth;
        $colunasAntes = (int) $referencia->dayOfWeek;

        $proximos = Evento::with('usuario')
            ->where('data_inicio', '>=', now())
            ->orderBy('data_inicio')
            ->limit(6)
            ->get();

        foreach ($periodos as $periodo) {
            if ($periodo->data_fim->gte(now())) {
                $proximos->push($periodo);
            }
        }
        $proximos = $proximos->sortBy('data_inicio')->take(6)->values();

        $podeGerir = auth()->user()->can('gerir_eventos');

        $meses = [1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'];
        $semanas = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        return view('eventos.index', compact(
            'ano', 'mes', 'referencia', 'porDia', 'diasNoMes', 'colunasAntes', 'proximos', 'podeGerir', 'meses', 'semanas'
        ));
    }

    public function create()
    {
        $evento = null;
        return view('eventos.create', compact('evento'));
    }

    /**
     * Converte o período letivo (matrículas e férias) das configurações em "eventos"
     * virtuais para exibição no calendário. Não são persistidos nem editáveis.
     */
    private function periodosLetivosComoEventos()
    {
        $configs = \App\Models\Configuracao::todas();

        $definicoes = [
            [
                'inicio' => $configs['periodo.matriculas_inicio'] ?? null,
                'fim' => $configs['periodo.matriculas_fim'] ?? null,
                'titulo' => 'Período de Matrículas',
                'tipo' => 'atividade',
            ],
            [
                'inicio' => $configs['periodo.ferias_inverno_inicio'] ?? null,
                'fim' => $configs['periodo.ferias_inverno_fim'] ?? null,
                'titulo' => 'Férias de Inverno',
                'tipo' => 'ferias',
            ],
            [
                'inicio' => $configs['periodo.ferias_verao_inicio'] ?? null,
                'fim' => $configs['periodo.ferias_verao_fim'] ?? null,
                'titulo' => 'Férias de Verão',
                'tipo' => 'ferias',
            ],
        ];

        $periodos = collect();

        foreach ($definicoes as $d) {
            if (empty($d['inicio'])) {
                continue;
            }

            $evento = new Evento([
                'titulo' => $d['titulo'],
                'tipo' => $d['tipo'],
                'data_inicio' => Carbon::parse($d['inicio'])->startOfDay(),
                'data_fim' => !empty($d['fim']) ? Carbon::parse($d['fim'])->endOfDay() : Carbon::parse($d['inicio'])->endOfDay(),
            ]);
            $evento->sistema = true;

            $periodos->push($evento);
        }

        return $periodos;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:150',
            'descricao' => 'nullable|string|max:5000',
            'tipo' => 'required|in:' . implode(',', array_keys(Evento::TIPOS)),
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'local' => 'nullable|string|max:120',
            'cor' => 'nullable|string|max:7',
        ]);

        $validated['user_id'] = auth()->id();
        $campoCor = $validated['cor'] ?? null;
        unset($validated['cor']);
        if ($campoCor && !str_starts_with($campoCor, '#')) {
            $campoCor = '#' . $campoCor;
        }
        $validated['cor'] = str_starts_with($campoCor ?? '', '#') ? $campoCor : null;

        $evento = Evento::create($validated);

        Atividade::registar('create', "Marcou o evento '{$evento->titulo}' no calendário ({$evento->data_inicio->format('d/m/Y')})", null, Evento::class, $evento->id, ['tipo' => $evento->tipo]);

        return redirect()->route('calendario.index', ['mes' => $evento->data_inicio->month, 'ano' => $evento->data_inicio->year])
            ->with('success', 'Evento marcado no calendário.');
    }

    public function edit(Evento $evento)
    {
        return view('eventos.create', compact('evento'));
    }

    public function update(Request $request, Evento $evento)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:150',
            'descricao' => 'nullable|string|max:5000',
            'tipo' => 'required|in:' . implode(',', array_keys(Evento::TIPOS)),
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'local' => 'nullable|string|max:120',
            'cor' => 'nullable|string|max:7',
        ]);

        $campoCor = $validated['cor'] ?? null;
        unset($validated['cor']);
        $validated['cor'] = str_starts_with($campoCor ?? '', '#') ? $campoCor : null;

        $evento->update($validated);

        Atividade::registar('update', "Atualizou o evento '{$evento->titulo}' no calendário", null, Evento::class, $evento->id, ['tipo' => $evento->tipo]);

        return redirect()->route('calendario.index', ['mes' => $evento->data_inicio->month, 'ano' => $evento->data_inicio->year])
            ->with('success', 'Evento atualizado com sucesso.');
    }

    public function destroy(Evento $evento)
    {
        $titulo = $evento->titulo;
        $evento->delete();

        Atividade::registar('delete', "Eliminou o evento '{$titulo}' do calendário", null, Evento::class, $evento->id);

        return back()->with('success', 'Evento eliminado.');
    }
}