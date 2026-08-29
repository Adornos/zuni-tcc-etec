<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Reports;
use App\Models\Schedule;
use App\Models\User;
use App\Models\TeacherSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\StudentSheet;
use Illuminate\Http\JsonResponse;

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
        $enrollments = StudentSheet::where('status', 'pending')->get(['id', 'student_id', 'name', 'status']);

        // Validar dados de $enrollments para disponibilizar depois.

        return view('coordinator.dashboard', [
            'dashboardInfo' => view('coordinator.enrollments', compact('enrollments'))
        ]);
    }

    public function searchEnrollments(Request $request)
    {
        $query = Enrollment::query()
            ->with('studentSheet');

        // Nome
        if ($request->filled('name')) {
            $name = $request->name;

            $query->whereHas('studentSheet', function ($q) use ($name) {
                $q->where('name', 'like', "%{$name}%");
            });
        }

        // Ano escolar
        if ($request->filled('ano')) {
            $query->whereHas('studentSheet', function ($q) use ($request) {
                $q->where('school_year', $request->ano);
            });
        }

        // Status
        if ($request->filled('status')) {
            $query->whereHas('studentSheet', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // // Turma
        // if ($request->filled('turma')) {
        //     $query->whereHas('studentSheet', function ($q) use ($request) {
        //         $q->where('class', $request->turma);
        //     });
        // }

        return response()->json(
            $query->get()
        );
    }

    public function showEnrollment($enrollment)
    {

        $enrollmentInfo = StudentSheet::where('id', $enrollment)->firstOrFail();

        return view('coordinator.dashboard',[ 
            'dashboardInfo' => view('coordinator.enrollment.info', ['enrollmentInfo' => $enrollmentInfo])
            ]);
    }

    public function approveEnrollment(Request $request, Enrollment $enrollment)
    {

        // Atualiza o processo de matrícula
        $enrollment->update([
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        // Atualiza a ficha do aluno
        $enrollment->studentSheet->update([
            'status' => 'active',
        ]);

        return redirect()->route('coordinator.enrollment.show', [
            'enrollment' => $enrollment->student_id,
        ]);
    }

    public function rejectEnrollment(Request $request, Enrollment $enrollment)
    {

        // Atualiza o processo de matrícula
        $enrollment->update([
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        // Atualiza a ficha do aluno
        $enrollment->studentSheet->update([
            'status' => 'inactive',
        ]);

        return redirect()->route('coordinator.enrollment.show', [
            'enrollment' => $enrollment->student_id,
        ]);
    }

    public function teachers(){
        return view('coordinator.dashboard', [
            'dashboardInfo' => view('coordinator.teacher.index')
        ]);
    }
    public function formTeacher(){
        return view('coordinator.dashboard', [
            'dashboardInfo' => view('coordinator.teacher.register')
        ]);
    }
    public function registerTeacher(Request $request){

        app(TeacherController::class)->store($request);
        
    }
    public function showTeacher($teacher){

        $teacherInfo = TeacherSheet::where('id', $teacher)->firstOrFail();

        return view('coordinator.dashboard', [
            'dashboardInfo' => view('coordinator.teacher.show', ['teacherInfo' => $teacherInfo])
        ]);
    }
    public function editTeacher(TeacherSheet $teacher){
        return redirect()->route('coordinator.teacher.show', [
            'teacherId' => $teacher->id,
        ]);
    }

    // Gerenciamento de horários (abordagem de grade)
    public function schedules()
    {
        $schedules = Schedule::with('teacher', 'student')->get();

        return view('coordinator.dashboard', [
            'dashboardInfo' => view('coordinator.schedules'), 
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

    public function studentSchedule(\App\Models\StudentSheet $student)
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

    // TRATAMENTO EDE RELATÓRIOS
    
    public function reports()
    {
        $reports = Reports::latest()->paginate(20);

        return view('coordinator.dashboard', [
            'dashboardInfo' => view('coordinator.reports.index', 
            compact('reports'))
            ]);
    }

    public function searchItems($index) : JsonResponse
    {
        return match ($index) {

            1 => response()->json([
                'type' => 'general',
                'data' => Reports::query()->get(),
            ]),

            2 => response()->json([
                'type' => 'classroom',
                'data' => [],
                ]),
                
            3 => response()->json([
                'type' => 'student',
                'data' => StudentSheet::query()->get('id', 'student_id', 'name', 'class'),
            ]),

            default => response()->json([
                'message' => 'Referência inválida.'
            ], 422),
            
        };
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

        Reports::create($validated);

        return redirect()->route('coordinator.reports.index');
    }

    public function editReport(Reports $report)
    {
        return view('coordinator.dashboard', [
            'dashboardInfo' => 'coordinator.reports.edit',
            compact('report')
            ]);
    }

    public function updateReport(Request $request, Reports $report)
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

    public function destroyReport(Reports $report)
    {
        $report->delete();

        return redirect()->route('coordinator.reports.index');
    }
}