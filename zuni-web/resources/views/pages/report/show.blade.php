@php
    $isStudents = $report->target === \App\Enums\ReportTarget::STUDENTS;
    $backUrl = \Illuminate\Support\Facades\Route::has($prefix . '.report.index')
        ? route($prefix . '.report.index')
        : url()->previous();
@endphp

<x-dynamic-component :component="'panel.' . auth()->user()->role->value">

    <div class="col-span-4 row-span-4 space-y-4">

        <div class="card bg-base-100 shadow">
            <div class="card-body space-y-4">

                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h2 class="card-title text-2xl">{{ $report->title }}</h2>

                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="badge {{ $report->type->badgeClass() }}">{{ $report->type->label() }}</span>

                            {{-- Estado só é exibido a quem pode alterá-lo ou é autor/coordenador/diretor --}}
                            @if (! auth()->user()->isGuardian())
                                <span class="badge {{ $report->is_active ? 'badge-success' : 'badge-error' }}">
                                    {{ $report->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            @endif
                        </div>

                        <p class="text-sm text-base-content/60 mt-2">
                            Por {{ $report->author?->name ?? 'Autor removido' }}
                            · criado em {{ $report->created_at->format('d/m/Y H:i') }}
                            @if ($report->updated_at && $report->updated_at->ne($report->created_at))
                                · atualizado em {{ $report->updated_at->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    </div>

                    <x-report.actions :report="$report" />
                </div>

                <div class="divider my-0"></div>

                <div class="whitespace-pre-line leading-relaxed">{{ $report->content }}</div>

            </div>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body">

                <h3 class="card-title">{{ $isStudents ? 'Alunos' : 'Salas' }}</h3>

                <ul class="mt-2 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                    @forelse ($entities as $entity)
                        @php
                            if ($isStudents) {
                                $route = $prefix . '.student.show';
                                $url = \Illuminate\Support\Facades\Route::has($route) ? route($route, $entity->student_id) : null;
                                $label = $entity->user->name ?? 'Aluno';
                                $sub = $entity->classroom->name ?? 'Sem turma';
                            } else {
                                $route = $prefix . '.classroom.show';
                                $url = \Illuminate\Support\Facades\Route::has($route) ? route($route, $entity) : null;
                                $label = $entity->name;
                                $sub = null;
                            }
                        @endphp

                        <li class="rounded-box border border-base-300 p-3">
                            @if ($url)
                                <a href="{{ $url }}" class="font-medium link link-hover">{{ $label }}</a>
                            @else
                                <span class="font-medium">{{ $label }}</span>
                            @endif
                            @if ($sub)
                                <p class="text-xs text-base-content/60">{{ $sub }}</p>
                            @endif
                        </li>
                    @empty
                        <li class="text-sm text-base-content/60">Nenhuma atribuição visível.</li>
                    @endforelse
                </ul>

            </div>
        </div>

        <div>
            <a href="{{ $backUrl }}" class="btn btn-ghost">← Voltar</a>
        </div>

    </div>

</x-dynamic-component>
