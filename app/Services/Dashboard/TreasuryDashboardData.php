<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\ConsentHistory;
use App\Models\CampusSeason;
use App\Models\CampusTeacher;
use App\Models\CampusCourseTeacher;

class TreasuryDashboardData
{
    public function build($user): array
    {
        // Forçar la temporada 2024-25 per al dashboard de tresoreria
        $seasonCode = "2024-25";
        $season = CampusSeason::where("slug", $seasonCode)->first();

        // Professors totals / RGPD acceptat
        $totalTeachers = CampusTeacher::count();
        $teachersWithConsent = ConsentHistory::where("season", $seasonCode)
            ->whereNotNull("document_path")
            ->distinct("teacher_id")
            ->count("teacher_id");

        // Dades Bancàries: Total assignacions de cursos / Actualitzades
        $totalCourseAssignments = CampusCourseTeacher::whereHas("course", function($query) use ($season) {
            $query->where("season_id", $season?->id);
        })->count();

        return [
            "season" => $seasonCode,
            
            // Professors totals / RGPD acceptat
            "teachers_total" => $totalTeachers,
            "teachers_with_rgpd" => $teachersWithConsent,
            
            // Dades Bancàries (Total / Actualitzades)
            "course_assignments_total" => $totalCourseAssignments,
            "course_assignments_updated" => $teachersWithConsent, // Han acceptat consentiment i PDF creat
            
            // Últims consentiments
            "last_consents" => ConsentHistory::with("teacher")
                ->where("season", $seasonCode)
                ->orderByDesc("accepted_at")
                ->limit(5)
                ->get(),
        ];
    }
}
