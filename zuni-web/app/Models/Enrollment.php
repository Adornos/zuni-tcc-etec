<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\User;
use App\Models\studentSheet;
// use App\Models\TeacherSheet;


class Enrollment extends Model
{
    // database/migrations/xxxx_xx_xx_create_enrollments_table.php

    protected $fillable = [
        'sheet_id',
        'reviewed_by',
        'reviewed_at',
    ];

    function student_sheet(): BelongsTo
    {
        return $this->belongsTo(StudentSheet::class, 'sheet_id');
    }

}
