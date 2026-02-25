<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Models\CampusCourse;
use App\Models\CampusTeacher;
use App\Models\CampusTeacherPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function __construct()
    {
        // Métodos CRUD para administración
        $this->middleware('can:campus.teachers.index')->only(['index']);
        $this->middleware('can:campus.teachers.create')->only(['create', 'store']);
        $this->middleware('can:campus.teachers.edit')->only(['edit', 'update']);
        $this->middleware('can:campus.teachers.delete')->only(['destroy']);
        $this->middleware('can:campus.teachers.view')->only(['show']);
        
        // Métodos para profesores autenticados (mantener existentes)
        $this->middleware('auth')->only(['courses', 'showCourse', 'students']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CampusTeacher::with(['user', 'courses']);
        
        // Filtro por nombre
        if ($request->filled('search_name')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search_name . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search_name . '%')
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $request->search_name . '%']);
            });
        }
        
        // Filtro por email
        if ($request->filled('search_email')) {
            $query->where('email', 'like', '%' . $request->search_email . '%');
        }
        
        // Filtro por teléfono
        if ($request->filled('search_phone')) {
            $query->where('phone', 'like', '%' . $request->search_phone . '%');
        }
        
        // Filtro por DNI
        if ($request->filled('search_dni')) {
            $query->where('dni', 'like', '%' . $request->search_dni . '%')
                  ->orWhere('fiscal_id', 'like', '%' . $request->search_dni . '%');
        }
        
        // Filtro por ciudad
        if ($request->filled('search_city')) {
            $query->where('city', 'like', '%' . $request->search_city . '%');
        }
        
        // Filtro por estado
        if ($request->filled('search_status')) {
            $query->where('status', $request->search_status);
        }
        
        // Filtro por cursos asignados
        if ($request->filled('search_courses_min')) {
            $query->withCount(['courses as courses_count'])
                  ->having('courses_count', '>=', $request->search_courses_min);
        }
        
        if ($request->filled('search_courses_max')) {
            if ($request->filled('search_courses_min')) {
                $query->having('courses_count', '<=', $request->search_courses_max);
            } else {
                $query->withCount(['courses as courses_count'])
                      ->having('courses_count', '<=', $request->search_courses_max);
            }
        }
        
        // Filtro por fecha de contratación
        if ($request->filled('search_date_from')) {
            $query->whereDate('hiring_date', '>=', $request->search_date_from);
        }
        
        if ($request->filled('search_date_to')) {
            $query->whereDate('hiring_date', '<=', $request->search_date_to);
        }
        
        // Filtro por especialización/áreas
        if ($request->filled('search_specialization')) {
            $query->where('specialization', 'like', '%' . $request->search_specialization . '%')
                  ->orWhere('areas', 'like', '%' . $request->search_specialization . '%');
        }
        
        // Ordenamiento
        $sortBy = $request->get('sort_by', 'last_name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['first_name', 'last_name', 'email', 'hiring_date', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } elseif ($sortBy === 'courses_count') {
            $query->withCount(['courses'])->orderBy('courses_count', $sortOrder);
        }
        
        $teachers = $query->paginate(15)->withQueryString();
        
        // Obtener valores únicos para filtros
        $cities = CampusTeacher::whereNotNull('city')->distinct()->pluck('city')->sort();
        $specializations = CampusTeacher::whereNotNull('specialization')->distinct()->pluck('specialization')->sort();
        
        return view('campus.teachers.index', compact('teachers', 'cities', 'specializations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = CampusCourse::orderBy('title')->get();
        return view('campus.teachers.create', compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('=== STORE START TeacherController ===');
        \Log::info('Request data:', $request->all());
        
        try {
            $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'dni' => 'nullable|string|max:20',
            'iban' => 'nullable|string|max:34',
            'bank_titular' => 'nullable|string|max:255',
            'fiscal_situation' => 'nullable|string|in:autonomo,empleat,pensionista,altres',
            'degree' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'areas' => 'nullable|string',
            'hiring_date' => 'nullable|date',
            'status' => 'nullable|string|in:active,inactive,on_leave',
            'courses' => 'nullable|array',
            'courses.*' => 'nullable|string|in:new',
            'hours_assigned' => 'nullable|array',
            'hours_assigned.*' => 'nullable|integer|min:1',
            'role' => 'nullable|array',
            'role.*' => 'nullable|string|in:teacher,assistant',
        ]);
        
        // Validación adicional para nuevos cursos (simplificada)
        $newCourseRules = [
            'new_course_title' => 'nullable|array',
            'new_course_title.*' => 'required_with:new_course_code.*|string|max:255',
            'new_course_code' => 'nullable|array',
            'new_course_code.*' => 'required_with:new_course_title.*|string|max:50|unique:campus_courses,code',
        ];
        
        // Personalizar mensajes de error para nuevos cursos
        $newCourseMessages = [
            'new_course_code.*.unique' => 'El codi de curs ":input" ja existeix. Si us plau, utilitza un altre codi.',
            'new_course_title.*.required_with' => 'El nom del curs és obligatori quan s\'introdueix un codi.',
            'new_course_code.*.required_with' => 'El codi del curs és obligatori quan s\'introdueix un nom.',
        ];
        
        $newCourseValidated = $request->validate($newCourseRules, $newCourseMessages);
        
        // Validación adicional: verificar si el título ya existe (advertencia, no error)
        if (!empty($newCourseValidated['new_course_title'])) {
            foreach ($newCourseValidated['new_course_title'] as $index => $title) {
                if (!empty($title)) {
                    $existingCourse = \App\Models\CampusCourse::where('title', $title)->first();
                    if ($existingCourse) {
                        \Log::warning('Course title already exists:', ['title' => $title, 'existing_id' => $existingCourse->id]);
                        // No lanzar error, solo registrar advertencia
                    }
                }
            }
        }
            
            \Log::info('Validation passed:', $validated);
            
            try {
                // Procesar nuevos cursos primero (versión simplificada)
                $newCourses = [];
                if (!empty($newCourseValidated['new_course_title'])) {
                    \Log::info('Processing new courses...');
                    $newCourseTitles = $newCourseValidated['new_course_title'];
                    $newCourseCodes = $newCourseValidated['new_course_code'];
                    
                    // Obtener temporada actual
                    $currentSeason = \App\Models\CampusSeason::where('is_current', true)->first();
                    $seasonId = $currentSeason ? $currentSeason->id : 1;
                    
                    foreach ($newCourseTitles as $index => $title) {
                        if (!empty($title) && !empty($newCourseCodes[$index])) {
                            \Log::info('Creating new course:', ['title' => $title, 'code' => $newCourseCodes[$index]]);
                            
                            // Crear nuevo curso con datos automáticos
                            $newCourse = \App\Models\CampusCourse::create([
                                'season_id' => $seasonId, // Usar temporada actual
                                'category_id' => null, // Sin categoría por defecto
                                'code' => $newCourseCodes[$index],
                                'title' => $title,
                                'slug' => \Str::slug($title) . '-' . time(), // Asegurar unicidad
                                'description' => 'Curs creat automàticament des de l\'assignació de professor', // Descripción automática
                                'credits' => 1,
                                'hours' => $validated['hours_assigned'][$index] ?? 20, // Usar horas asignadas como horas totales del curso
                                'max_students' => 30,
                                'price' => 100.00,
                                'level' => 'beginner', // Nivel por defecto
                                'start_date' => $currentSeason ? $currentSeason->season_start : now()->format('Y-m-d'), // Usar fecha de temporada
                                'end_date' => $currentSeason ? $currentSeason->season_end : now()->addMonths(3)->format('Y-m-d'), // Usar fecha de temporada
                                'is_active' => true,
                                'is_public' => true,
                            ]);
                            
                            $newCourses[$index] = $newCourse->id;
                            \Log::info('New course created:', ['course_id' => $newCourse->id]);
                        }
                    }
                }
                
                // Reemplazar 'new' en el array de cursos con los IDs de los nuevos cursos y validar cursos existentes
                if (!empty($validated['courses'])) {
                    foreach ($validated['courses'] as $index => $courseId) {
                        if ($courseId === 'new' && isset($newCourses[$index])) {
                            $validated['courses'][$index] = $newCourses[$index];
                        } elseif ($courseId !== 'new' && $courseId !== null) {
                            // Validar que el curso existente realmente existe
                            if (!\App\Models\CampusCourse::find($courseId)) {
                                throw new \Exception("El curso seleccionado no existe");
                            }
                        }
                    }
                }
                
                // Generar código de profesor único
                $teacherCode = 'PROF_' . str_pad(CampusTeacher::count() + 1, 4, '0', STR_PAD_LEFT);
                \Log::info('Teacher code generated:', ['code' => $teacherCode]);

                // Crear usuario
                Log::info('Creating user...');
                $user = \App\Models\User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')), // Contraseña temporal
        ]);
        
        // Asignar rol de profesor
        Log::info('Assigning teacher role to user:', ['user_id' => $user->id]);
        $user->assignRole('teacher');

        // Crear profesor
        \Log::info('Creating teacher record...');
        $teacher = CampusTeacher::create([
            'user_id' => $user->id,
            'teacher_code' => $teacherCode,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'city' => $validated['city'] ?? null,
            'dni' => $validated['dni'] ?? null,
            'iban' => $validated['iban'] ?? null,
            'bank_titular' => $validated['bank_titular'] ?? null,
            'fiscal_situation' => $validated['fiscal_situation'] ?? null,
            'degree' => $validated['degree'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
            'title' => $validated['title'] ?? null,
            'areas' => $validated['areas'] ?? null,
            'hiring_date' => $validated['hiring_date'] ?? now()->format('Y-m-d'),
            'status' => $validated['status'] ?? 'active',
        ]);

        \Log::info('Teacher created successfully:', ['teacher_id' => $teacher->id]);

        // Asignar cursos si se proporcionaron
        if (!empty($validated['courses'])) {
            // Filtrar cursos que no sean null
            $validCourses = array_filter($validated['courses'], function($courseId) {
                return $courseId !== null;
            });
            
            if (!empty($validCourses)) {
                \Log::info('Assigning courses:', ['courses' => $validCourses]);
                foreach ($validCourses as $index => $courseId) {
                    $teacher->courses()->attach($courseId, [
                        'hours_assigned' => $validated['hours_assigned'][$index] ?? 0,
                        'role' => $validated['role'][$index] ?? 'teacher',
                    ]);
                }
            }
        }

        \Log::info('Redirecting to teacher show page...');
            return redirect()->route('campus.teachers.show', $teacher)
                ->with('success', 'Professor creat correctament.');
                
        } catch (\Exception $e) {
            \Log::error('Error creating teacher:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el profesor: ' . $e->getMessage());
        }
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('General error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error general: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CampusTeacher $teacher)
    {
        \Log::info('=== SHOW START TeacherController ===');
        \Log::info('Showing teacher:', ['teacher_id' => $teacher->id]);
        
        $teacher->load(['user', 'courses' => function($query) {
            $query->with(['season', 'category']);
        }]);
        
        return view('campus.teachers.show', compact('teacher'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CampusTeacher $teacher)
    {
        $teacher->load(['courses']);
        $courses = CampusCourse::orderBy('title')->get();
        
        // Verificar si hay pagos confirmados para los cursos asignados
        $restrictedCourses = [];
        foreach ($teacher->courses as $course) {
            $currentSeason = $course->season_id ?? null;
            if ($currentSeason) {
                $payment = CampusTeacherPayment::where('teacher_id', $teacher->id)
                    ->where('course_id', $course->id)
                    ->where('season_id', $currentSeason)
                    ->whereNotNull('payment_option')
                    ->first();
                
                if ($payment) {
                    $restrictedCourses[] = $course->id;
                }
            }
        }
        
        return view('campus.teachers.edit', compact('teacher', 'courses', 'restrictedCourses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CampusTeacher $teacher)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($teacher->user_id),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'dni' => 'nullable|string|max:20',
            'iban' => 'nullable|string|max:34',
            'bank_titular' => 'nullable|string|max:255',
            'fiscal_id' => 'nullable|string|max:20',
            'fiscal_situation' => 'nullable|string|max:50',
            'degree' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:50',
            'areas' => 'nullable|string|max:1000',
            'hiring_date' => 'nullable|date',
            'status' => 'nullable|string|in:active,inactive,on_leave',
            'courses' => 'nullable|array',
            'courses.*' => 'nullable|string|in:new',
            'hours_assigned' => 'nullable|array',
            'hours_assigned.*' => 'nullable|integer|min:1',
            'role' => 'nullable|array',
            'role.*' => 'nullable|string|in:teacher,assistant',
        ]);

        // Actualizar usuario
        $teacher->user->update([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
        ]);

        // Actualizar profesor
        $teacher->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'city' => $validated['city'] ?? null,
            'dni' => $validated['dni'] ?? null,
            'iban' => $validated['iban'] ?? null,
            'bank_titular' => $validated['bank_titular'] ?? null,
            'fiscal_id' => $validated['fiscal_id'] ?? null,
            'fiscal_situation' => $validated['fiscal_situation'] ?? null,
            'degree' => $validated['degree'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
            'title' => $validated['title'] ?? null,
            'areas' => $validated['areas'] ? explode(',', str_replace(' ', '', $validated['areas'])) : null,
            'hiring_date' => $validated['hiring_date'] ?? now()->format('Y-m-d'),
            'status' => $validated['status'] ?? 'active',
        ]);

        // Verificar cursos con pagos confirmados y excluirlos de la actualización
        $restrictedCourses = [];
        if (!empty($validated['courses'])) {
            foreach ($validated['courses'] as $index => $courseId) {
                $course = CampusCourse::find($courseId);
                if ($course) {
                    $currentSeason = $course->season_id ?? null;
                    if ($currentSeason) {
                        $payment = CampusTeacherPayment::where('teacher_id', $teacher->id)
                            ->where('course_id', $courseId)
                            ->where('season_id', $currentSeason)
                            ->whereNotNull('payment_option')
                            ->first();
                        
                        if ($payment) {
                            $restrictedCourses[] = $courseId;
                        }
                    }
                }
            }
        }

        // Sincronizar cursos solo los que no están restringidos
        if (!empty($validated['courses'])) {
            $syncData = [];
            foreach ($validated['courses'] as $index => $courseId) {
                if (!in_array($courseId, $restrictedCourses)) {
                    $syncData[$courseId] = [
                        'hours_assigned' => $validated['hours_assigned'][$index] ?? 0,
                        'role' => $validated['role'][$index] ?? 'teacher',
                    ];
                }
            }
            
            // Obtener cursos actuales que no están restringidos
            $currentUnrestrictedCourses = $teacher->courses()
                ->whereNotIn('campus_courses.id', $restrictedCourses)
                ->pluck('campus_courses.id')
                ->toArray();
            
            // Sincronizar solo cursos no restringidos
            $teacher->courses()->sync($syncData);
            
            // Mantener cursos restringidos sin cambios
            foreach ($restrictedCourses as $restrictedCourseId) {
                if (!$teacher->courses()->where('campus_courses.id', $restrictedCourseId)->exists()) {
                    // Si por alguna razón no existe, volver a añadirlo con datos originales
                    $originalPivot = $teacher->courses()
                        ->withPivot(['hours_assigned', 'role'])
                        ->where('campus_courses.id', $restrictedCourseId)
                        ->first();
                    
                    if ($originalPivot) {
                        $teacher->courses()->attach($restrictedCourseId, [
                            'hours_assigned' => $originalPivot->pivot->hours_assigned,
                            'role' => $originalPivot->pivot->role,
                        ]);
                    }
                }
            }
        } else {
            // Si no se envían cursos, eliminar solo los no restringidos
            $teacher->courses()->whereNotIn('campus_courses.id', $restrictedCourses)->detach();
        }

        return redirect()->route('campus.teachers.show', $teacher)
            ->with('success', 'Professor actualitzat correctament.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CampusTeacher $teacher)
    {
        // Eliminar usuario asociado
        $teacher->user->delete();
        
        // Eliminar profesor
        $teacher->delete();

        return redirect()->route('campus.teachers.index')
            ->with('success', 'Professor eliminat correctament.');
    }

    /**
     * Mostrar los cursos del profesor
     */
    public function courses()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(403, 'No tienes un perfil de profesor');
        }
        
        // Obtener cursos del profesor para la temporada actual
        $courses = $teacher->courses()
            ->with(['season', 'category'])
            ->withCount([
                'registrations as confirmed_students_count' => function ($query) {
                    $query->whereIn('status', ['confirmed', 'completed']);
                }
            ])
            ->latest()
            ->get();
        
        return view('campus.teacher.courses', compact('courses', 'teacher'));
    }
    
    /**
     * Mostrar detalles de un curso específico
     */
    public function showCourse($courseId)
    {
        $course = CampusCourse::with(['season', 'category', 'teachers'])->findOrFail($courseId);
        
        $this->authorizeCourse($course);
        
        return view('campus.teacher.course-show', compact('course'));
    }
    
    /**
     * Mostrar estudiantes de un curso
     */
    public function students($courseId)
    {
        $course = CampusCourse::with([
            'season',
            'category',
            'registrations' => function ($query) {
                $query->whereIn('status', ['confirmed', 'completed'])
                      ->with(['student.user']);
            }
        ])->findOrFail($courseId);
        
        $this->authorizeCourse($course);
        
        $students = $course->registrations->map(function ($registration) {
            return [
                'id' => $registration->student->id,
                'name' => $registration->student->user->name ?? 
                         ($registration->student->first_name . ' ' . $registration->student->last_name),
                'email' => $registration->student->email ?? $registration->student->user->email,
                'student_code' => $registration->student->student_code,
                'registration_date' => $registration->registration_date,
                'status' => $registration->status,
                'registration_id' => $registration->id
            ];
        });
        return view('campus.teacher.students', compact('course', 'students'));
    }
    
    /**
     * Autorizar que el profesor tenga acceso al curso
     */
    private function authorizeCourse(CampusCourse $course)
    {
        $teacher = Auth::user()->teacher;
        
        // Verificar que el usuario tenga perfil de profesor
        abort_if(!$teacher, 403, 'No tienes un perfil de profesor');
        
        // Verificar que el profesor esté asignado a este curso
        $isAssigned = $course->teachers()
            ->where('campus_teachers.id', $teacher->id)
            ->exists();
            
        abort_if(!$isAssigned, 403, 'No estás asignado a este curso');
    }

    /**
     * Descargar plantilla de importación de profesores
     */
    public function template()
    {
        $this->authorize('campus.teachers.create');
        
        $filename = 'plantilla_profesores.csv';
        
        return response()->download(
            storage_path('app/templates/plantilla_profesores.csv'),
            $filename,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]
        );
    }
}