<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherSheet extends Model
{
    protected $fillable = [
        'teacher_id',

        'status',

        'name',
        'birth_date',
        'gender',

        'cpf',
        'rg',

        'phone',
        'email',

        'formation',
        'specialization',
        'registration',
        'hire_date',

        'street',
        'number',
        'district',
        'city',
        'state',

        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
    ];

    /**
     * TeacherSheet belongs to a Teacher (User)
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}