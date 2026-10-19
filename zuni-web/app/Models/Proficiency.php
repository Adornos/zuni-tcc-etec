<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Habilidade cadastrável. Inativar NÃO apaga valores nem histórico.
 */
class Proficiency extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function studentProficiencies(): HasMany
    {
        return $this->hasMany(StudentProficiency::class);
    }

    public function observations(): HasMany
    {
        return $this->hasMany(ProficiencyObservation::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('proficiencies.is_active', true);
    }
}
