<?php

use App\Models\StudentSheet;
use App\Enums\UserRole;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $name = '';
    public string $status = '';
    public string $class = '';

    /**
     * Verifica se o usuário pode pesquisar todos os alunos.
     */
    public function canSearchAllStudents(): bool
    {
        return in_array(auth()->user()->role, [
            UserRole::COORDINATOR,
            UserRole::DIRECTOR,
        ]);
    }

    #[Computed]
    public function students()
    {
        $query = StudentSheet::query()
            ->with('user');

        /*
         * COORDINATOR e DIRECTOR
         * podem visualizar e pesquisar todos os alunos.
         */
        if ($this->canSearchAllStudents()) {

            // Filtro por nome do aluno
            $query->when($this->name, function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where(
                        'name',
                        'like',
                        '%' . $this->name . '%'
                    );
                });
            });

            // Filtro por status do usuário
            $query->when($this->status, function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where(
                        'status',
                        $this->status
                    );
                });
            });

            // Filtro por turma
            $query->when($this->class, function ($query) {
                $query->where(
                    'class',
                    $this->class
                );
            });

        } else {

            /*
             * Usuários comuns:
             * somente alunos vinculados a eles como responsáveis.
             */
            $query->where(
                'guardian_id',
                auth()->id()
            );
        }

        return $query->get();
    }
};
?>

<div>

    {{-- FILTROS --}}
    @if ($this->canSearchAllStudents())

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
                lg:grid-cols-3
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
                        wire:model.live="class"
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

    @endif

    @if (auth()->user()->isGuardian())

    <div class="flex justify-end mb-6">

        <a
            href="{{ route('guardian.student.register') }}"
            class="btn bg-Csecondary text-white"
        >
            + Adicionar criança
        </a>

    </div>

    @endif



    {{-- RESULTADOS --}}
    <div class="flex flex-col gap-4">

    @if (auth()->user()->isGuardian())
        @forelse ($this->students as $student)

            <div
                wire:key="student-{{ $student->id }}"
                class="card bg-base-100"
            >
                <a href="{{route('coordinator.enrollment.show', ['enrollment' => $student->enrollment])}}">
                    <div
                        class="
                            card-body
                            flex
                            flex-col
                            md:flex-row
                            items-center
                            justify-between
                            gap-6
                            rounded-xl
                            shadow-[0_0_20px_rgba(0,0,0,0.15)]
                            p-5
                            sm:p-6
                        "
                    >
    
                        {{-- LADO ESQUERDO --}}
                        <div class="flex items-center gap-5 w-full">
    
                            {{-- Avatar --}}
                            <div class="avatar shrink-0">
    
                                <div class="w-16 sm:w-20 rounded-full">
    
                                    <img
                                        src="https://ui-avatars.com/api/?name={{ urlencode($student->user->name) }}&background=random"
                                        alt="Avatar de {{ $student->user->name }}"
                                    />
    
                                </div>
    
                            </div>
    
    
                            {{-- INFORMAÇÕES --}}
                            <div class="min-w-0">
    
                                <h2 class="
                                    text-xl
                                    sm:text-2xl
                                    font-bold
                                    truncate
                                ">
                                    {{ $student->user->name ?? 'Nome da criança' }}
                                </h2>
    
    
                                <p class="text-sm sm:text-base text-base-content/60">
    
                                    {{ $student->classroom_id ?? 'Turma não definida' }}
    
                                    @if (!empty($student->shift))
                                        • {{ $student->shift }}
                                    @endif
    
                                </p>
    
    
                                {{-- INFORMAÇÕES RÁPIDAS --}}
                                <div class="
                                    flex
                                    flex-wrap
                                    gap-2
                                    mt-2
                                ">
    
                                    <span class="badge text-white bg-Csecondary">
                                        Matrícula: {{ $student->student_id }}
                                    </span>
    
                                    <span class="badge text-white bg-Csecondary">
                                        {{ $student->age ?? '0' }} anos
                                    </span>
    
                                </div>
    
                            </div>
    
                        </div>
    
    
                        {{-- LADO DIREITO --}}
                        <div class="
                            text-center
                            md:text-right
                            w-full
                            md:w-auto
                            shrink-0
                        ">
    
                            <p class="text-sm text-base-content/60">
                                Situação
                            </p>
    
    
                            <p
                                @class([
                                    'text-lg font-semibold',
    
                                    'text-green-600' =>
                                        $student->user?->status?->value === 'active',
    
                                    'text-red-600' =>
                                        $student->user?->status?->value === 'inactive',
    
                                    'text-amber-500' =>
                                        !in_array(
                                            $student->user?->status?->value,
                                            ['active', 'inactive']
                                        ),
                                ])
                            >
                                {{ $student->user?->status?->label() ?? 'Não encontrado' }}
                            </p>
    
    
                            {{-- FREQUÊNCIA --}}
                            <div class="mt-2">
    
                                <span class="badge text-white bg-Csecondary">
                                    Frequência:
                                    {{ $student->attendance ?? 'Sem frequência' }}
                                </span>
    
                            </div>
    
                        </div>
    
                    </div>
                </a>

            </div>

        @empty

            <div class="text-center py-10">

                <p class="text-base-content/60">
                    Nenhum aluno encontrado.
                </p>

            </div>

        @endforelse

    @else 

    {{-- RESULTADOS --}} 
    <div
        class=" grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5 lg:gap-6 "> 
        @forelse ($this->students as $student) 
            <div 
            wire:key="student-{{ $student->id }}"
            class=" card bg-base-100 border border-base-200 shadow-sm transition-shadow hover:shadow-md h-fit "
            >
                <div class="card-body p-4 sm:p-5 md:p-6">
                    <h2 class="card-title text-base sm:text-lg"> {{ $student->user->name }} </h2>
                    <p class="text-xs sm:text-sm text-base-content/60"> Student #{{ $student->student_id }} </p>
                    <div class="divider my-2"></div>
                    <div class="flex justify-between gap-3">
                        <span class="text-sm text-base-content/60"> Student ID </span>
                        <span class="font-medium"> {{ $student->student_id }} </span> 
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-sm text-base-content/60"> Status </span>
                        <span class="font-medium"> {{ $student->user->status->value }} </span>
                    </div>
                    <div class="card-actions justify-end mt-4"> 
                        <a
                        href="{{ route( 'coordinator.enrollment.show', $student->id ) }}"
                        class=" btn btn-sm sm:btn-md text-white bg-Cprimary w-full sm:w-auto "
                        > 
                            Mais informações 
                            <span aria-hidden="true"> → </span> 
                        </a> 
                    </div>
                </div>
            </div> 
        @empty 
        <div class="col-span-full text-center py-10">
            <p class="text-base-content/60"> Nenhum aluno encontrado. </p>
        </div> 
        @endforelse 
    </div>
    
    @endif
    </div>

</div>