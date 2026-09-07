<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use App\Models\PresencaAlunoMarcacao;
use App\Models\Turma;
use Illuminate\Http\Request;

class ProfessorPresencaController extends Controller
{
    /**
     * Turmas em que o professor leciona (a partir do horário).
     */
    private function turmasDoProfessor(bool $withCountAlunos = false)
    {
        $query = auth()->user()->horariosComoProfessor()->with('turma');

        $turmas = $query->get()->pluck('turma')->filter()->unique('id')->sortBy('nome_turma')->values();

        if ($withCountAlunos) {
            $turmas = $turmas->map(function (Turma $turma) {
                $turma->alunos_count = $turma->alunos()->count();

                return $turma;
            });
        }

        return $turmas;
    }

    public function index(Request $request)
    {
        $turmas = $this->turmasDoProfessor();

        $turma = null;
        $alunos = collect();
        $marcacoes = collect();
        $mes = $request->integer('mes', now()->month);
        $ano = $request->integer('ano', now()->year);

        if ($request->filled('turma_id')) {
            $turma = $turmas->firstWhere('id', (int) $request->turma_id);

            if ($turma) {
                $alunos = $turma->alunos()->orderBy('name')->get();

                // Apenas as marcações feitas por este professor (disciplinas podem ser marcadas por vários professores)
                $marcacoes = PresencaAlunoMarcacao::where('turma_id', $turma->id)
                    ->where('professor_id', auth()->id())
                    ->where('mes', $mes)
                    ->where('ano', $ano)
                    ->get()
                    ->groupBy('aluno_id');
            }
        }

        $meses = [1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

        return view('professor.presencas', compact('turmas', 'turma', 'alunos', 'marcacoes', 'mes', 'ano', 'meses'));
    }

    public function marcar(Request $request)
    {
        $validated = $request->validate([
            'aluno_id' => 'required|exists:users,id',
            'turma_id' => 'required|exists:turmas,id',
            'estado' => 'required|in:presente,falta,justificada',
            'data' => 'required|date',
        ]);

        $professor = auth()->user();

        // Só permite marcar alunos de turmas onde o professor leciona
        $permitidaTurma = $this->turmasDoProfessor()->contains('id', (int) $validated['turma_id']);

        if (! $permitidaTurma) {
            return back()->with('error', 'Não tem permissão para marcar presenças nesta turma.');
        }

        $aluno = \App\Models\User::findOrFail($validated['aluno_id']);

        if ((int) $aluno->turma_id !== (int) $validated['turma_id']) {
            return back()->with('error', 'O aluno não pertence à turma selecionada.');
        }

        $data = \Illuminate\Support\Carbon::parse($validated['data']);

        // Cada professor tem a sua própria marcação por dia (disciplinas diferentes),
        // por isso a procura considera o professor que está a marcar.
        $marcacao = PresencaAlunoMarcacao::where('aluno_id', $aluno->id)
            ->where('professor_id', $professor->id)
            ->where('data', $data->toDateString())
            ->first();

        if ($marcacao) {
            $marcacao->update([
                'estado' => $validated['estado'],
                'hora' => now()->format('H:i:s'),
                'turma_id' => $validated['turma_id'],
            ]);
        } else {
            PresencaAlunoMarcacao::create([
                'aluno_id' => $aluno->id,
                'professor_id' => $professor->id,
                'turma_id' => $validated['turma_id'],
                'estado' => $validated['estado'],
                'data' => $data->toDateString(),
                'hora' => now()->format('H:i:s'),
                'mes' => $data->month,
                'ano' => $data->year,
            ]);
        }

        // Quando marca FALTA, notifica o encarregado de educação do aluno
        if ($validated['estado'] === 'falta') {
            $this->notificarEncarregado($aluno, $data);
        }

        $label = PresencaAlunoMarcacao::ESTADOS[$validated['estado']] ?? $validated['estado'];

        return back()->with('success', "{$label} registada para {$aluno->name} em {$data->format('d/m/Y')}.");
    }

    public function desfazer(Request $request)
    {
        $validated = $request->validate([
            'aluno_id' => 'required|exists:users,id',
            'data' => 'required|date',
        ]);

        $data = \Illuminate\Support\Carbon::parse($validated['data']);

        $marcacao = PresencaAlunoMarcacao::where('aluno_id', $validated['aluno_id'])
            ->where('professor_id', auth()->id())
            ->where('data', $data->toDateString())
            ->first();

        if (! $marcacao) {
            return back()->with('error', 'Não existe marcação para desfazer.');
        }

        $marcacao->delete();

        return back()->with('success', 'Marcação removida.');
    }

    /**
     * Cria um aviso para o encarregado de educação do aluno quando este falta.
     * A notificação destina-se APENAS ao(s) encarregado(s) vinculado(s) ao aluno,
     * nunca ao professor.
     */
    private function notificarEncarregado(\App\Models\User $aluno, \Illuminate\Support\Carbon $data): void
    {
        $encarregado = $aluno->encarregado;

        if (! $encarregado) {
            return;
        }

        $destinatarioId = $encarregado->user_id ?? $aluno->id;

        // Se ainda assim o destinatário fosse o próprio professor, não envia
        if ((int) $destinatarioId === (int) auth()->id()) {
            return;
        }

        $mensagem = $aluno->name . ' não está presente na sala de aulas neste momento'
            . ' (' . $data->format('d/m/Y') . ' às ' . now()->format('H:i') . ').';

        Aviso::create([
            'titulo' => 'Alerta de falta — ' . $aluno->name,
            'mensagem' => $mensagem,
            'remetente_id' => auth()->id(),
            'destinatario_tipo' => 'individual',
            'destinatario_id' => $destinatarioId,
        ]);
    }

    public function pdf(Request $request, \App\Models\User $aluno)
    {
        $mes = $request->filled('mes') ? (int) $request->query('mes') : null;
        $ano = $request->filled('ano') ? (int) $request->query('ano') : now()->year;

        $turmasDoProf = $this->turmasDoProfessor();

        $queryMarcacoes = PresencaAlunoMarcacao::where('aluno_id', $aluno->id)
            ->where('ano', $ano);

        if ($mes) {
            $queryMarcacoes->where('mes', $mes);
        }

        // Professores só veem as suas próprias marcações; gestão (admin/diretor) vê todas
        if (! auth()->user()->isAdmin() && ! auth()->user()->isDiretor()) {
            $queryMarcacoes->where('professor_id', auth()->id());
        }

        $marcacoes = $queryMarcacoes->with('professor')->orderBy('data')->orderBy('hora')->get();

        // Restringe a turmas onde o professor leciona (se o aluno lá estiver)
        $turmaRel = $turmasDoProf->first(fn ($t) => (int) $aluno->turma_id === (int) $t->id);

        if (auth()->user()->isAdmin() || auth()->user()->isDiretor()) {
            $turmaRel = $turmaRel ?: \App\Models\Turma::find($aluno->turma_id);
        } elseif (! $turmaRel) {
            abort(403, 'Não tem permissão para gerar o relatório deste aluno.');
        }

        $totalPresentes = $marcacoes->where('estado', 'presente')->count();
        $totalFaltas = $marcacoes->where('estado', 'falta')->count();
        $totalJustificadas = $marcacoes->where('estado', 'justificada')->count();

        $meses = [1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        $tituloPeriodo = $mes ? ($meses[$mes] . ' de ' . $ano) : ('Ano letivo ' . ($ano - 1) . '/' . $ano);

        $progenitor = auth()->user();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('professor.presenca_aluno_pdf', compact(
            'aluno', 'turmaRel', 'marcacoes',
            'totalPresentes', 'totalFaltas', 'totalJustificadas',
            'meses', 'mes', 'ano', 'tituloPeriodo', 'progenitor'
        ));

        $nomeFicheiro = 'presencas_' . str_replace(' ', '_', strtolower($aluno->name)) . '_' . ($mes ?? 'ANUAL') . '_' . $ano . '.pdf';

        return $pdf->download($nomeFicheiro);
    }
}