<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\ConsentHistory;
use App\Models\CampusSeason;
use App\Models\CampusTeacher;
use App\Models\CampusCourseTeacher;
use App\Models\CampusTeacherPayment;

class TreasuryDashboardData
{
    public function build($user): array
    {
        // Forçar la temporada 2024-25 per al dashboard de tresoreria
        $seasonCode = "2024-25";
        $season = CampusSeason::where("slug", $seasonCode)->first();

        // professorat totals / RGPD acceptat
        $totalTeachers = CampusTeacher::count();
        $teachersWithConsent = ConsentHistory::where("season", $seasonCode)
            ->whereNotNull("document_path")
            ->distinct("teacher_id")
            ->count("teacher_id");

        // Dades Bancàries: Total assignacions de cursos / Pendents
        $totalCourseAssignments = CampusCourseTeacher::whereHas("course", function($query) use ($season) {
            $query->where("season_id", $season?->id);
        })->count();

        // Dades bancàries pendents de confirmación
        $pendingBankData = CampusTeacherPayment::where('needs_payment', true)
            ->whereHas('course', function($query) use ($season) {
                $query->where("season_id", $season?->id);
            })
            ->count();

        return [
            "season" => $seasonCode,
            
            // professorat totals / RGPD acceptat
            "teachers_total" => $totalTeachers,
            "teachers_with_rgpd" => $teachersWithConsent,
            
            // Dades Bancàries (Total / Pendents)
            "course_assignments_total" => $totalCourseAssignments,
            "course_assignments_updated" => $teachersWithConsent, // Han acceptat consentiment i PDF creat
            "pending_bank_data" => $pendingBankData,
            
            // Últims consentiments
            "last_consents" => ConsentHistory::with("teacher")
                ->where("season", $seasonCode)
                ->orderByDesc("accepted_at")
                ->limit(5)
                ->get(),
        ];
    }
}
