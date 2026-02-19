<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\StudentDashboardData;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view(
            'student.dashboard',
            StudentDashboardData::from($user)
        );
    }
}
