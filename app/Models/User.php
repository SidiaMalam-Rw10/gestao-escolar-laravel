<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'numero',
    'name', 
    'username',
    'email', 
    'password',
    'role',
    'telefone',
    'endereco',
    'genero',
    'foto',
    'disciplina',
    'turma_id',
    'encarregado_id',
    'nivel',
    'ano_lectivo',
    'is_active',
    'roles'
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'roles' => 'array',
        ];
    }

    // Relacionamentos
    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function encarregado()
    {
        return $this->belongsTo(Encarregado::class);
    }

    public function perfilEncarregado()
    {
        return $this->hasOne(Encarregado::class, 'user_id');
    }

    public function avisosLidos()
    {
        return $this->belongsToMany(Aviso::class, 'aviso_user')->withPivot('lido', 'lido_em')->withTimestamps();
    }

    /**
     * Avisos relevantes para este utilizador (para o sino de notificações).
     */
    public function avisosRelevantesQuery()
    {
        return Aviso::where(function ($q) {
            $q->where('destinatario_tipo', 'todos');

            // Gestão (admin/diretor/pctp) vê todos os avisos
            if ($this->isAdmin() || $this->isDiretor()) {
                $q->orWhereIn('destinatario_tipo', ['alunos', 'professores', 'turma', 'individual']);
                return;
            }

            if ($this->isAluno()) {
                $q->orWhere('destinatario_tipo', 'alunos')
                  ->orWhere(fn ($q2) => $q2->where('destinatario_tipo', 'turma')->where('turma_id', $this->turma_id))
                  ->orWhere(fn ($q2) => $q2->where('destinatario_tipo', 'individual')->where('destinatario_id', $this->id));
            }

            if ($this->isProfessor()) {
                $turmaIds = $this->horariosComoProfessor()->pluck('turma_id');
                $q->orWhere('destinatario_tipo', 'professores')
                  ->orWhere(fn ($q2) => $q2->where('destinatario_tipo', 'turma')->whereIn('turma_id', $turmaIds))
                  ->orWhere(fn ($q2) => $q2->where('destinatario_tipo', 'individual')->where('destinatario_id', $this->id));
            }

            if ($this->isEncarregado()) {
                $filhos = $this->perfilEncarregado?->alunos()->get() ?? collect();
                $turmaIds = $filhos->pluck('turma_id')->filter()->values();
                $filhoIds = $filhos->pluck('id');
                $q->orWhere('destinatario_tipo', 'alunos')
                  ->orWhere(fn ($q2) => $q2->where('destinatario_tipo', 'turma')->whereIn('turma_id', $turmaIds))
                  ->orWhere(fn ($q2) => $q2->where('destinatario_tipo', 'individual')->whereIn('destinatario_id', $filhoIds))
                  ->orWhere(fn ($q2) => $q2->where('destinatario_tipo', 'individual')->where('destinatario_id', $this->id));
            }
        });
    }

    public function notas()
    {
        return $this->hasMany(Nota::class, 'aluno_id');
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'aluno_id');
    }

    public function presencas()
    {
        return $this->hasMany(Presenca::class);
    }

    public function horariosComoProfessor()
    {
        return $this->hasMany(Horario::class, 'professor_id');
    }

    public function turmasResponsavel()
    {
        return $this->hasMany(Turma::class, 'professor_responsavel_id');
    }

    public function departamentos()
    {
        return $this->belongsToMany(Departamento::class, 'user_departamento')
                    ->withPivot('cargo', 'regime', 'is_principal', 'ano_lectivo')
                    ->withTimestamps();
    }

    public function departamentoPrincipal()
    {
        return $this->belongsToMany(Departamento::class, 'user_departamento')
                    ->wherePivot('is_principal', true)
                    ->withPivot('cargo', 'regime', 'ano_lectivo')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeAlunos($query)
    {
        return $query->where(function ($q) {
            $q->where('role', 'aluno')->orWhereJsonContains('roles', 'aluno');
        });
    }

    public function scopeProfessores($query)
    {
        return $query->where(function ($q) {
            $q->where('role', 'professor')->orWhereJsonContains('roles', 'professor');
        });
    }

    public function scopeAtivos($query)
    {
        return $query->where('is_active', true);
    }

    // Helpers
    public function hasRole(string $role)
    {
        return $this->role === $role || in_array($role, $this->roles ?? []);
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isDiretor()
    {
        return $this->hasRole('diretor') || $this->hasRole('pctp');
    }

    public function isPctp()
    {
        return $this->hasRole('pctp');
    }

    public function isFinanceiro()
    {
        return $this->hasRole('financeiro');
    }

    public function isProfessor()
    {
        return $this->hasRole('professor');
    }

    public function isAluno()
    {
        return $this->hasRole('aluno');
    }

    public function isAuxiliar()
    {
        return $this->hasRole('auxiliar');
    }

    public function isEncarregado()
    {
        return $this->hasRole('encarregado');
    }
}
