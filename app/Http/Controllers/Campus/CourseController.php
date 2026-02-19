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
        return $request->validate([
            'season_id'     => ['required', 'exists:campus_seasons,id'],
            'category_id'   => ['nullable', 'exists:campus_categories,id'],
            'code'          => ['nullable', 'string', 'max:50'],
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'credits'       => ['nullable', 'integer', 'min:0'],
            'hours'         => ['nullable', 'integer', 'min:0'],
            'max_students'  => ['nullable', 'integer', 'min:1'],
            'price'         => ['nullable', 'numeric', 'min:0'],
            'level'         => ['nullable', 'string', 'max:50'],
            'schedule'      => ['nullable', 'array'],
            'start_date'    => ['nullable', 'date'],
            'end_date'      => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active'     => ['boolean'],
            'is_public'     => ['boolean'],
            'requirements'  => ['nullable', 'array'],
            'objectives'    => ['nullable', 'array'],
            'metadata'      => ['nullable', 'array'],
        ]);
    }
}
