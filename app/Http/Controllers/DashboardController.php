<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CampusCourse;
use App\Models\CampusCategory;
use App\Models\CampusSeason;
use App\Models\Event;
use App\Models\Feedback;
use App\Models\CampusRegistration;

class DashboardController extends Controller
{

public function index()
    {
        $user = auth()->user();
        $data = [];

        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            $data = app(\App\Services\Dashboard\AdminDashboardData::class)->build();

        } elseif ($user->hasAnyRole(['gestor', 'editor', 'manager'])) {
            $data = app(\App\Services\Dashboard\ManagerDashboardData::class)
                ->build($user);
        } elseif ($user->hasAnyRole(['treasury'])) {
            $data = app(\App\Services\Dashboard\TreasuryDashboardData::class)
                ->build($user);
            return view('dashboard.treasury', compact('data'));
        } elseif ($user->hasRole('teacher')) {
            $data = app(\App\Services\Dashboard\TeacherDashboardData::class)
                ->build($user);
        } elseif ($user->hasRole('student')) {
            $data = app(\App\Services\Dashboard\StudentDashboardData::class)
                ->build($user);
        }

        return view('dashboard', $data);
    }


}
