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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('teacher.panel');
    }
    public function profile()
    {
        $user = Auth::user();
        return view('teacher.profile', ['profile' => $user]);
    }

    public function schedule()
    {
        $user = Auth::user();
        return view('teacher.schedule', ['profile' => $user]);
    }

    public function forum()
    {
        return view('teacher.forum');
    }
    public function chat()
    {
        return view('teacher.chat');
    }

    /**
     * Display the specified resource.
     */
    public function show(TeacherSheet $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeacherSheet $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeacherSheet $teacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeacherSheet $teacher)
    {
        //
    }
}
