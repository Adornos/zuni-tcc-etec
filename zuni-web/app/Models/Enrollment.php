<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\User;
use App\Models\studentSheet;
// use App\Models\TeacherSheet;


class Enrollment extends Model
{
    protected $primaryKey = 'student_id';

    protected $fillable = [
        'sheet_id',
        'reviewed_by',
        'reviewed_at',
    ];

    public function studentSheet()
    {
        return $this->belongsTo(StudentSheet::class, 'sheet_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

}
