<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Models\CampusCourse;
use App\Models\CampusSeason;
use App\Models\CampusCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:campus.courses.view')->only(['index', 'show']);
        $this->middleware('can:campus.courses.create')->only(['create', 'store']);
        $this->middleware('can:campus.courses.edit')->only(['edit', 'update']);
        $this->middleware('can:campus.courses.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the courses.
     */
    public function index()
    {
        $courses = CampusCourse::with(['season', 'category'])
            ->orderByDesc('start_date')
            ->paginate(15);

        return view('campus.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        $seasons = CampusSeason::orderByDesc('season_start')->get();
        $categories = CampusCategory::orderBy('name')->get();

        return view('campus.courses.create', compact('seasons', 'categories'));
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $data['slug'] = Str::slug($data['title']);

        $course = CampusCourse::create($data);

        return redirect()
            ->route('campus.courses.show', $course)
            ->with('success', __('campus.course_created'));
    }

    /**
     * Display the specified course.
     */
    public function show(CampusCourse $course)
    {
        $course->load(['season', 'category']);

        return view('campus.courses.show', compact('course'));
    }

    /**
     * Get course data as JSON for AJAX requests.
     */
    public function getCourseData(CampusCourse $course)
    {
        \Log::info('getCourseData called for course ID: ' . $course->id);
        
        // Load course with relationships including schedules
        $course->load(['season', 'category', 'schedules']);
        
        $courseData = [
            'id' => $course->id,
            'title' => $course->title,
            'requirements' => $course->requirements,
            'sessions' => $course->sessions,
            'season_id' => $course->season_id,
            'category_id' => $course->category_id,
            'code' => $course->code,
            'description' => $course->description,
            'credits' => $course->credits,
            'hours' => $course->hours,
            'max_students' => $course->max_students,
            'price' => $course->price,
            'level' => $course->level,
            'schedule' => $course->schedule,
            'start_date' => $course->start_date,
            'end_date' => $course->end_date,
            'location' => $course->location,
            'format' => $course->format,
            'is_active' => $course->is_active,
            'is_public' => $course->is_public,
            'objectives' => $course->objectives,
            'metadata' => $course->metadata,
            'created_at' => $course->created_at,
            'updated_at' => $course->updated_at,
            'season' => $course->season,
            'category' => $course->category,
            'schedules' => $course->schedules, // Include schedules
        ];
        
        \Log::info('Course data loaded:', $courseData);
        
        return response()->json([
            'success' => true,
            'course' => $courseData
        ]);
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(CampusCourse $course)
    {
        $seasons = CampusSeason::orderByDesc('season_start')->get();
        $categories = CampusCategory::orderBy('name')->get();

        return view('campus.courses.edit', compact('course', 'seasons', 'categories'));
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, CampusCourse $course)
    {
        $data = $this->validatedData($request);

        if ($course->title !== $data['title']) {
            $data['slug'] = Str::slug($data['title']);
        }

        $course->update($data);

        // Handle schedule assignment (space, time_slot, semester)
        if ($request->has('space_id') && $request->has('time_slot_id') && $request->has('semester')) {
            $spaceId = $request->input('space_id');
            $timeSlotId = $request->input('time_slot_id');
            $semester = $request->input('semester');

            if ($spaceId && $timeSlotId && $semester) {
                // Create or update course schedule
                \App\Models\CampusCourseSchedule::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'space_id' => $spaceId,
                        'time_slot_id' => $timeSlotId,
                        'semester' => $semester,
                    ],
                    [
                        'status' => 'assigned'
                    ]
                );
            }
        }

        // If AJAX request, return JSON response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('campus.course_updated'),
                'course' => $course->load(['season', 'category', 'schedules'])
            ]);
        }

        // For regular form submissions, redirect as before
        return redirect()
            ->route('campus.courses.show', $course)
            ->with('success', __('campus.course_updated'));
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(CampusCourse $course)
    {
        $course->delete();

        return redirect()
            ->route('campus.courses.index')
            ->with('success', __('campus.course_deleted'));
    }

    /**
     * Validation rules shared by store & update.
     */
    protected function validatedData(Request $request): array
    {
        // Debug: Mostrar todos los datos recibidos antes de validar
        \Log::info('Request received:', [
            'all_data' => $request->all(),
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type')
        ]);
        
        $data = $request->validate([
            'season_id'     => ['required', 'exists:campus_seasons,id'],
            'category_id'   => ['nullable', 'exists:campus_categories,id'],
            'code'          => ['nullable', 'string', 'max:50'],
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'credits'       => ['nullable', 'integer', 'min:0'],
            'hours'         => ['nullable', 'integer', 'min:0'],
            'sessions'      => ['nullable', 'integer', 'min:30', 'max:480'],
            'max_students'  => ['nullable', 'integer', 'min:1'],
            'price'         => ['nullable', 'numeric', 'min:0'],
            'level'         => ['nullable', 'string', 'max:50'],
            'schedule'      => ['nullable', 'array'],
            'start_date'    => ['nullable', 'date'],
            'end_date'      => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active'     => ['boolean'],
            'is_public'     => ['boolean'],
            'requirements'  => ['nullable', 'string'],
            'objectives'    => ['nullable', 'string'],
            'metadata'      => ['nullable', 'array'],
        ]);
        
        // Debug: Mostrar todos los datos recibidos y validados
        \Log::info('Validation result:', [
            'validated_data' => $data
        ]);
        
        return $data;
    }
    
    /**
     * Handle validation errors for debugging
     */
    protected function handleValidationErrors(Request $request, array $errors)
    {
        \Log::error('Validation errors:', [
            'errors' => $errors,
            'request_data' => $request->all()
        ]);
        
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $errors
        ], 422);
    }
}
