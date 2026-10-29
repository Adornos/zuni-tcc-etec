<?php

namespace App\Services\Chat;

use App\Models\Message;
use App\Models\Report;
use App\Models\StudentSheet;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * Links internos (relatórios e alunos). Nunca guarda URL: guarda tipo + id e
 * resolve a rota real, para o papel de quem abre, a cada acesso.
 */
class ChatLinkService
{
    public const TYPES = [Message::LINK_REPORT, Message::LINK_STUDENT];

    /**
     * Recursos que remetente E destinatário podem abrir (para o seletor).
     *
     * @return Collection<int, array{type:string,id:int,title:string,subtitle:string}>
     */
    public function candidates(User $sender, User $recipient, string $type, string $search = ''): Collection
    {
        $like = '%'.addcslashes($search, '%_\\').'%';

        if ($type === Message::LINK_REPORT) {
            return Report::query()
                ->visibleTo($sender)
                ->whereIn('reports.id', Report::query()->visibleTo($recipient)->select('reports.id'))
                ->with('author:id,name')
                ->when($search !== '', fn ($q) => $q->where('reports.title', 'like', $like))
                ->latestFirst()
                ->limit(8)
                ->get()
                ->map(fn (Report $r) => [
                    'type' => Message::LINK_REPORT,
                    'id' => $r->id,
                    'title' => $r->title,
                    'subtitle' => $r->type->label().' · '.($r->author?->name ?? '—'),
                ]);
        }

        if ($type === Message::LINK_STUDENT) {
            return StudentSheet::query()
                ->visibleTo($sender)
                ->whereIn('student_sheets.id', StudentSheet::query()->visibleTo($recipient)->select('student_sheets.id'))
                ->with(['user:id,name', 'classroom:id,name'])
                ->when($search !== '', fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', $like)))
                ->limit(8)
                ->get()
                ->map(fn (StudentSheet $s) => [
                    'type' => Message::LINK_STUDENT,
                    'id' => $s->id,
                    'title' => $s->user?->name ?? 'Aluno',
                    'subtitle' => $s->classroom?->name ?? 'Sem sala',
                ]);
        }

        return collect();
    }

    /**
     * Garante que o recurso existe e que remetente e destinatário podem abri-lo.
     *
     * @throws ValidationException
     */
    public function assertShareable(User $sender, User $recipient, string $type, int $id): void
    {
        $ok = in_array($type, self::TYPES, true)
            && $this->resolveFor($sender, $type, $id) !== null
            && $this->resolveFor($recipient, $type, $id) !== null;

        if (! $ok) {
            throw ValidationException::withMessages([
                'link' => 'Este recurso não existe ou não está disponível para os dois participantes.',
            ]);
        }
    }

    /**
     * Resumo e rota do recurso para quem está vendo; null se inexistente ou sem acesso.
     *
     * @return array{type:string,id:int,title:string,subtitle:string,route:string,params:array<string,mixed>}|null
     */
    public function resolveFor(User $viewer, ?string $type, ?int $id): ?array
    {
        if ($type === null || $id === null) {
            return null;
        }

        $prefix = $viewer->role->value;

        if ($type === Message::LINK_REPORT) {
            $report = Report::query()->visibleTo($viewer)->with('author:id,name')->whereKey($id)->first();

            return $report === null ? null : [
                'type' => $type,
                'id' => $report->id,
                'title' => $report->title,
                'subtitle' => 'Relatório · '.$report->type->label().' · '.($report->author?->name ?? '—'),
                'route' => $prefix.'.report.show',
                'params' => ['report' => $report->id],
            ];
        }

        if ($type === Message::LINK_STUDENT) {
            $sheet = StudentSheet::query()->visibleTo($viewer)->with(['user:id,name', 'classroom:id,name'])->whereKey($id)->first();

            return ($sheet === null || $sheet->user === null) ? null : [
                'type' => $type,
                'id' => $sheet->id,
                'title' => $sheet->user->name,
                'subtitle' => 'Aluno · '.($sheet->classroom?->name ?? 'Sem sala'),
                'route' => $prefix.'.student.show',
                'params' => ['student' => $sheet->user->id],
            ];
        }

        return null;
    }
}
