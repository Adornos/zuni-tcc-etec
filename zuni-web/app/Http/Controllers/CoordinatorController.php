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
       
            return view('pages.forum.index');
        
    }

    public function chat()
    {
        
            return view('pages.chat.index');
        
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

        return view('pages.student.show', ['studentSheet' => $studentInfo]);
            
    }

    public function approveEnrollment(Enrollment $enrollment)
    {
        $enrollment->update([
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $user = $enrollment->studentSheet->user;

        $user->status = UserStatus::ACTIVE;
        $user->save();

        return redirect()->route('coordinator.student.show', [
            'student' => $user,
        ]);
    }

    public function rejectEnrollment(Enrollment $enrollment)
    {
        

        $enrollment->update([
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $user = $enrollment->studentSheet->user;
        
        $user->status = UserStatus::INACTIVE;
        $user->save();

        return redirect()->route('coordinator.student.show', [
            'student' => $user,
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

        return view('pages.schedule.student');

    }

    public function teacherSchedule(User $teacher)
    {
        $schedules = Schedule::where('teacher_id', $teacher->id)->get();

        
        return view('pages.schedule.teacher');

    }

}