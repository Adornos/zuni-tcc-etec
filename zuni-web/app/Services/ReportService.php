<?php

namespace App\Services;

use App\Enums\ReportTarget;
use App\Models\Classroom;
use App\Models\Report;
use App\Models\StudentSheet;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReportService
{
    /**
     * Fichas de aluno sobre as quais o usuário pode escrever relatórios.
     * Coordenador: todas. Professor: alunos das turmas pelas quais é responsável.
     */
    public function studentSheetsFor(User $user): Builder
    {
        $query = StudentSheet::query();

        if ($user->isCoordinator()) {
            return $query;
        }

        if ($user->isTeacher()) {
            return $query->whereIn(
                'student_sheets.classroom_id',
                $user->classrooms()->pluck('classrooms.id')->all()
            );
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * Salas sobre as quais o usuário pode escrever relatórios.
     */
    public function classroomsFor(User $user): Builder
    {
        $query = Classroom::query();

        if ($user->isCoordinator()) {
            return $query;
        }

        if ($user->isTeacher()) {
            return $query->whereIn(
                'classrooms.id',
                $user->classrooms()->pluck('classrooms.id')->all()
            );
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * Confere que TODOS os ids existem e estão autorizados para o usuário.
     * Retorna a lista normalizada (inteiros, sem duplicatas, nunca vazia).
     *
     * @param  array<int, mixed>  $ids
     * @return array<int, int>
     *
     * @throws ValidationException
     */
    public function resolveAssignments(User $actor, ReportTarget $target, array $ids): array
    {
        $field = $target->value;

        $ids = array_values(array_unique(array_map('intval', $ids)));

        if ($ids === []) {
            throw ValidationException::withMessages([
                $field => 'Selecione ao menos '.($target === ReportTarget::STUDENTS ? 'um aluno.' : 'uma sala.'),
            ]);
        }

        $query = $target === ReportTarget::STUDENTS
            ? $this->studentSheetsFor($actor)
            : $this->classroomsFor($actor);

        $allowed = $query
            ->whereIn($query->getModel()->getQualifiedKeyName(), $ids)
            ->pluck($query->getModel()->getQualifiedKeyName())
            ->map(fn ($id) => (int) $id)
            ->all();

        if (count($allowed) !== count($ids)) {
            throw ValidationException::withMessages([
                $field => $target === ReportTarget::STUDENTS
                    ? 'Um ou mais alunos selecionados não existem ou não estão sob sua responsabilidade.'
                    : 'Uma ou mais salas selecionadas não existem ou não estão sob sua responsabilidade.',
            ]);
        }

        return $ids;
    }

    /**
     * @param  array{title:string, content:string, type:\App\Enums\ReportType, target:ReportTarget, ids:array<int,mixed>}  $data
     */
    public function create(User $author, array $data): Report
    {
        return DB::transaction(function () use ($author, $data) {
            $ids = $this->resolveAssignments($author, $data['target'], $data['ids']);

            // Autor e estado vêm sempre do servidor, nunca do formulário.
            $report = Report::create([
                'title' => $data['title'],
                'content' => $data['content'],
                'type' => $data['type'],
                'target' => $data['target'],
                'is_active' => true,
                'author_id' => $author->id,
            ]);

            $this->syncAssignments($report, $ids);

            return $report;
        });
    }

    /**
     * Atualiza conteúdo e atribuições. A categoria (alunos/salas) é imutável,
     * e todas as entidades finais precisam estar autorizadas para o ator.
     *
     * @param  array{title:string, content:string, type:\App\Enums\ReportType, target:ReportTarget, ids:array<int,mixed>}  $data
     */
    public function update(Report $report, User $actor, array $data): Report
    {
        return DB::transaction(function () use ($report, $actor, $data) {
            /** @var Report $report */
            $report = Report::query()->lockForUpdate()->findOrFail($report->getKey());

            if ($data['target'] !== $report->target) {
                throw ValidationException::withMessages([
                    'target' => 'Não é permitido mudar a categoria de atribuição do relatório.',
                ]);
            }

            $ids = $this->resolveAssignments($actor, $report->target, $data['ids']);

            $report->update([
                'title' => $data['title'],
                'content' => $data['content'],
                'type' => $data['type'],
            ]);

            $this->syncAssignments($report, $ids);

            return $report;
        });
    }

    public function delete(Report $report): void
    {
        DB::transaction(function () use ($report) {
            $report->studentSheets()->detach();
            $report->classrooms()->detach();
            $report->delete();
        });
    }

    /**
     * Altera apenas o estado; não toca em conteúdo, autor ou atribuições
     * e preserva updated_at (que marca a última edição de conteúdo).
     */
    public function setActive(Report $report, bool $active): Report
    {
        $report->timestamps = false;
        $report->is_active = $active;
        $report->save();
        $report->timestamps = true;

        return $report;
    }

    /**
     * @param  array<int, int>  $ids
     */
    private function syncAssignments(Report $report, array $ids): void
    {
        if ($report->target === ReportTarget::STUDENTS) {
            $report->classrooms()->detach();
            $report->studentSheets()->sync($ids);
        } else {
            $report->studentSheets()->detach();
            $report->classrooms()->sync($ids);
        }
    }
}
