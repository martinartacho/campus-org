<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CampusCourse;
use Illuminate\Http\Request;

class CoursesController extends Controller
{
    /**
     * Obtener lista de cursos para select
     */
    public function list(Request $request)
    {
        $courses = CampusCourse::select('id', 'code', 'title', 'format', 'price', 'parent_id')
            ->where('is_active', 1)
            ->where('is_public', 1)
            ->orderBy('code')
            ->get();

        return response()->json($courses);
    }
}
