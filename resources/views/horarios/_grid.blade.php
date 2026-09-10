@php
    $diasAbreviados = ['Segunda' => '2ª', 'Terça' => '3ª', 'Quarta' => '4ª', 'Quinta' => '5ª', 'Sexta' => '6ª', 'Sábado' => 'Sáb', 'Domingo' => 'Dom'];
    $colunasDias = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];
    if ($horarios->contains(fn ($a) => $a->dia_semana === 'Sábado')) { $colunasDias[] = 'Sábado'; }
    if ($horarios->contains(fn ($a) => $a->dia_semana === 'Domingo')) { $colunasDias[] = 'Domingo'; }

    $usarTempo = $horarios->contains(fn ($a) => $a->tempo !== null);
    if ($usarTempo) {
        $tempos = $horarios->pluck('tempo')->filter()->unique()->sort()->values();
        $linhas = $tempos->map(function ($numero) use ($horarios) {
            $aulas = $horarios->where('tempo', $numero);
            $primeira = $aulas->first();
            return [
                'numero' => $numero,
                'entrada' => $primeira?->hora_inicio->format('H:i'),
                'saida' => $primeira?->hora_fim->format('H:i'),
            ];
        });
    } else {
        $linhas = $horarios
            ->map(fn ($a) => ['numero' => null, 'entrada' => $a->hora_inicio->format('H:i'), 'saida' => $a->hora_fim->format('H:i')])
            ->unique(fn ($l) => $l['entrada'] . '|' . $l['saida'])
            ->sortBy(fn ($l) => $l['entrada'])
            ->values()
            ->map(fn ($l, $indice) => ['numero' => null, 'entrada' => $l['entrada'], 'saida' => $l['saida'], 'rotulo' => $indice + 1]);
    }
@endphp

<table>
    <thead>
        <tr>
            <th>Tempo</th>
            <th>Entrada</th>
            <th>Saída</th>
            @foreach($colunasDias as $dia)
                <th>{{ $diasAbreviados[$dia] ?? $dia }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($linhas as $linha)
        <tr>
            <td>{{ $linha['numero'] ? $linha['numero'] . 'º' : ($linha['rotulo'] ?? '') }}</td>
            <td>{{ $linha['entrada'] }}</td>
            <td>{{ $linha['saida'] }}</td>
            @foreach($colunasDias as $dia)
            @php
                $celulas = $usarTempo
                    ? $horarios->filter(fn ($a) => $a->dia_semana === $dia && $a->tempo === $linha['numero'])->values()
                    : $horarios->filter(fn ($a) => $a->dia_semana === $dia && $a->hora_inicio->format('H:i') === $linha['entrada'] && $a->hora_fim->format('H:i') === $linha['saida'])->values();
            @endphp
            <td>
                @foreach($celulas as $aula)
                    @if($modo === 'professor')
                    <div class="cell-main">{{ $aula->turma?->nome_turma ?? '—' }}</div>
                    @if($aula->disciplina)<div class="cell-sub">{{ $aula->disciplina }}</div>@endif
                    @else
                    <div class="cell-main">{{ $aula->disciplina }}</div>
                    @if($aula->professor)<div class="cell-sub">{{ $aula->professor->name }}</div>@endif
                    @endif
                @endforeach
            </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>