<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\ConsentHistory;
use App\Models\CampusSeason;

class TreasuryDashboardData
{
    public function build($user): array
    {
        $season = CampusSeason::where('is_active', true)->first();

        // fallback de seguretat (mai hauria de passar)
            //        $seasonCode = $season?->code;
         $seasonCode = $season?->slug;


        return [
            'season' => $seasonCode,

            'teachers_total' => User::role('teacher')->count(),

            'teachers_pending_rgpd' => User::role('teacher')
                ->whereDoesntHave('consents', function ($q) use ($seasonCode) {
                    $q->where('season', $seasonCode);
                })->count(),

            'teachers_with_rgpd' => User::role('teacher')
                ->whereHas('consents', function ($q) use ($seasonCode) {
                    $q->where('season', $seasonCode);
                })->count(),

            'last_consents' => ConsentHistory::with('teacher')
                ->where('season', $seasonCode)
                ->orderByDesc('accepted_at')
                ->limit(5)
                ->get(),
        ];
    }
}
