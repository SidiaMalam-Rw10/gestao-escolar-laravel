@php
    $diasAbreviados = ['Segunda' => '2ª', 'Terça' => '3ª', 'Quarta' => '4ª', 'Quinta' => '5ª', 'Sexta' => '6ª', 'Sábado' => 'Sáb', 'Domingo' => 'Dom'];
    $colunasDias = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];
    if ($horarios->contains(fn ($a) => $a->dia_semana === 'Sábado')) { $colunasDias[] = 'Sábado'; }
    if ($horarios->contains(fn ($a) => $a->dia_semana === 'Domingo')) { $colunasDias[] = 'Domingo'; }

    $tempos = $horarios
        ->map(fn ($a) => $a->hora_inicio->format('H:i') . '|' . $a->hora_fim->format('H:i'))
        ->unique()
        ->sort()
        ->values();
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
        @foreach($tempos as $indice => $tempo)
        @php [$entrada, $saida] = explode('|', $tempo); @endphp
        <tr>
            <td>{{ $indice + 1 }}º</td>
            <td>{{ $entrada }}</td>
            <td>{{ $saida }}</td>
            @foreach($colunasDias as $dia)
            @php
                $celulas = $horarios->filter(fn ($a) => $a->dia_semana === $dia && $a->hora_inicio->format('H:i') === $entrada && $a->hora_fim->format('H:i') === $saida)->values();
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