<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class DirectorController extends Controller
{
    public function index()
    {
        return view('director.index');
    }

    public function profile()
    {
        return view('director.profile');
    }

    public function forum()
    {
        return view('director.forum');
    }

    public function chat()
    {
        return view('director.chat');
    }

    // Gerenciamento de professores
    public function teachers()
    {
        $teachers = User::where('role', 'teacher')->paginate(20);

        return view('director.teachers.index', compact('teachers'));
    }

    public function showTeacher(User $teacher)
    {
        return view('director.teachers.show', compact('teacher'));
    }

    public function storeTeacher(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        $validated['role'] = 'teacher';

        $teacher = User::create($validated);

        return redirect()->route('director.teachers.show', $teacher);
    }

    public function updateTeacher(Request $request, User $teacher)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $teacher->id],
        ]);

        $teacher->update($validated);

        return redirect()->route('director.teachers.show', $teacher);
    }

    public function destroyTeacher(User $teacher)
    {
        $teacher->delete();

        return redirect()->route('director.teachers.index');
    }

    // Relatórios
    public function reports()
    {
        $reports = Report::latest()->paginate(20);

        return view('director.reports.index', compact('reports'));
    }

    public function createReport()
    {
        return view('director.reports.create');
    }

    public function storeReport(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'type' => ['required', 'in:internal,external'],
            'student_id' => ['nullable', 'exists:students,id'],
        ]);

        $validated['author_id'] = $request->user()->id;

        $report = Report::create($validated);

        return redirect()->route('director.reports.index');
    }

    public function editReport(Report $report)
    {
        return view('director.reports.edit', compact('report'));
    }

    public function updateReport(Request $request, Report $report)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'type' => ['sometimes', 'in:internal,external'],
            'student_id' => ['nullable', 'exists:students,id'],
        ]);

        $report->update($validated);

        return redirect()->route('director.reports.index');
    }

    public function destroyReport(Report $report)
    {
        $report->delete();

        return redirect()->route('director.reports.index');
    }
}