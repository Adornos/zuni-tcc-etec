<?php

namespace App\Http\Controllers;

use App\Models\DirectorSheet;
use App\Models\TeacherSheet;
use App\Models\User;
use Illuminate\Http\Request;

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
        return match ($request->role) {
            'teacher' => app(TeacherController::class)->store($request),
            'coordinator' => app(CoordinatorController::class)->store($request),
            'director' => app(DirectorController::class)->store($request),

            default => abort(422, 'Cargo inválido.'),
        };
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