<?php

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Classroom;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Enums\ClassroomGrade;
use App\Enums\UserRole;


new class extends Component
{
    // Visualização do coordenador
    public string $name = '';
    public string $grade = '';
    public string $status = '';
    public string $shift = '';
    public bool $canSearchClass = false;
    public string $routePrefix;

    public array $availableGrades;

    public function mount()
    {
        $this->availableGrades = ClassroomGrade::cases();

        $this->canSearchClass = Auth::user()->isCoordinator();

        $this->routePrefix = Auth::user()->role->value;
    }



    #[Computed]
    public function classrooms()
    {
        $query = Classroom::query();

        $query->with([
            'teachers',
            'students',
            'latestPerformance',
            'performances',
        ]);

        if($this->canSearchClass){

            $query->when($this->name, function ($query) {
                $query->where(
                    'name',
                    'like',
                    '%' . $this->name . '%'
                );
            });
    
            $query->when($this->grade, function ($query) {
                $query->where(
                    'grade',
                    $this->grade
                );
            });
    
            $query->when($this->status, function ($query) {
                $query->where(
                    'status',
                    $this->status
                );
            });
    
            $query->when($this->shift, function ($query) {
                $query->where(
                    'shift',
                    $this->shift
                );
            });
        } else {

            $query->whereHas('teachers', function ($query) {
                $query->where('id', Auth::id());
            });

        }

        return $query->paginate(32);
    }
}
?>

<div>


    {{-- FILTROS --}}
    @if($this->canSearchClass)
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
                            Turma
                        </span>
                    </label>

                    <input
                        type="text"
                        wire:model.live.debounce.300ms="name"
                        placeholder="Nome da turma"
                        class="input input-bordered w-full"
                    >

                </div>


                {{-- Série --}}
                <div class="form-control">

                    <label class="label">
                        <span class="label-text">
                            Série / Ano
                        </span>
                    </label>

                    <select
                        wire:model.live="grade"
                        class="select select-bordered w-full"
                    >
                        <option value="">Todos</option>
                        @foreach(ClassroomGrade::cases() as $grade)
                            <option value="{{$grade}}">{{$grade->label()}}</option>
                        @endforeach
                    </select>

                </div>


                {{-- Período --}}
                <div class="form-control">

                    <label class="label">
                        <span class="label-text">
                            Período
                        </span>
                    </label>

                    <select
                        wire:model.live="shift"
                        class="select select-bordered w-full"
                    >
                        <option value="">Todos</option>
                        <option value="morning">Manhã</option>
                        <option value="afternoon">Tarde</option>
                        <option value="full_time">Integral</option>
                        <option value="evening">Noite</option>
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
                        <option value="active">Ativa</option>
                        <option value="inactive">Inativa</option>
                    </select>

                </div>

            </div>

        </div>
    @endif()


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

        @forelse ($this->classrooms as $classroom)

            @php
                $latestPerformance = $classroom->performances->first();
            @endphp

            <div
                wire:key="classroom-{{ $classroom->id }}"
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

                    {{-- Nome --}}
                    <h2 class="card-title text-base sm:text-lg">
                        {{ $classroom->name }}
                    </h2>

                    <p class="text-xs sm:text-sm text-base-content/60">
                        #{{ str_pad($classroom->id, 4, '0', STR_PAD_LEFT) }}
                    </p>


                    <div class="divider my-2"></div>


                    {{-- Informações básicas --}}
                    <div class="space-y-2">

                        <div class="flex justify-between gap-3">
                            <span class="text-sm text-base-content/60">
                                Série
                            </span>

                            <span class="font-medium">
                                {{ $classroom->grade }}
                            </span>
                        </div>


                        <div class="flex justify-between gap-3">
                            <span class="text-sm text-base-content/60">
                                Período
                            </span>

                            <span class="font-medium">
                                {{ match($classroom->shift) {
                                    'morning' => 'Manhã',
                                    'afternoon' => 'Tarde',
                                    'full_time' => 'Integral',
                                    'evening' => 'Noite',
                                    default => 'Não informado',
                                } }}
                            </span>
                        </div>


                        <div class="flex justify-between gap-3">
                            <span class="text-sm text-base-content/60">
                                Professores
                            </span>

                            <span class="font-medium">
                                {{ $classroom->teachers->count() }}
                            </span>
                        </div>


                        <div class="flex justify-between gap-3">
                            <span class="text-sm text-base-content/60">
                                Capacidade
                            </span>

                            <span class="font-medium">
                                {{ $classroom->capacity ?? 'Não definida' }}
                            </span>
                        </div>


                        <div class="flex justify-between gap-3">
                            <span class="text-sm text-base-content/60">
                                Status
                            </span>

                            <span class="badge
                                {{ $classroom->status === 'active'
                                    ? 'badge-success'
                                    : 'badge-error'
                                }}"
                            >
                                {{ $classroom->status === 'active'
                                    ? 'Ativa'
                                    : 'Inativa'
                                }}
                            </span>
                        </div>

                    </div>


                    {{-- PERFORMANCE MAIS RECENTE --}}
                    @if($latestPerformance)

                        <div class="divider my-2"></div>

                        <div>

                            <p class="text-sm font-semibold mb-2">
                                Último desempenho
                            </p>

                            <div class="grid grid-cols-2 gap-2">

                                <div class="text-center">
                                    <p class="text-xs text-base-content/60">
                                        Média
                                    </p>

                                    <p class="font-bold">
                                        {{ $latestPerformance->average_grade ?? '—' }}
                                    </p>
                                </div>

                                <div class="text-center">
                                    <p class="text-xs text-base-content/60">
                                        Sociabilidade
                                    </p>

                                    <p class="font-bold">
                                        {{ $latestPerformance->sociability ?? '—' }}
                                    </p>
                                </div>

                                <div class="text-center">
                                    <p class="text-xs text-base-content/60">
                                        Autonomia
                                    </p>

                                    <p class="font-bold">
                                        {{ $latestPerformance->autonomy ?? '—' }}
                                    </p>
                                </div>

                                <div class="text-center">
                                    <p class="text-xs text-base-content/60">
                                        Engajamento
                                    </p>

                                    <p class="font-bold">
                                        {{ $latestPerformance->engagement ?? '—' }}
                                    </p>
                                </div>

                            </div>

                        </div>

                    @else

                        <div class="divider my-2"></div>

                        <p class="text-xs text-base-content/50">
                            Nenhum desempenho registrado.
                        </p>

                    @endif


                    {{-- AÇÕES --}}
                    <div class="card-actions justify-end mt-4">

                        <a
                            href="{{ route(
                                $this->routePrefix . '.classroom.show',
                                $classroom->id
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
            @if($this->canSearchClass)
                <div class="col-span-full text-center py-10">

                    <p class="text-base-content/60 mb-8">
                        Nenhuma turma encontrada.
                    </p>

                    <a
                        href="{{ route('coordinator.classroom.create') }}"
                        class="text-white bg-Csecondary rounded-full p-4"
                    >
                        CADASTRAR TURMA
                    </a>

                </div>
            @else
                <div class="col-span-full text-center py-10">

                    <p class="text-base-content/60 mb-8">
                        Nenhuma turma atribuída.
                    </p>
                </div>
            @endif

        @endforelse

        @if($this->canSearchClass)
        
        {{-- CADASTRAR --}}
            <div class="col-span-full text-center py-10">

                <a
                    href="{{ route('coordinator.classroom.create') }}"
                    class="text-white bg-Csecondary rounded-full p-4"
                >
                    CADASTRAR TURMA
                </a>

            </div>
        @endif

    </div>

</div>
