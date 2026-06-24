<?php

namespace App\Enums;

enum UserRole: string
{
    case GUARDIAN = 'guardian';
    case TEACHER = 'teacher';
    case COORDINATOR = 'coordinator';
    case ADMIN = 'admin';
}