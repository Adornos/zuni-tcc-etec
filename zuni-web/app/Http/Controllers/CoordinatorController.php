<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Report;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoordinatorController extends Controller
{
    public function index()
    {
        return view('coordinator.dashboard', [
            'dashboardInfo' => view('coordinator.panel')
            
        ]);
    }
    public function profile()
    {
        $user = Auth::user();
        return view('coordinator.dashboard', [
            'dashboardInfo' => view('coordinator.profile', ['profile' => $user])
        ]);
    }

    public function forum()
    {
       return view('coordinator.dashboard', [
            'dashboardInfo' => view('coordinator.forum')
        ]);
    }

    public function chat()
    {
        return view('coordinator.dashboard', [
            'dashboardInfo' => view('coordinator.chat')
        ]);
    }

    // Aprovação de matrículas
    public function enrollments()
    {
        $enrollments = Enrollment::with('student', 'guardian')
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('coordinator.dashboard', [
            'dashboardInfo' => 'coordinator.enrollments.index',
            'enrollments' => $enrollments
        ]);
    }

    public function showEnrollment(Enrollment $enrollment)
    {
        return view('coordinator.dashboard',[
        'dashboardInfo' => 'coordinator.enrollments.show',
        'enrollments' => $enrollment
        ]);
    }

    public function approveEnrollment(Request $request, Enrollment $enrollment)
    {
        $enrollment->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('coordinator.enrollments.index');
    }

    public function rejectEnrollment(Request $request, Enrollment $enrollment)
    {
        $enrollment->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('coordinator.enrollments.index');
    }

    // Gerenciamento de horários (abordagem de grade)
    public function schedules()
    {
        $schedules = Schedule::with('teacher', 'student')->get();

        return view('coordinator.dashboard', [
            'dashboardInfo' => 'coordinator.schedules.index', 
            'schedules' => $schedules
        ]);
    }

    public function updateSchedules(Request $request)
    {
        $validated = $request->validate([
            'schedules' => ['required', 'array'],
            'schedules.*.id' => ['nullable', 'exists:schedules,id'],
            'schedules.*.day_of_week' => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday'],
            'schedules.*.start_time' => ['required'],
            'schedules.*.end_time' => ['required'],
            'schedules.*.teacher_id' => ['required', 'exists:users,id'],
            'schedules.*.student_id' => ['nullable', 'exists:students,id'],
            'schedules.*.subject' => ['nullable', 'string'],
        ]);

        foreach ($validated['schedules'] as $cell) {
            Schedule::updateOrCreate(
                ['id' => $cell['id'] ?? null],
                $cell
            );
        }

        return redirect()->route('coordinator.schedules.index');
    }

    public function studentSchedule(\App\Models\Student $student)
    {
        $schedules = Schedule::where('student_id', $student->id)->get();

        return view('coordinator.dashboard', [
            'dashboardInfo' => 'coordinator.schedules.student', 
            compact('student', 'schedules')
            ]);
    }

    public function teacherSchedule(User $teacher)
    {
        $schedules = Schedule::where('teacher_id', $teacher->id)->get();

        return view('coordinator.dashboard', [
            'dashboardInfo' => 'coordinator.schedules.teacher', 
            compact('teacher', 'schedules')
            ]);
    }

    // Relatórios (internos: para docentes | externos: para responsáveis)
    public function reports()
    {
        $reports = Report::latest()->paginate(20);

        return view('coordinator.dashboard', [
            'dashboardInfo' => 'coordinator.reports.index', 
            compact('reports')
            ]);
    }

    public function createReport()
    {
        return view('coordinator.dashboard', [
            'dashboardInfo' => 'coordinator.reports.create'
            ]);
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

        Report::create($validated);

        return redirect()->route('coordinator.reports.index');
    }

    public function editReport(Report $report)
    {
        return view('coordinator.dashboard', [
            'dashboardInfo' => 'coordinator.reports.edit',
            compact('report')
            ]);
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

        return redirect()->route('coordinator.reports.index');
    }

    public function destroyReport(Report $report)
    {
        $report->delete();

        return redirect()->route('coordinator.reports.index');
    }
}