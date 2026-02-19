<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CampusRegistration;

class RegistrationController extends Controller
{
    public function index()
    {
        $this->authorize('campus.registrations.view');

        $registrations = CampusRegistration::with([
                'student',
                'course',
                'course.season',
            ])
            ->latest()
            ->get();

        return view('manager.registrations.index', compact('registrations'));
    }
}
