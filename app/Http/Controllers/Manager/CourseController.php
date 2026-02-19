<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CampusCourse;

class CourseController extends Controller
{
    public function index()
    {
        $this->authorize('campus.courses.view');

        $courses = CampusCourse::with('category', 'season')
            ->orderBy('name')
            ->get();

        return view('manager.courses.index', compact('courses'));
    }
}
