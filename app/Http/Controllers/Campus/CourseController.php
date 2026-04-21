<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
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
        // Guardar temporada seleccionada en sesión (vida larga: 1 año)
        if ($request->filled('search_season')) {
            session(['selected_season' => $request->search_season]);
            if (!session()->has('selected_season_set_at')) {
                session(['selected_season_set_at' => now()->addYear()]);
            }
        }
        
        // Usar temporada de sesión si no hay parámetro
        $seasonFilter = $request->get('search_season', session('selected_season'));
        
        $query = CampusCourse::with(['season', 'category', 'teachers' => function($query) {
            $query->select('campus_teachers.id', 'campus_teachers.teacher_code', 'campus_teachers.first_name', 'campus_teachers.last_name')
                  ->withPivot('role');
        }]);
        
        // Filtro por código
        if ($request->filled('search_code')) {
            $searchCode = $request->search_code;
            $query->where('code', 'like', '%' . $searchCode . '%');
        }
        
        // Filtro por título
        if ($request->filled('search_title')) {
            $query->where('title', 'like', '%' . $request->search_title . '%');
        }
        
        // Filtro por temporada (usar sesión si no hay parámetro)
        if ($seasonFilter) {
            $query->where('season_id', $seasonFilter);
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
        
        
       // Columnes permeses per ordenar
    $allowedColumns = ['code', 'title', 'category', 'season', 'dates'];

    // Mapeig de columnes a camps de la base de dades
        $columnMapping = [
            'code' => 'code',
            'title' => 'title',
            'category' => 'category_id',
            'season' => 'season_id',
            'dates' => 'start_date'
        ];
                
        // Verificar que la columna sigui permesa
        if (in_array($sortBy, $allowedColumns)) {
            $dbColumn = $columnMapping[$sortBy];
                    
            // Per a category i season, ordenar per nom
            if (in_array($sortBy, ['category', 'season'])) {
            $query->with([$sortBy => function($query) {
                $query->orderBy('name');
            }]);
        }
                
        $query->orderBy($dbColumn, $sortOrder);
    }
        
        $courses = $query->paginate(15)->withQueryString();
        
        // Obtener valores únicos para filtros según permisos de usuario
        if (auth()->user()->hasRole('admin') || auth()->user()->can('manage_seasons')) {
            // Admin/Manager: ve todas las temporadas
            $seasons = CampusSeason::withCount("courses")->orderByDesc('season_start')->get();
        } else {
            // Otros roles: solo temporadas visibles según reglas
            $seasons = CampusSeason::getVisibleForUser()->withCount("courses")->orderByDesc('season_start')->get();
        }
        $categories = CampusCategory::orderBy('name')->get();
        $levels = ['beginner', 'intermediate', 'advanced'];
        $formats = ['presencial', 'online', 'hybrid'];
        
        return view('campus.courses.index', compact('courses', 'seasons', 'categories', 'levels', 'formats'));
    }

    /**
     * Clear the saved season from session.
     */
    public function clearSeason()
    {
        session()->forget(['selected_season', 'selected_season_set_at']);
        
        return redirect()->route('campus.courses.index')
            ->with('success', 'Temporada guardada eliminada correctament.');
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(Request $request)
    {
        // Comprovar si venim des del botó "Crear Instància"
        $parentId = $request->get('parent_id');
        $parentCourse = null;
        
        if ($parentId) {
            $parentCourse = CampusCourse::find($parentId);
            if (!$parentCourse) {
                return redirect()
                    ->route('campus.courses.index')
                    ->with('error', 'Curs pare no vàlid');
            }
        }
        
        // Obtenir totes les temporades per al select
        if (auth()->user()->hasRole('superadmin')) {
            $seasons = CampusSeason::orderBy('season_start', 'desc')->get();
        } else {
            $seasons = CampusSeason::getVisibleForUser()->withCount("courses")->orderByDesc('season_start')->get();
        }
        
        $categories = CampusCategory::orderBy('name')->get();
        
        // Pre-omplir dades per a instàncies
        $defaultData = [];
        if ($parentCourse) {
            // Generar codi d'instància (següent número de la mateixa família)
            $instanceCode = $this->generateInstanceCode($parentCourse->code);
            
            // Comptar instàncies existents per determinar el número d'edició
            $instanceCount = CampusCourse::where('parent_id', $parentCourse->id)->count();
            $editionNumber = $instanceCount + 1;
            
            // Obtenir temporada seleccionada (per defecte l'actual)
            $selectedSeasonId = $parentCourse->season_id;
            $selectedSeason = CampusSeason::find($selectedSeasonId);
            
            $defaultData = [
                'code' => $instanceCode,
                'season_id' => $selectedSeasonId,
                'category_id' => $parentCourse->category_id,
                'title' => $parentCourse->title,
                'description' => $parentCourse->description,
                'start_date' => $selectedSeason?->season_start?->format('Y-m-d'),
                'end_date' => $selectedSeason?->season_end?->format('Y-m-d'),
                'price' => $parentCourse->price,
                'level' => $parentCourse->level,
                'location' => $parentCourse->location,
                'format' => $parentCourse->format,
                'hours' => $parentCourse->hours,
                'max_students' => $parentCourse->max_students,
                'sessions' => $parentCourse->sessions ?? 15,
                'is_active' => true,
                'is_public' => true,
            ];
        }
        
        return view('campus.courses.create', compact('seasons', 'categories', 'parentCourse', 'defaultData'));
    }
    
    /**
     * Generate instance code based on parent course code
     */
    public function generateInstanceCode($parentCode)
    {
        // Extreure les lletres del codi pare (ex: PA de PA-001)
        $letters = substr($parentCode, 0, strpos($parentCode, '-'));
        
        // Buscar el màxim número existent per aquestes lletres
        $lastCode = CampusCourse::where('code', 'like', $letters . '-%')
            ->orderByRaw('CAST(SUBSTRING(code, -3) AS UNSIGNED) DESC')
            ->value('code');
        
        // Calcular el següent número
        $nextNumber = $lastCode ? intval(substr($lastCode, -3)) + 1 : 1;
        $suffix = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        return $letters . '-' . $suffix;
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $this->validatedData($request);

            // Aplicar valors per defecte si són null
            $data = $this->applyDefaultValues($data);

            // Generar codi automàticament
            if (empty($data['code'])) {
                $data['code'] = \App\Models\CampusCourse::generateCourseCode($data['title']);
            }
            
            // Verificar que el codi sigui únic
            $existingCourse = \App\Models\CampusCourse::where('code', $data['code'])->first();
            if ($existingCourse) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['code' => "El codi '{$data['code']}' ja existeix. Si us plau, selecciona un altre títol o introdueix un codi diferent."]);
            }

            $course = CampusCourse::create($data);

            return redirect()
                ->route('campus.courses.show', $course)
                ->with('success', __('campus.course_created'));
                
        } catch (\Exception $e) {
            \Log::error('Error creant curs: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error creant el curs: ' . $e->getMessage()]);
        }
    }

    /**
     * Aplicar valors per defecte als camps nullable
     */
    private function applyDefaultValues(array $data): array
    {
        // Valors per defecte segons lògica de negoci
        $defaults = [
            'credits' => 1,           // Mínim 1 crèdit ECTS
            'hours' => 25,             // Mínim 25 hores
            'sessions' => 15,          // Mínim 15 sessions
            'max_students' => 20,       // Mínim 20 alumnes
            'price' => 0,              // Gratuït per defecte
            'level' => 'beginner',      // Nivell principiant per defecte
            'is_active' => true,        // Actiu per defecte
            'is_public' => true,        // Públic per defecte
        ];
        
        // Per instàncies, si no tenim season_id, agafar el de la sessió actual
        if (!isset($data['season_id']) || $data['season_id'] === null) {
            $currentSeason = \App\Models\CampusSeason::where('is_current', true)->first();
            if ($currentSeason) {
                $defaults['season_id'] = $currentSeason->id;
            }
        }
        
        // Aplicar valors per defecte només si són null
        foreach ($defaults as $field => $default) {
            if (!isset($data[$field]) || $data[$field] === null || $data[$field] === '') {
                $data[$field] = $default;
            }
        }

        return $data;
    }

    /**
     * Display the specified course.
     */
    public function show(CampusCourse $course, Request $request)
    {
        $course->load(['season', 'category']);
        
        // Capturar la URL de referencia para volver al listado con filtros
        // Prioridad: 1) back_url explícito, 2) referer HTTP, 3) ruta por defecto
        $backUrl = $request->get('back_url');
        
        if (!$backUrl && $request->header('referer')) {
            $referer = $request->header('referer');
            // Solo usar referer si viene de nuestro dominio y es la página de cursos
            if (strpos($referer, route('campus.courses.index')) !== false) {
                $backUrl = $referer;
            }
        }
        
        // Fallback a ruta por defecto
        if (!$backUrl) {
            $backUrl = route('campus.courses.index');
        }

        return view('campus.courses.show', compact('course', 'backUrl'));
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
    public function edit(CampusCourse $course, Request $request)
    {
        // Obtener temporadas según permisos
        if (auth()->user()->hasRole('admin') || auth()->user()->can('manage_seasons')) {
            $seasons = CampusSeason::withCount("courses")->orderByDesc('season_start')->get();
        } else {
            $seasons = CampusSeason::getVisibleForUser()->withCount("courses")->orderByDesc('season_start')->get();
        }
        
        $categories = CampusCategory::orderBy('name')->get();
        
        // Capturar la URL de referencia para volver al listado con filtros
        $backUrl = $request->get('back_url', route('campus.courses.index'));

        return view('campus.courses.edit', compact('course', 'seasons', 'categories', 'backUrl'));
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, CampusCourse $course)
    {
        $data = $this->validatedData($request, $course);

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

        $course->update($data);
        
        // Debug: Log después de actualizar
        \Log::info('=== COURSE UPDATE RESULT ===');
        \Log::info('Course updated successfully: ' . json_encode([
            'course_id' => $course->id,
            'final_code' => $course->fresh()->code,
            'data_saved' => $data
        ]));
        \Log::info('=== COURSE UPDATE END ===');
        
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
        $backUrl = $request->get('back_url', route('campus.courses.index'));
        
        return redirect($backUrl)
            ->with('success', __('campus.course_updated'));
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(CampusCourse $course, Request $request)
    {
        $course->delete();

        $backUrl = $request->get('back_url', route('campus.courses.index'));
        
        return redirect($backUrl)
            ->with('success', __('campus.course_deleted'));
    }

    /**
     * Validation rules shared by store & update.
     */
    protected function validatedData(Request $request, CampusCourse $course = null): array
    {
        // Debug: Mostrar todos los datos recibidos antes de validar
        \Log::info('=== COURSE EDIT DEBUG START ===');
        \Log::info('Course ID: ' . ($course->id ?? 'new'));
        \Log::info('Is Base Course: ' . ($course ? ($course->isBaseCourse() ? 'YES' : 'NO') : 'NEW COURSE'));
        \Log::info('Current Code: ' . ($course->code ?? 'NONE'));
        \Log::info('Request received:', [
            'all_data' => $request->all(),
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type')
        ]);
        
        $data = $request->validate([
            'season_id'     => ['required', 'exists:campus_seasons,id'],
            'category_id'   => ['nullable', 'exists:campus_categories,id'],
            'code' => [
                'nullable', 
                'string', 
                'max:50', 
                Rule::unique("campus_courses", "code")->ignore($course?->id)->where(function ($query) use ($course) {
                    // Para cursos base, permitir mantener su código original sin validación de unicidad
                    if ($course && $course->isBaseCourse()) {
                        return $query->whereRaw('0 = 1'); // Condición que nunca es verdadera
                    }
                    return $query;
                })
            ],
            'title'         => ['required', 'string', 'max:255'],
            'slug'          => ['nullable', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'hours'         => ['nullable', 'integer', 'min:0', 'max:1000'],
            'sessions'      => ['nullable', 'integer', 'min:0', 'max:100'],
            'max_students'  => ['nullable', 'integer', 'min:0'],
            'price'         => ['nullable', 'numeric', 'min:0'],
            'level'         => ['nullable', 'string', 'in:none,beginner,intermediate,advanced,expert'],
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
            'parent_id'     => ['nullable', 'exists:campus_courses,id'],
        ]);
        
        // Debug: Mostrar todos los datos recibidos y validados
        \Log::info('Validation result: ' . json_encode([
            'validated_data' => $data,
            'code_submitted' => $data['code'] ?? 'NULL',
            'original_code' => $course->code ?? 'NONE',
            'code_changed' => ($course && isset($data['code']) && $data['code'] !== $course->code) ? 'YES' : 'NO',
            'is_base_course' => $course ? ($course->isBaseCourse() ? 'YES' : 'NO') : 'NEW',
            'course_id' => $course->id ?? 'NEW'
        ]));
        
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
     * Generate course code from title
     */
    public function generateCode(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        try {
            $code = CampusCourse::generateCourseCode($request->title);
            
            return response()->json([
                'success' => true,
                'code' => $code
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
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
