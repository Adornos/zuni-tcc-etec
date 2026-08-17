<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\studentSheet;
// use App\Models\TeacherSheet;


class Enrollment extends Model
{
    // database/migrations/xxxx_xx_xx_create_enrollments_table.php

    protected $fillable = [
        'student_id',
        'guardian_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    function guardian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }

    function studentSheet(): BelongsTo
    {
        return $this->belongsTo(studentSheet::class, 'student_id');
    }
}
