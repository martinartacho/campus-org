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

        $stats = [];
        $widgets = [];

        // 📊 STATS (igual que ara)
        if ($user->can('campus.courses.view')) {
            $stats['courses'] = $adminData['total_courses'];
        }

        if ($user->can('campus.teachers.view')) {
            $stats['teachers'] = $adminData['teacher_count'];
        }

        if ($user->can('campus.students.view')) {
            $stats['students'] = $adminData['student_count'];
        }

        if ($user->can('campus.registrations.view')) {
            $stats['registrations'] = $adminData['total_registrations'];
        }

        // 🧠 WIDGETS OPERATIUS

        if ($user->can('campus.courses.view')) {
            $widgets[] = 'dashboard.widgets.courses_status';
        }

        if ($user->can('campus.registrations.view')) {
            $widgets[] = 'dashboard.widgets.recent_registrations';
        }

        if ($user->can('campus.registrations.manage')) {
            $widgets[] = 'dashboard.widgets.pending_registrations';
        }

        if ($user->can('campus.support.view')) {
            $widgets[] = 'dashboard.widgets.support_tickets';
        }

        if ($user->can('campus.courses.manage')) {
            $widgets[] = 'dashboard.widgets.alerts';
        }

        return [
            'stats' => $stats,
            'widgets' => $widgets,
        ];
    }
}

/* class ManagerDashboardData
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



} */
