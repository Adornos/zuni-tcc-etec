<?php

namespace App\Http\Controllers;


use App\Models\TeacherSheet;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index()
    {
        return view('teacher.panel');
    }
    
    public function schedule()
    {
        $user = Auth::user();
        return view('teacher.schedule', ['profile' => $user]);
    }

    public function forum()
    {
        return view('pages.forum.index');
    }

    public function chat()
    {
        return view('pages.chat.index');
    }
    
    public function destroy(User $teacher)
    {
        //
    }
}
