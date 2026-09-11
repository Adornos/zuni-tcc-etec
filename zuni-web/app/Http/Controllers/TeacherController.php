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

    public function show(User $teacher)
    {   

        if(isset($teacher)){
            $teacher = Auth::user();
            abort_unless($teacher->role === UserRole::TEACHER, 403, 'Usuário não permitido');
        }

        return view('pages.teacher.show', ['teacherInfo' => $teacher]);    

    }
    
    public function edit()
    {
        $user = Auth::user();
        return view('pages.teacher.edit', ['profile' => $user]);
    }

    public function update(Request $request)
    {

        $user = Auth::user();
        abort_unless($user->role === UserRole::TEACHER, 403, 'Acesso negado.');

        app(EmployeeController::class)->update($request, $user);


        return redirect()->route('teacher.profile')->with('status', 'Perfil atualizado com sucesso!');

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $teacher)
    {
        //
    }
}
