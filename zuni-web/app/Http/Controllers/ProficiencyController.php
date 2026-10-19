<?php

namespace App\Http\Controllers;

use App\Models\Proficiency;
use App\Services\Proficiency\ProficiencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Cadastro de habilidades (coordenação). Nunca apaga habilidade, valor ou histórico:
 * inativar apenas impede novas observações.
 */
class ProficiencyController extends Controller
{
    public function __construct(private ProficiencyService $service)
    {
    }

    public function index()
    {
        Gate::authorize('viewAny', Proficiency::class);

        $proficiencies = Proficiency::query()
            ->withCount([
                'studentProficiencies',
                'observations as valid_observations_count' => fn ($q) => $q->valid(),
            ])
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(15);

        return view('pages.proficiency.index', ['proficiencies' => $proficiencies]);
    }

    public function create()
    {
        Gate::authorize('create', Proficiency::class);

        return view('pages.proficiency.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Proficiency::class);

        $validated = $request->validate($this->rules(), $this->messages());

        $proficiency = DB::transaction(function () use ($validated) {
            $proficiency = Proficiency::create([
                'name' => $validated['name'],
                'slug' => $this->uniqueSlug($validated['name']),
                'description' => $validated['description'] ?? null,
                'is_active' => true,
            ]);

            // Alunos elegíveis passam a ter a nova habilidade com valor neutro.
            $this->service->initializeProficiency($proficiency);

            return $proficiency;
        });

        return redirect()
            ->route('coordinator.proficiency.show', $proficiency)
            ->with('success', 'Habilidade cadastrada com sucesso!');
    }

    public function show(Proficiency $proficiency)
    {
        Gate::authorize('view', $proficiency);

        $proficiency->loadCount([
            'studentProficiencies',
            'observations',
            'observations as valid_observations_count' => fn ($q) => $q->valid(),
        ]);

        return view('pages.proficiency.show', ['proficiency' => $proficiency]);
    }

    public function edit(Proficiency $proficiency)
    {
        Gate::authorize('update', $proficiency);

        return view('pages.proficiency.edit', ['proficiency' => $proficiency]);
    }

    public function update(Request $request, Proficiency $proficiency)
    {
        Gate::authorize('update', $proficiency);

        $validated = $request->validate($this->rules($proficiency), $this->messages());

        // O slug é um identificador estável e não muda com a edição do nome.
        $proficiency->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('coordinator.proficiency.show', $proficiency)
            ->with('success', 'Habilidade atualizada com sucesso!');
    }

    public function status(Request $request, Proficiency $proficiency)
    {
        Gate::authorize('toggleActive', $proficiency);

        $validated = $request->validate(['is_active' => ['required', 'boolean']]);
        $active = (bool) $validated['is_active'];

        DB::transaction(function () use ($proficiency, $active) {
            $proficiency->update(['is_active' => $active]);

            if ($active) {
                $this->service->initializeProficiency($proficiency);
            }
        });

        return back()->with('success', $active ? 'Habilidade ativada.' : 'Habilidade inativada. O histórico foi preservado.');
    }

    private function rules(?Proficiency $proficiency = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('proficiencies', 'name')->ignore($proficiency?->id)],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function messages(): array
    {
        return [
            'name.required' => 'Informe o nome da habilidade.',
            'name.unique' => 'Já existe uma habilidade com este nome.',
        ];
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'habilidade';
        $slug = $base;
        $i = 2;

        while (Proficiency::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
