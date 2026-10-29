<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'guardian_id',
        'classroom_id',
        'registration_number',

        'sociability',
        'autonomy',
        'engagement',
        'communication',
        'motor_development',

        'neurodivergent',
        'allergy',
        'food_restriction',
        'special_care',

        'notes',
    ];

    protected function casts(): array
    {
        return [
            'sociability' => 'decimal:2',
            'autonomy' => 'decimal:2',
            'engagement' => 'decimal:2',
            'communication' => 'decimal:2',
            'motor_development' => 'decimal:2',

            'neurodivergent' => 'boolean',
            'allergy' => 'boolean',
            'food_restriction' => 'boolean',
            'special_care' => 'boolean',
        ];
    }

    /**
     * Student User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Guardian responsible for the student
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }

    /**
     * Enrollment
     */
    public function enrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class, 'sheet_id');
    }
    /**
     * Classroom
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    /**
     * Reports
     */
    public function reports(): BelongsToMany
    {
        return $this->belongsToMany(
            Report::class,
            'report_student_sheet',
            'student_sheet_id',
            'report_id'
        );
    }

    /**
     * Valores atuais das proficiências (um registro por habilidade).
     */
    public function studentProficiencies(): HasMany
    {
        return $this->hasMany(StudentProficiency::class);
    }

    /**
     * Histórico de observações de proficiência.
     */
    public function proficiencyObservations(): HasMany
    {
        return $this->hasMany(ProficiencyObservation::class);
    }

    /**
     * Fichas que o usuário pode abrir (mesma regra de StudentSheetPolicy):
     * coordenador/diretor todas; professor as das suas salas; responsável as dos seus filhos.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return match ($user->role) {
            UserRole::COORDINATOR, UserRole::DIRECTOR => $query,

            UserRole::TEACHER => $query->whereIn(
                'student_sheets.classroom_id',
                $user->classrooms()->select('classrooms.id')
            ),

            UserRole::GUARDIAN => $query->where('student_sheets.guardian_id', $user->id),

            default => $query->whereRaw('1 = 0'),
        };
    }
}
