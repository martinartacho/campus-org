<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\CampusCourse;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Registration;

class ManagerDashboardData
{
    public function build(User $user): array
    {
        $adminData = app(AdminDashboardData::class)->raw();

        $filteredStats = [];

        if ($user->can('campus.courses.view')) {
            $filteredStats['courses'] = $adminData['total_courses'];
        }

        if ($user->can('campus.teachers.view')) {
            $filteredStats['teachers'] = $adminData['teacher_count'];
        }

        if ($user->can('campus.students.view')) {
            $filteredStats['students'] = $adminData['student_count'];
        }

        if ($user->can('campus.registrations.view')) {
            $filteredStats['registrations'] = $adminData['total_registrations'];
        }

        return [
            'stats' => $filteredStats,
        ];
    }



}
