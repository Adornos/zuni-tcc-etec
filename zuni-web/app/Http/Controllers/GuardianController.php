<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Enrollment;


class GuardianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('guardian.dashboard', [
            'dashboardInfo' => view('guardian.panel')
            
        ]);
    }
    public function profile()
    {
        $user = Auth::user();
        return view('guardian.dashboard', [
            'dashboardInfo' => view('guardian.profile', ['profile' => $user])
        ]);
    }
    public function registered()
    {

        $students = auth()->user()->students()->latest()->get() ?? [];
        

        return view('guardian.dashboard', [

            'dashboardInfo' => view('guardian.registered', [
                'students' => $students,
            ])
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function registerStudentForm()
    {
        $student = new StudentController();
        return view('guardian.dashboard', [
            'dashboardInfo' => $student->create()
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function registerStudent(Request $request)
    {
        return app(StudentController::class)->store($request);
    }

    public function forum()
    {
        return view('guardian.dashboard', [
            'dashboardInfo' => view('guardian.forum')
        ]);
    }
    public function chat()
    {
        return view('guardian.dashboard', [
            'dashboardInfo' => view('guardian.chat')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
