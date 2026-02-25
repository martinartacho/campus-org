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
    public function index(Request $request)
    {
        $query = CampusCourse::with(['season', 'category']);
        
        // Filtro por código
        if ($request->filled('search_code')) {
            $query->where('code', 'like', '%' . $request->search_code . '%');
        }
        
        // Filtro por título
        if ($request->filled('search_title')) {
            $query->where('title', 'like', '%' . $request->search_title . '%');
        }
        
        // Filtro por temporada
        if ($request->filled('search_season')) {
            $query->where('season_id', $request->search_season);
        }
        
        // Filtro por categoría
        if ($request->filled('search_category')) {
            $query->where('category_id', $request->search_category);
        }
        
        // Filtro por estado
        if ($request->filled('search_status')) {
            $query->where('is_active', $request->search_status === 'active');
        }
        
        // Filtro por nivel
        if ($request->filled('search_level')) {
            $query->where('level', $request->search_level);
        }
        
        // Filtro por formato
        if ($request->filled('search_format')) {
            $query->where('format', $request->search_format);
        }
        
        // Filtro por precio mínimo
        if ($request->filled('search_price_min')) {
            $query->where('price', '>=', $request->search_price_min);
        }
        
        // Filtro por precio máximo
        if ($request->filled('search_price_max')) {
            $query->where('price', '<=', $request->search_price_max);
        }
        
        // Filtro por horas mínimas
        if ($request->filled('search_hours_min')) {
            $query->where('hours', '>=', $request->search_hours_min);
        }
        
        // Filtro por horas máximas
        if ($request->filled('search_hours_max')) {
            $query->where('hours', '<=', $request->search_hours_max);
        }
        
        // Filtro por fecha de inicio
        if ($request->filled('search_date_from')) {
            $query->whereDate('start_date', '>=', $request->search_date_from);
        }
        
        if ($request->filled('search_date_to')) {
            $query->whereDate('start_date', '<=', $request->search_date_to);
        }
        
        // Filtro por ubicación
        if ($request->filled('search_location')) {
            $query->where('location', 'like', '%' . $request->search_location . '%');
        }
        
        // Ordenamiento
        $sortBy = $request->get('sort_by', 'start_date');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['title', 'code', 'start_date', 'end_date', 'price', 'hours', 'max_students'])) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $courses = $query->paginate(15)->withQueryString();
        
        // Obtener valores únicos para filtros
        $seasons = CampusSeason::orderByDesc('season_start')->get();
        $categories = CampusCategory::orderBy('name')->get();
        $levels = ['beginner', 'intermediate', 'advanced'];
        $formats = ['presencial', 'online', 'hybrid'];
        
        return view('campus.courses.index', compact('courses', 'seasons', 'categories', 'levels', 'formats'));
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
        
        // Load course with relationships including space and timeSlot
        $course->load(['season', 'category', 'space', 'timeSlot']);
        
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
            'status' => $course->status,
            'objectives' => $course->objectives,
            'metadata' => $course->metadata,
            'created_at' => $course->created_at,
            'updated_at' => $course->updated_at,
            'season' => $course->season,
            'category' => $course->category,
            'space' => $course->space,
            'timeSlot' => $course->timeSlot,
            'space_id' => $course->space_id,
            'time_slot_id' => $course->time_slot_id,
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

        // Check for conflicts before updating
        if (isset($data['space_id']) && isset($data['time_slot_id'])) {
            $hasConflict = CampusCourse::hasConflict(
                $data['space_id'], 
                $data['time_slot_id'], 
                $course->id
            );
            
            if ($hasConflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Coincidencia detectada: El espacio y franja ya están ocupados por otro curso',
                    'conflict' => true,
                    'conflicts' => CampusCourse::getConflicts(
                        $data['space_id'], 
                        $data['time_slot_id'], 
                        $course->id
                    )
                ], 422);
            }
        }

        if ($course->title !== $data['title']) {
            $data['slug'] = Str::slug($data['title']);
        }

        $course->update($data);
        
        // Handle manual status completion
        if ($request->has('status_completed') && $request->input('status_completed') === 'completed') {
            $course->status = CampusCourse::STATUS_COMPLETED;
            $course->save();
        } else {
            // Auto-update status based on completion (only if not manually set)
            $course->updateStatus();
        }

        // If AJAX request, return JSON response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('campus.course_updated'),
                'course' => $course->load(['season', 'category', 'space', 'timeSlot']),
                'status' => $course->status,
                'status_label' => $course->getStatusLabel(),
                'status_color' => $course->getStatusColor()
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
            'slug'          => ['nullable', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'credits'       => ['nullable', 'integer', 'min:0', 'max:240'],
            'hours'         => ['nullable', 'integer', 'min:1', 'max:1000'],
            'sessions'      => ['nullable', 'integer', 'min:1', 'max:100'],
            'max_students'  => ['nullable', 'integer', 'min:1'],
            'price'         => ['nullable', 'numeric', 'min:0'],
            'level'         => ['nullable', 'string', 'max:50'],
            'schedule'      => ['nullable', 'array'],
            'start_date'    => ['nullable', 'date'],
            'end_date'      => ['nullable', 'date', 'after_or_equal:start_date'],
            'location'      => ['nullable', 'string', 'max:255'],
            'format'        => ['nullable', 'string', 'max:50'],
            'is_active'     => ['boolean'],
            'is_public'     => ['boolean'],
            'status'        => ['nullable', 'string', 'in:draft,planning,in_progress,completed,closed'],
            'requirements'  => ['nullable', 'string'],
            'objectives'    => ['nullable', 'string'],
            'metadata'      => ['nullable', 'array'],
            'space_id'      => ['nullable', 'exists:campus_spaces,id'],
            'time_slot_id'  => ['nullable', 'exists:campus_time_slots,id'],
            'semester'       => ['nullable', 'string', 'in:1Q,2Q'],
            'status_completed' => ['nullable', 'string'],  // Agregar esta regla
        ]);
        
        // Debug: Mostrar todos los datos recibidos y validados
        \Log::info('Validation result:', [
            'validated_data' => $data
        ]);
        
        return $data;
    }
    
    /**
     * Check for course conflicts in space and time slot.
     */
    public function checkConflict(Request $request)
    {
        $validated = $request->validate([
            'space_id' => 'required|exists:campus_spaces,id',
            'time_slot_id' => 'required|exists:campus_time_slots,id',
            'exclude_course_id' => 'nullable|exists:campus_courses,id'
        ]);

        // Debug: Mostrar qué estamos verificando
        \Log::info('Checking conflict for:', [
            'space_id' => $validated['space_id'],
            'time_slot_id' => $validated['time_slot_id'],
            'exclude_course_id' => $validated['exclude_course_id'] ?? null
        ]);

        $hasConflict = CampusCourse::hasConflict(
            $validated['space_id'],
            $validated['time_slot_id'],
            $validated['exclude_course_id'] ?? null
        );

        // Debug: Mostrar resultado
        \Log::info('Conflict result:', ['has_conflict' => $hasConflict]);

        if ($hasConflict) {
            return response()->json([
                'conflict' => true,
                'conflicts' => CampusCourse::getConflicts(
                    $validated['space_id'],
                    $validated['time_slot_id'],
                    $validated['exclude_course_id'] ?? null
                )
            ], 422);
        }

        return response()->json([
            'conflict' => false
        ]);
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
