<style>
    .filho-card{background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;overflow:hidden}
    .filho-header{display:flex;align-items:center;gap:16px;padding:20px 24px;border-bottom:1px solid var(--border-color);flex-wrap:wrap}
    .filho-avatar{width:52px;height:52px;border-radius:50%;background:var(--accent-yellow);color:#000;display:grid;place-items:center;font-weight:700;font-size:22px;flex-shrink:0}
    .filho-name{font-size:18px;font-weight:700;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
    .filho-meta{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:4px}
    .filho-switch{margin-left:auto}
    .filho-select{background:var(--bg-input,#151D19);border:1px solid var(--border-color);border-radius:6px;color:var(--text-primary);padding:9px 12px;font-size:13px;cursor:pointer;outline:none;min-width:220px}
    .filho-select:focus{border-color:var(--accent-green)}
    .filho-tabs{display:flex;gap:4px;padding:12px 24px;border-bottom:1px solid var(--border-color);background:rgba(255,255,255,.015);overflow-x:auto}
    .filho-tab{display:flex;align-items:center;gap:8px;padding:8px 16px;border-radius:6px;font-size:13px;font-weight:500;color:var(--text-secondary);text-decoration:none;transition:all .15s;white-space:nowrap}
    .filho-tab:hover{color:var(--text-primary);background:var(--bg-hover)}
    .filho-tab.active{background:rgba(34,197,94,.12);color:var(--accent-green)}
    .filho-tab i{font-size:12px}
    @media(max-width:768px){.filho-switch{margin-left:0;width:100%}.filho-select{width:100%}}
</style>

<div class="filho-card" style="margin-bottom:20px">
    <div class="filho-header">
        <div class="filho-avatar">{{ strtoupper(substr($aluno->name, 0, 1)) }}</div>
        <div style="flex:1;min-width:0">
            <div class="filho-name">
                {{ $aluno->name }}
                @if($aluno->is_active)
                <span class="tag" style="padding:3px 10px;border-radius:12px;font-size:10px;font-weight:600;background:rgba(34,197,94,.12);color:var(--accent-green)">Ativo</span>
                @endif
            </div>
            <div class="filho-meta">
                @if($aluno->turma)
                <span class="tag" style="padding:3px 10px;border-radius:12px;font-size:10px;font-weight:600;background:rgba(59,130,246,.12);color:#60A5FA">{{ $aluno->turma->nome_turma }}</span>
                <span class="tag" style="padding:3px 10px;border-radius:12px;font-size:10px;font-weight:600;background:rgba(139,92,246,.12);color:#A78BFA">{{ $aluno->turma->nivel }}</span>
                @endif
                @if($aluno->numero)
                <span style="font-size:12px;color:var(--text-secondary)">Nº {{ $aluno->numero }}</span>
                @endif
            </div>
        </div>
        @if($filhos->count() > 1)
        <div class="filho-switch">
            <select class="filho-select" onchange="if(this.value){window.location.href=this.value}">
                @foreach($filhos as $f)
                <option value="{{ route('encarregado.filhos.' . $secao, $f) }}" {{ $f->id === $aluno->id ? 'selected' : '' }}>{{ $f->name }}@if($f->turma) ({{ $f->turma->nome_turma }})@endif</option>
                @endforeach
            </select>
        </div>
        @endif
    </div>
    <div class="filho-tabs">
        <a href="{{ route('encarregado.filhos.notas', $aluno) }}" class="filho-tab {{ $secao === 'notas' ? 'active' : '' }}">
            <i class="fas fa-graduation-cap"></i> Notas
        </a>
        <a href="{{ route('encarregado.filhos.horario', $aluno) }}" class="filho-tab {{ $secao === 'horario' ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i> Horário
        </a>
        <a href="{{ route('encarregado.filhos.pagamentos', $aluno) }}" class="filho-tab {{ $secao === 'pagamentos' ? 'active' : '' }}">
            <i class="fas fa-money-check-alt"></i> Pagamentos
        </a>
        <a href="{{ route('encarregado.filhos.presencas', $aluno) }}" class="filho-tab {{ $secao === 'presencas' ? 'active' : '' }}">
            <i class="fas fa-user-check"></i> Faltas & Presenças
        </a>
        <a href="{{ route('dashboard') }}" class="filho-tab" style="margin-left:auto">
            <i class="fas fa-arrow-left"></i> Todos os filhos
        </a>
    </div>
</div>