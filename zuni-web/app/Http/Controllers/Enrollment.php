<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Enrollment extends Controller
{

    /**
     * Provê métodos com as matriculas
     */

    public function getRequests(Request $request)
    {
        $enrollments = \App\Models\Enrollment::with('student', 'guardian')
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return $enrollments;
    }

    
}
