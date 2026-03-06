<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Models\CampusCourse;
use Illuminate\Http\Request;

class CourseApiController extends Controller
{
    /**
     * Get courses for modal selection.
     */
    public function index(Request $request)
    {
        $courses = CampusCourse::where('is_active', true)
            ->select(['id', 'title', 'code', 'category_id'])
            ->with(['category:id,name'])
            ->orderBy('title')
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'code' => $course->code,
                    'category' => $course->category?->name,
                    'display_name' => "{$course->title} ({$course->code})"
                ];
            });

        return response()->json($courses);
    }

    /**
     * Search courses by title or code.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $courses = CampusCourse::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('code', 'like', "%{$query}%");
            })
            ->select(['id', 'title', 'code'])
            ->orderBy('title')
            ->limit(20)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'code' => $course->code,
                    'display_name' => "{$course->title} ({$course->code})"
                ];
            });

        return response()->json($courses);
    }
}
