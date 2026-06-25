<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StudentController;

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

        $children = auth()->user()->students()->latest()->get() ?? [
        (object)[
            'name' => 'Maria Silva',
            'class' => '3º Ano A',
            'age' => 8
        ],
        (object)[
            'name' => 'João Pedro',
            'class' => '2º Ano B',
            'age' => 7
        ],
        (object)[
            'name' => 'Ana Clara',
            'class' => '4º Ano C',
            'age' => 9
        ],
    ];
        
        return view('guardian.dashboard', [
            // 'dashboardInfo' => view('guardian.registered', compact('children'))
            'dashboardInfo' => view('guardian.registered', compact('children'))
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function registerStudentForm()
    {
        $student = new StudentController();
                return view('guardian.dashboard', [
            // 'dashboardInfo' => view('guardian.registered', compact('children'))
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
