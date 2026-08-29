<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\StudentSheet;
use App\Models\Enrollment;



#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'registration_number',
        'email',
        'cpf',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }


    public function isGuardian(): bool
    {
        return $this->role === UserRole::GUARDIAN;
    }

    public function students(): HasMany
    {
        return $this->hasMany(StudentSheet::class, 'guardian_id');
    }

    public function isStudent(): bool
    {
        return $this->role === UserRole::STUDENT;
    }

    public function studentSheet(): HasOne
    {
        return $this->hasOne(StudentSheet::class, 'student_id');
    }

    public function isTeacher(): bool
    {
        return $this->role === UserRole::TEACHER;
    }

    public function teacherSheet(): HasOne
    {
        return $this->hasOne(TeacherSheet::class, 'teacher_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }
    public function isCoordinator(): bool
    {
        return $this->role === UserRole::COORDINATOR;
    }



    // public function Enrollments(): HasMany
    // {
    //     return $this->hasMany(Enrollment::class, 'guardian_id');
    // }

    
    public function report(): HasMany
    {
        return $this->hasMany(Reports::class, 'report_id');
    }




}
