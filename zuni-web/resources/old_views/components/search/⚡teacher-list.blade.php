<?php

use App\Models\TeacherSheet;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $name = '';
    public string $ano = '';
    public string $status = '';
    public string $turma = '';

    #[Computed]
    public function teachers()
    {
        return TeacherSheet::query()
            ->with('user')

            ->when($this->name, function ($query) {
                $query->where(
                    'name',
                    'like',
                    '%' . $this->name . '%'
                );
            })

            ->when($this->status, function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where(
                        'status',
                        $this->status
                    );
                });
            })

            ->get();
    }
    
};
?>

<div>

    {{-- FILTROS --}}
    <div class="
        card
        bg-base-100
        shadow-xl
        p-4
        sm:p-5
        md:p-6
        mb-6
    ">

        <h2 class="
            card-title
            mb-4
            text-base
            sm:text-lg
            md:text-xl
        ">
            Filtros
        </h2>


        <div class="
            grid
            grid-cols-1
            sm:grid-cols-2
            lg:grid-cols-2
            gap-3
            sm:gap-4
        ">

            {{-- Nome --}}
            <div class="form-control">

                <label class="label">
                    <span class="label-text">
                        Professor
                    </span>
                </label>

                <input
                    type="text"
                    wire:model.live.debounce.300ms="name"
                    placeholder="Nome do professor"
                    class="input input-bordered w-full"
                >

            </div>


            {{-- Status --}}
            <div class="form-control">

                <label class="label">
                    <span class="label-text">
                        Status
                    </span>
                </label>

                <select
                    wire:model.live="status"
                    class="select select-bordered w-full"
                >
                    <option value="">Todos</option>
                    <option value="active">Ativo</option>
                    <option value="inactive">Inativo</option>
                    <option value="suspended">Suspenso</option>
                </select>

            </div>

        </div>

    </div>


    {{-- RESULTADOS --}}
    <div class="
        grid
        grid-cols-1
        sm:grid-cols-2
        lg:grid-cols-3
        xl:grid-cols-4
        gap-4
        sm:gap-5
        lg:gap-6
    ">

        @forelse ($this->teachers as $teacher)

            <div
                wire:key="teacher-{{ $teacher->teacher_id }}"
                class="
                    card
                    bg-base-100
                    border
                    border-base-200
                    shadow-sm
                    transition-shadow
                    hover:shadow-md
                    h-fit
                "
            >

                <div class="card-body p-4 sm:p-5 md:p-6">

                    <h2 class="card-title text-base sm:text-lg">
                        {{ $teacher->user?->name }}
                    </h2>

                    <p class="text-xs sm:text-sm text-base-content/60">
                        #{{ $teacher->teacher_id }}
                    </p>

                    <div class="divider my-2"></div>

                    <div class="flex justify-between gap-3">
                        <span class="text-sm text-base-content/60">
                            ID
                        </span>

                        <span class="font-medium">
                            {{ $teacher->teacher_id }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-3">
                        <span class="text-sm text-base-content/60">
                            Status
                        </span>

                        <span class="font-medium">
                            {{ $teacher->user->status->label() }}
                        </span>
                    </div>

                    <div class="card-actions justify-end mt-4">

                        <a
                            href="{{ route(
                                'coordinator.teacher.show',
                                $teacher->id
                            ) }}"
                            class="
                                btn
                                btn-sm
                                sm:btn-md
                                text-white
                                bg-Cprimary
                                w-full
                                sm:w-auto
                            "
                        >
                            Mais informações

                            <span aria-hidden="true">
                                →
                            </span>
                        </a>
                    </div>

                </div>

            </div>

        @empty

            <div class="col-span-full text-center py-10">
                <p class="text-base-content/60 mb-8">
                    Nenhuma matrícula encontrada.
                </p>
                <a href="{{ route('coordinator.teacher.register') }}" class="text-white bg-Csecondary rounded-full p-4">CADASTRAR PROFESSOR</a>
            </div>

        @endforelse

            <div class="col-span-full text-center py-10">
                <a href="{{ route('coordinator.teacher.register') }}" class="text-white bg-Csecondary rounded-full p-4">CADASTRAR PROFESSOR</a>
            </div>

    </div>

</div>