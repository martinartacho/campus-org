<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\AdminDashboardData;

class DashboardController extends Controller
{
    public function index()
    {
        // (opcional, solo informativo)
        auth()->user()?->update([
            'last_context' => 'admin',
        ]);

        // Usamos EXACTAMENTE el mismo servicio
        $data = app(AdminDashboardData::class)->build();

        // 🔑 Usamos EXACTAMENTE la misma vista
        return view('dashboard', $data);
    }
}
