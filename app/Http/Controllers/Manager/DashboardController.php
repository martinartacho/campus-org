<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\ManagerDashboardData;

class DashboardController extends Controller
{


    public function index()
    {
        $user = auth()->user();
        $stats = [];

        if ($user->hasRole('gestor')) {
            $stats = app(\App\Services\Dashboard\ManagerDashboardData::class)
                ->build($user);
        }
        dd('Error: 220260129: Fins ara no s\'havia fet servir comunicar a responsabe de programació');
        return view('dashboard', compact('stats'));
    }

}
