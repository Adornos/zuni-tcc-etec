<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $role = '';
    public string $name = '';
    public string $email = '';
    public string $status = '';

    #[Computed]
    public function users()
    {
        return User::query()

            ->whereNotIn('role', [
                UserRole::STUDENT->value,
                UserRole::GUARDIAN->value,
            ])

            ->when($this->role, function ($query){
                $query->where(
                    'role',
                    'like',
                    $this->role
                );
            })

            ->when($this->name, function ($query) {
                $query->where(
                    'name',
                    'like',
                    '%' . $this->name . '%'
                );
            })

            ->when($this->email, function ($query) {
                $query->where(
                    'email',
                    'like',
                    '%' . $this->email . '%'
                );
            })

            ->when($this->status, function ($query) {
                $query->where(function ($query) {
                    $query
                        ->whereHas('teacherSheet', function ($query) {
                            $query->where('status', $this->status);
                        })
                        ->orWhereHas('coordinatorSheet', function ($query) {
                            $query->where('status', $this->status);
                        })
                        ->orWhereHas('directorSheet', function ($query) {
                            $query->where('status', $this->status);
                        });
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
                        Funcionário
                    </span>
                </label>

                <input
                    type="text"
                    wire:model.live.debounce.300ms="name"
                    placeholder="Nome do funcionário"
                    class="input input-bordered w-full"
                >

            </div>


            {{-- Tipo de funcionário --}}
            <div class="form-control">

                <label class="label">
                    <span class="label-text">
                        Tipo
                    </span>
                </label>

                <select
                    wire:model.live="role"
                    class="select select-bordered w-full"
                >
                    <option value="">Todos</option>
                    <option value="teacher">Professor</option>
                    <option value="coordinator">Coordenador</option>
                    <option value="director">Diretor</option>
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

    {{-- ADICIONAR FUNCIONÁRIO --}}
                <div
                class="
                    card
                    bg-base-100
                    border
                    border-base-200
                    shadow-sm
                    transition-shadow
                    hover:shadow-md
                    h-full
                "
            >

                <div class="card-body p-4 sm:p-5 md:p-6">

                    <div class="card-actions justify-center items-center h-full">

                        <a href="{{route('director.employee.register')}}" class="text-white bg-Csecondary rounded-full p-4">CADASTRAR

                            <span aria-hidden="true">
                                +
                            </span>
                        </a>

                    </div>

                </div>

            </div>

    {{-- LISTAGEM DE FUNCIONÁRIOS --}}


        @forelse ($this->users as $user)

            <div
                wire:key="user-{{ $user->user_id }}"
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
                        {{ $user->name }}
                    </h2>

                    <p class="text-xs sm:text-sm text-base-content/60">
                        {{ $user->role->label() }}
                    </p>

                    <div class="divider my-2"></div>

                    <div class="flex justify-between gap-3">
                        <span class="text-sm text-base-content/60">
                            Student ID
                        </span>

                        <span class="font-medium">
                            {{ $user->id }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-3">
                        <span class="text-sm text-base-content/60">
                            Status
                        </span>

                        <span class="font-medium">
                            {{ $user->roleSheet?->status }}
                        </span>
                    </div>

                    <div class="card-actions justify-end mt-4">

                        <a
                            href="{{ route(
                                'director.employee.show',
                                $user->id
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
                <a href="" class="text-white bg-Csecondary rounded-full p-4">CADASTRAR PROFESSOR</a>
            </div>

        @endforelse

    </div>

</div>