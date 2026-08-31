<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Enrollment;
use App\Models\Report;
use App\Models\Schedule;
use App\Models\User;
use App\Models\TeacherSheet;
use App\Models\CoordinatorSheet;
use App\Models\StudentSheet;

use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CoordinatorController extends Controller
{
    public function index()
    {
        return  view('coordinator.panel');
        
    }
    public function profile()
    {
        $user = Auth::user();
        
            return view('coordinator.profile', ['profile' => $user]);
        
    }

    public function forum()
    {
       
            return view('coordinator.forum');
        
    }

    public function chat()
    {
        
            return view('coordinator.chat');
        
    }

    // Aprovação de matrículas
    public function students()
    {

        // Validar dados de $students para disponibilizar depois.

        
            return view('coordinator.student.index');
        
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

    public function showStudent($student)
    {

        $studentInfo = StudentSheet::where('id', $student)->firstOrFail();

        return view('coordinator.student.show', ['studentSheet' => $studentInfo]);
            
    }

    public function approveEnrollment(Enrollment $enrollment)
    {


        $enrollment->update([
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $user = $enrollment->studentSheet->user;

        $user->status = UserStatus::ACTIVE;
        $user->save();

        return redirect()->route('coordinator.enrollment.show', [
            'enrollment' => $enrollment->student_id,
        ]);
    }

    public function rejectEnrollment(Enrollment $enrollment)
    {


        $enrollment->update([
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $user = $enrollment->studentSheet->user;
        
        $user->status = UserStatus::INACTIVE;
        $user->save();

        return redirect()->route('coordinator.enrollment.show', [
            'enrollment' => $enrollment->student_id,
        ]);
    }

    public function teachers(){
        
        return view('coordinator.teacher.index');
        
    }
    public function formTeacher(){
        
        return view('coordinator.teacher.register');
        
    }
    public function registerTeacher(Request $request)
    {
        return app(EmployeeController::class)->store($request);
    }
    public function showTeacher($teacher){

        $teacherInfo = TeacherSheet::where('id', $teacher)->firstOrFail();


        return view('coordinator.teacher.show', ['teacherInfo' => $teacherInfo]);

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

        return view('coordinator.schedules');

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

        return view('coordinator.schedules.student');

    }

    public function teacherSchedule(User $teacher)
    {
        $schedules = Schedule::where('teacher_id', $teacher->id)->get();

        
        return view('coordinator.schedules.teacher');

    }

    // TRATAMENTO EDE RELATÓRIOS
    
    public function reports()
    {
        $reports = Report::latest()->paginate(20);

        return view('coordinator.reports.index');

    }

    public function searchItems($index) : JsonResponse
    {
        return match ($index) {

            1 => response()->json([
                'type' => 'general',
                'data' => Report::query()->get(),
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
        return view('coordinator.reports.create');
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
        return view('coordinator.reports.edit');

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