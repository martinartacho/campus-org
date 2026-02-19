<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Models\CampusCourse;

class CourseRegistrationController extends Controller
{
    public function index(CampusCourse $course)
    {
        abort_unless(
            auth()->user()->can('campus.registrations.view'),
            403
        );

        $registrations = $course->registrations()
            ->with('student')
            ->orderBy('created_at')
            ->get();

        return view('campus.courses.registrations', compact(
            'course',
            'registrations'
        ));
    }

}
