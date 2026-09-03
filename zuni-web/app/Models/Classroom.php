<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Classroom extends Model
{
    protected $fillable = [
        'name',
        'grade',
        'shift',
        'capacity',
        'teacher_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }

    /**
     * Professor responsável pela turma
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(
            Classroom::class,
            'classroom_teacher', 
            'teacher_id', 
            'classroom_id'
            );
    }

    /**
     * Alunos da turma
     */
    public function students(): HasMany
    {
        return $this->hasMany(StudentSheet::class, 'classroom_id');
    }

    /**
     * Relatórios da turma
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'classroom_id');
    }

    /**
     * Relatórios da turma
     */
    public function performances(): HasMany
    {
        return $this->hasMany(ClassroomPerformance::class, 'classroom_id');
    }

    public function latestPerformance(): HasOne
    {
        return $this->hasOne(ClassroomPerformance::class)
            ->latestOfMany([
                'year',
                'period',
            ]);
    }
}