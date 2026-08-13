<?php

namespace App\Enums;

enum UserRole: string
{
    case GUARDIAN = 'guardian';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    case COORDINATOR = 'coordinator';
    case DIRECTOR = 'director';
    case ADMIN = 'admin';
}