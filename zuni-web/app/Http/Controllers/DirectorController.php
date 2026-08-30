<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;

use App\Models\Enrollment;
use App\Models\Reports;
use App\Models\Schedule;
use App\Models\User;
use App\Models\DirectorSheet;

use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class DirectorController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return view('director.index');
    }


    /*
    |--------------------------------------------------------------------------
    | Perfil
    |--------------------------------------------------------------------------
    */

    public function profile()
    {
        $director = auth()->user()->directorSheet;

        return view('director.profile', compact('director'));
    }


    /*
    |--------------------------------------------------------------------------
    | Funcionários
    |--------------------------------------------------------------------------
    */

    /**
     * Lista de Funcionários
     */
    public function employees(){

        return view('director.employee.index');

    }

    /**
     * Cadastro de Funcionários
     */
    public function formEmployee(){

        return view('director.employee.register');

    }

    public function registerEmployee(Request $request)
    {
        return app(EmployeeController::class)->store($request);
    }

    public function showEmployee(User $employee)
    {
        $employee->load([
            'teacherSheet',
            'coordinatorSheet',
            'directorSheet',
        ]);

        return view('director.employee.show', [
            'employee' => $employee,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Fórum
    |--------------------------------------------------------------------------
    */

    public function forum()
    {
        return view('director.forum');
    }


    /*
    |--------------------------------------------------------------------------
    | Chat
    |--------------------------------------------------------------------------
    */

    public function chat()
    {
        return view('director.chat');
    }

}