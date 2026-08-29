<?php

use App\Models\Enrollment;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $name = '';
    public string $ano = '';
    public string $status = '';
    public string $turma = '';

    #[Computed]
    public function enrollments()
    {
        return Enrollment::query()
            ->with('studentSheet')

            ->when($this->name, function ($query) {
                $query->whereHas('studentSheet', function ($query) {
                    $query->where(
                        'name',
                        'like',
                        '%' . $this->name . '%'
                    );
                });
            })

            ->when($this->ano, function ($query) {
                $query->whereHas('studentSheet', function ($query) {
                    $query->where(
                        'class',
                        $this->ano
                    );
                });
            })

            ->when($this->status, function ($query) {
                $query->whereHas('studentSheet', function ($query) {
                    $query->where(
                        'status',
                        $this->status
                    );
                });
            })

            // ->when($this->turma, function ($query) {
            //     $query->whereHas('studentSheet', function ($query) {
            //         $query->where(
            //             'class',
            //             $this->turma
            //         );
            //     });
            // })

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
            lg:grid-cols-4
            gap-3
            sm:gap-4
        ">

            {{-- Nome --}}
            <div class="form-control">

                <label class="label">
                    <span class="label-text">
                        Aluno
                    </span>
                </label>

                <input
                    type="text"
                    wire:model.live.debounce.300ms="name"
                    placeholder="Nome do aluno"
                    class="input input-bordered w-full"
                >

            </div>


            {{-- Ano --}}
            <div class="form-control">

                <label class="label">
                    <span class="label-text">
                        Ano escolar
                    </span>
                </label>

                <select
                    wire:model.live="ano"
                    class="select select-bordered w-full"
                >
                    <option value="">Todos</option>
                    <option value="1-jardim">1º Jardim</option>
                    <option value="2-jardim">2º Jardim</option>
                    <option value="1-ano">1º Ano</option>
                    <option value="2-ano">2º Ano</option>
                    <option value="3-ano">3º Ano</option>
                    <option value="4-ano">4º Ano</option>
                    <option value="5-ano">5º Ano</option>
                    <option value="6-ano">6º Ano</option>
                    <option value="7-ano">7º Ano</option>
                    <option value="8-ano">8º Ano</option>
                    <option value="9-ano">9º Ano</option>
                </select>

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
                    <option value="pending">Pendente</option>
                    <option value="active">Ativo</option>
                    <option value="inactive">Inativo</option>
                    <option value="suspended">Suspenso</option>
                </select>

            </div>


            {{-- Turma --}}
            <div class="form-control">

                <label class="label">
                    <span class="label-text">
                        Turma
                    </span>
                </label>

                <select
                    wire:model.live="turma"
                    class="select select-bordered w-full"
                >
                    <option value="">Todas</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
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

        @forelse ($this->enrollments as $enrollment)

            <div
                wire:key="enrollment-{{ $enrollment->student_id }}"
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
                        {{ $enrollment->studentSheet->name }}
                    </h2>

                    <p class="text-xs sm:text-sm text-base-content/60">
                        Student #{{ $enrollment->student_id }}
                    </p>

                    <div class="divider my-2"></div>

                    <div class="flex justify-between gap-3">
                        <span class="text-sm text-base-content/60">
                            Student ID
                        </span>

                        <span class="font-medium">
                            {{ $enrollment->student_id }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-3">
                        <span class="text-sm text-base-content/60">
                            Status
                        </span>

                        <span class="font-medium">
                            {{ $enrollment->studentSheet->status }}
                        </span>
                    </div>

                    <div class="card-actions justify-end mt-4">

                        <a
                            href="{{ route(
                                'coordinator.enrollment.show',
                                $enrollment->student_id
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
                <p class="text-base-content/60">
                    Nenhuma matrícula encontrada.
                </p>
            </div>

        @endforelse

    </div>

</div>