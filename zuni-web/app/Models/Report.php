<?php

namespace App\Models;

use App\Enums\ReportTarget;
use App\Enums\ReportType;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Report extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'target',
        'is_active',
        'author_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => ReportType::class,
            'target' => ReportTarget::class,
            'is_active' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function studentSheets(): BelongsToMany
    {
        return $this->belongsToMany(
            StudentSheet::class,
            'report_student_sheet',
            'report_id',
            'student_sheet_id'
        );
    }

    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(
            Classroom::class,
            'report_classroom',
            'report_id',
            'classroom_id'
        );
    }

    /* ----------------------------------------------------------------- */
    /* Scopes                                                             */
    /* ----------------------------------------------------------------- */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('reports.is_active', true);
    }

    /** Mais recentes primeiro, com desempate estável. */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('reports.created_at')->orderByDesc('reports.id');
    }

    public function scopeForStudentSheet(Builder $query, int $sheetId): Builder
    {
        return $query->whereHas('studentSheets', fn ($q) => $q->where('student_sheets.id', $sheetId));
    }

    public function scopeForClassroom(Builder $query, int $classroomId): Builder
    {
        return $query->whereHas('classrooms', fn ($q) => $q->where('classrooms.id', $classroomId));
    }

    /**
     * Fonte única da regra "quem pode ver o quê". A ReportPolicy::view usa este
     * mesmo escopo, então listagens e acesso direto por URL nunca divergem.
     *
     * - Coordenador: todos (ativos e inativos).
     * - Diretor: somente ativos.
     * - Professor: os que criou (ativos e inativos) + ativos ligados a alunos/salas
     *   das turmas pelas quais é responsável.
     * - Responsável: ativos ligados a algum aluno seu.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return match ($user->role) {
            UserRole::COORDINATOR => $query,

            UserRole::DIRECTOR => $query->where('reports.is_active', true),

            UserRole::TEACHER => $query->where(function (Builder $q) use ($user) {
                $classroomIds = $user->classrooms()->pluck('classrooms.id')->all();

                $q->where('reports.author_id', $user->id)
                    ->orWhere(function (Builder $q) use ($classroomIds) {
                        $q->where('reports.is_active', true)
                            ->where(function (Builder $q) use ($classroomIds) {
                                $q->whereHas('studentSheets', fn ($s) => $s->whereIn('student_sheets.classroom_id', $classroomIds))
                                    ->orWhereHas('classrooms', fn ($c) => $c->whereIn('classrooms.id', $classroomIds));
                            });
                    });
            }),

            UserRole::GUARDIAN => $query
                ->where('reports.is_active', true)
                ->whereHas('studentSheets', fn ($s) => $s->where('student_sheets.guardian_id', $user->id)),

            default => $query->whereRaw('1 = 0'),
        };
    }
}
