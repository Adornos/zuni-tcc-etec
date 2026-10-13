<?php

namespace App\Http\Controllers;

use App\Models\TeacherSheet;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class GuardianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('guardian.panel');
    }
    
    public function forum()
    {
        return view('pages.forum.index');
    }

    public function chat()
    {
        return view('pages.chat.index');
    }
}
