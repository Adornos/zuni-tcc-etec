<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('teacher.dashboard', [

            'dashboardInfo' => view('teacher.panel')
            
        ]);
    }
    public function profile()
    {
        $user = Auth::user();
        return view('teacher.dashboard', [
            'dashboardInfo' => view('teacher.profile', ['profile' => $user])
        ]);
    }

    public function schedule()
    {
        $user = Auth::user();
        return view('teacher.dashboard', [
            'dashboardInfo' => view('teacher.schedule', ['profile' => $user])
        ]);
    }

    public function forum()
    {
        return view('teacher.dashboard', [
            'dashboardInfo' => view('teacher.forum')
        ]);
    }
    public function chat()
    {
        return view('teacher.dashboard', [
            'dashboardInfo' => view('teacher.chat')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        //
    }
}
