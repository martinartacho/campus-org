<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CampusSpace;
use App\Models\CampusTimeSlot;
use App\Models\CampusCourseSchedule;
use App\Models\CampusTeacherSchedule;
use App\Models\CampusCourse;
use App\Models\CampusTeacher;
use App\Models\CampusSeason;

class ResourceController extends Controller
{
    public function index()
    {
        return view('campus.resources.index');
    }
    
    public function calendar(Request $request)
    {
        $semester = $request->get('semester', '1Q');
        
        // Usar el nuevo método para obtener la temporada por defecto
        $selectedSeason = CampusSeason::getDefaultForCalendar();
        $selectedSeasonSlug = $selectedSeason ? $selectedSeason->slug : null;
        
        // Si hay temporada, filtrar por temporada, si no, usar semestre por defecto
        if ($selectedSeason) {
            // Filtrar horarios por temporada usando cursos asignados directamente
            $timeSlots = CampusTimeSlot::with(['courses' => function($query) use ($selectedSeason) {
                $query->where('season_id', $selectedSeason->id)
                      ->whereNotNull('space_id')
                      ->whereNotNull('time_slot_id')
                      ->with(['space']);
            }])
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
            
            // Contar todos los cursos de la temporada
            $coursesCount = \App\Models\CampusCourse::where('season_id', $selectedSeason->id)->count();
            
            \Log::info('Default Season: ID=' . $selectedSeason->id . ', Name="' . $selectedSeason->name . '", Status="' . $selectedSeason->status . '", Courses=' . $coursesCount);
        } else {
            // Sin temporada, usar semestre
            $timeSlots = CampusTimeSlot::with(['courses' => function($query) use ($semester) {
                $query->where('semester', $semester)
                      ->whereNotNull('space_id')
                      ->whereNotNull('time_slot_id')
                      ->with(['space']);
            }])
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
            
            $coursesCount = 0;
            \Log::warning('No season found for calendar');
        }
        
        $spaces = CampusSpace::where('is_active', true)
            ->orderBy('type')
            ->orderBy('capacity', 'desc')
            ->get();
            
        return view('campus.resources.calendar', compact('timeSlots', 'spaces', 'semester', 'selectedSeason', 'coursesCount'));
    }
    
    public function calendarQuarterly(Request $request)
    {
        $semester = $request->get('semester', '1Q');
        $selectedSeason = CampusSeason::getDefaultForCalendar();
        
        // Obtenir el rang de dates del quadrimestre (4 mesos)
        if ($selectedSeason && $selectedSeason->start_date && $selectedSeason->end_date) {
            $startDate = $selectedSeason->start_date;
            $endDate = $selectedSeason->end_date;
        } else {
            $startDate = now()->startOfMonth();
            $endDate = now()->copy()->addMonths(3)->endOfMonth();
        }
        
        // Obtenir tots els horaris del quadrimestre
        $schedules = CampusCourseSchedule::with(['course', 'space', 'timeSlot'])
            ->whereHas('course', function($query) use ($selectedSeason) {
                if ($selectedSeason) {
                    $query->where('season_id', $selectedSeason->id);
                }
            })
            ->whereBetween('start_date', [$startDate, $endDate])
            ->orderBy('start_date')
            ->get();
            
        // Agrupar per mes per a la vista quadrimestral
        $monthlySchedules = $schedules->groupBy(function($schedule) {
            return \Carbon\Carbon::parse($schedule->start_date)->format('Y-m');
        });
        
        // Obtenir espais i cursos per als filtres
        $spaces = CampusSpace::where('is_active', true)
            ->orderBy('type')
            ->orderBy('capacity', 'desc')
            ->get();
            
        $courses = CampusCourse::where('season_id', $selectedSeason->id ?? null)
            ->orderBy('title')
            ->get();
            
        return view('campus.resources.calendar-quarterly', compact(
            'monthlySchedules', 
            'spaces', 
            'courses', 
            'selectedSeason',
            'startDate',
            'endDate'
        ));
    }
    
    public function calendarMonthly(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $currentMonth = \Carbon\Carbon::createFromFormat('Y-m', $month);
        $selectedSeason = CampusSeason::getDefaultForCalendar();
        
        // Obtenir el rang de dates del mes
        $startDate = $currentMonth->copy()->startOfMonth();
        $endDate = $currentMonth->copy()->endOfMonth();
        
        // Obtenir tots els horaris del mes
        $schedules = CampusCourseSchedule::with(['course', 'space', 'timeSlot'])
            ->whereHas('course', function($query) use ($selectedSeason) {
                if ($selectedSeason) {
                    $query->where('season_id', $selectedSeason->id);
                }
            })
            ->where(function($query) use ($startDate, $endDate) {
                // Buscar horaris que overlapin amb el mes actual
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->orderBy('start_date')
            ->get();
            
        // Agrupar per dia per a la vista mensual
        $monthlySchedules = $schedules->groupBy(function($schedule) {
            return \Carbon\Carbon::parse($schedule->start_date)->format('Y-m-d');
        });
        
        // Obtenir espais i cursos per als filtres
        $spaces = CampusSpace::where('is_active', true)
            ->orderBy('type')
            ->orderBy('capacity', 'desc')
            ->get();
            
        $courses = CampusCourse::where('season_id', $selectedSeason->id ?? null)
            ->orderBy('title')
            ->get();
            
        return view('campus.resources.calendar-monthly', compact(
            'monthlySchedules', 
            'spaces', 
            'courses', 
            'selectedSeason',
            'currentMonth',
            'startDate',
            'endDate'
        ));
    }
    
    public function calendarMonthlyBootstrap(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $currentMonth = \Carbon\Carbon::createFromFormat('Y-m', $month);
        $selectedSeason = CampusSeason::getDefaultForCalendar();
        
        // Obtenir el rang de dates del mes
        $startDate = $currentMonth->copy()->startOfMonth();
        $endDate = $currentMonth->copy()->endOfMonth();
        
        // Obtenir cursos de la temporada
        $courses = CampusCourse::where('season_id', $selectedSeason->id ?? null)
            ->orderBy('title')
            ->get();
        // Generar horaris setmanals per cursos que no en tenen
        foreach ($courses as $course) {
            $this->generateWeeklySchedules($course);
        }
        
        // Obtenir cursos amb agenda JSON
        $courses = CampusCourse::where('season_id', $selectedSeason->id ?? null)
            ->whereNotNull('schedule')
            ->where('schedule', '!=', '[]')
            ->with(['space', 'timeSlot'])
            ->orderBy('title')
            ->get();

        // Obtenir totes les franges horaries disponibles
        $allTimeSlots = \App\Models\CampusTimeSlot::where('is_active', true)
            ->orderBy('start_time')
            ->get()
            ->groupBy('start_time');
        
        // Processar agenda JSON per agrupar per dia
        $monthlySchedules = collect();
        
        foreach ($courses as $course) {
            if ($course->schedule && is_array($course->schedule)) {
                foreach ($course->schedule as $session) {
                    $sessionDate = $session['date'];
                    $sessionTime = $session['time'];
                    
                    // Només sessions dins del mes actual
                    if ($sessionDate >= $startDate->format('Y-m-d') && 
                        $sessionDate <= $endDate->format('Y-m-d')) {
                        
                        if (!$monthlySchedules->has($sessionDate)) {
                            $monthlySchedules->put($sessionDate, collect());
                        }
                        
                        $monthlySchedules->get($sessionDate)->push([
                            'course' => $course,
                            'session' => $session,
                            'space' => $course->space,
                            'timeSlot' => $course->timeSlot
                        ]);
                    }
                }
            }
        }
        
        // No afegir franges buides - només mostrar cursos assignats
        // Les franges lliures no calen segons feedback de l'usuari
        
        // Ordenar sessions per hora
        $monthlySchedules = $monthlySchedules->map(function($daySchedules) {
            return $daySchedules->sortBy('session.time')->values();
        });    
        
        // Obtenir dies no lectius del mes
        $nonLectiveDays = \App\Models\CampusNonLectiveDay::getInRange($startDate, $endDate);
        
        // Normalitzar dates a format Y-m-d (sense hora) per comparació
        $nonLectiveDays = array_map(function($date) {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        }, $nonLectiveDays);
        
        // Obtenir espais per als filtres
        $spaces = CampusSpace::where('is_active', true)
            ->orderBy('type')
            ->orderBy('capacity', 'desc')
            ->get();
            
        return view('campus.resources.calendar-monthly-bootstrap', compact(
            'monthlySchedules', 
            'spaces', 
            'courses', 
            'selectedSeason',
            'currentMonth',
            'startDate',
            'endDate',
            'nonLectiveDays'
        ));
    }
    
    /**
     * Genera horaris setmanals per a cursos que no en tenen
     */
    private function generateWeeklySchedules($course)
    {
        // Si el curs ja té agenda, no generar
        if ($course->schedule && !empty($course->schedule)) {
            return;
        }
        
        // Obtenir el time_slot per saber quin dia de la setmana i hora
        $timeSlot = $course->timeSlot;
        if (!$timeSlot) {
            return;
        }
        
        // Assegurar que timeSlot és un objecte, no una col·lecció
        if (is_object($timeSlot)) {
            $dayOfWeek = $timeSlot->day_of_week; // 1 = Dilluns, 7 = Diumenge
            $startTime = $timeSlot->start_time;
        } else {
            // Si és una col·lecció, obtenir el primer element
            $timeSlot = $timeSlot->first();
            if (!$timeSlot) {
                return;
            }
            $dayOfWeek = $timeSlot->day_of_week;
            $startTime = $timeSlot->start_time;
        }
        
        // Generar totes les sessions
        $sessions = $course->sessions ?? $course->hours ?? 1;
        $currentDate = $course->start_date->copy();
        $agenda = [];
        
        for ($i = 0; $i < $sessions; $i++) {
            // Trobar el primer dia de la setmana correcte
            while ($currentDate->dayOfWeekIso != $dayOfWeek) {
                $currentDate->addDay();
            }
            
            // Si ens passem de la data de finalització, aturar
            if ($currentDate->gt($course->end_date)) {
                break;
            }
            
            // VALIDAR: Comprovar si el dia és no lectiu
            $currentDateStr = $currentDate->format('Y-m-d');
            if (\App\Models\CampusNonLectiveDay::isNonLective($currentDateStr)) {
                // Ometre aquesta sessió perquè és dia no lectiu
                $currentDate->addWeek();
                continue;
            }
            
            // Afegir sessió a l'agenda
            $agenda[] = [
                'date' => $currentDate->format('Y-m-d'),
                'time' => $startTime->format('H:i'),
                'day_of_week' => $dayOfWeek,
                'space_id' => $course->space_id,
                'time_slot_id' => $course->time_slot_id,
                'skipped_non_lective' => false // Marcar que no s'ha omès
            ];
            
            // Avançar una setmana per a la propera sessió
            $currentDate->addWeek();
        }
        
        // Afegir informació de sessions omeses
        $totalSessions = $course->sessions ?? $course->hours ?? 1;
        $skippedSessions = $totalSessions - count($agenda);
        
        if ($skippedSessions > 0) {
            // Opcional: Pots afegir un log o notificació aquí
            \Log::info("Curs {$course->code}: {$skippedSessions} sessions omeses per dies no lectius");
        }
        
        // Guardar agenda al camp schedule del curs
        $course->schedule = $agenda;
        $course->save();
    }
    
    /**
     * Marcar/desmarcar dia no lectiu
     */
    public function toggleNonLectiveDay(Request $request)
    {
        $date = $request->input('date');
        
        try {
            $nonLectiveDay = \App\Models\CampusNonLectiveDay::where('date', $date)->first();
            
            if ($nonLectiveDay) {
                // Si existeix, desactivar-lo
                $nonLectiveDay->delete();
                $message = 'Dia marcat com lectiu';
            } else {
                // Si no existeix, crear-lo
                \App\Models\CampusNonLectiveDay::create([
                    'date' => $date,
                    'description' => 'Dia no lectiu',
                    'is_active' => true
                ]);
                $message = 'Dia marcat com no lectiu';
            }
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Vista d'impressió millorada
     */
    public function calendarPrint(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $currentMonth = \Carbon\Carbon::createFromFormat('Y-m', $month);
        $selectedSeason = \App\Models\CampusSeason::getDefaultForCalendar();
        
        // Obtenir el rang de dates del mes
        $startDate = $currentMonth->copy()->startOfMonth();
        $endDate = $currentMonth->copy()->endOfMonth();
        
        // Obtenir cursos amb agenda JSON
        $courses = CampusCourse::where('season_id', $selectedSeason->id ?? null)
            ->whereNotNull('schedule')
            ->where('schedule', '!=', '[]')
            ->with(['space', 'timeSlot'])
            ->orderBy('title')
            ->get();

        // Processar agenda JSON per agrupar per dia
        $monthlySchedules = collect();
        
        foreach ($courses as $course) {
            if ($course->schedule && is_array($course->schedule)) {
                foreach ($course->schedule as $session) {
                    $sessionDate = $session['date'];
                    $sessionTime = $session['time'];
                    
                    // Només sessions dins del mes actual
                    if ($sessionDate >= $startDate->format('Y-m-d') && 
                        $sessionDate <= $endDate->format('Y-m-d')) {
                        
                        if (!$monthlySchedules->has($sessionDate)) {
                            $monthlySchedules->put($sessionDate, collect());
                        }
                        
                        $monthlySchedules->get($sessionDate)->push([
                            'course' => $course,
                            'session' => $session,
                            'space' => $course->space,
                            'timeSlot' => $course->timeSlot
                        ]);
                    }
                }
            }
        }
        
        // Ordenar sessions per hora
        $monthlySchedules = $monthlySchedules->map(function($daySchedules) {
            return $daySchedules->sortBy('session.time')->values();
        });
        
        // Obtenir dies no lectius del mes
        $nonLectiveDays = \App\Models\CampusNonLectiveDay::getInRange($startDate, $endDate);
        
        // Normalitzar dates a format Y-m-d (sense hora) per comparació
        $nonLectiveDays = array_map(function($date) {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        }, $nonLectiveDays);
        
        return view('campus.resources.calendar-print', compact(
            'monthlySchedules', 
            'selectedSeason',
            'currentMonth',
            'nonLectiveDays'
        ));
    }
    
    /**
     * Generar agenda per a cursos que no en tenen
     */
    public function generateAgenda(Request $request)
    {
        try {
            $selectedSeason = \App\Models\CampusSeason::getDefaultForCalendar();
            
            // Obtenir cursos sense agenda
            $courses = CampusCourse::where('season_id', $selectedSeason->id ?? null)
                ->where(function($query) {
                    $query->whereNull('schedule')
                          ->orWhere('schedule', '[]');
                })
                ->with(['space', 'timeSlot'])
                ->get();
            
            $generatedCount = 0;
            $errorCount = 0;
            $errors = [];
            
            foreach ($courses as $course) {
                try {
                    $this->generateWeeklySchedules($course);
                    $generatedCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Error amb {$course->code}: " . $e->getMessage();
                }
            }
            
            $message = "S'han generat {$generatedCount} agendes.";
            if ($errorCount > 0) {
                $message .= " Hi ha hagut {$errorCount} errors.";
                if (count($errors) <= 3) {
                    $message .= " Errors: " . implode(', ', $errors);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'generated' => $generatedCount,
                'errors' => $errorCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Regenerar agenda per a tots els cursos
     */
    public function regenerateAgenda(Request $request)
    {
        try {
            $selectedSeason = \App\Models\CampusSeason::getDefaultForCalendar();
            
            // Obtenir tots els cursos de la temporada
            $courses = CampusCourse::where('season_id', $selectedSeason->id ?? null)
                ->with(['space', 'timeSlot'])
                ->get();
            
            $regeneratedCount = 0;
            $errorCount = 0;
            $errors = [];
            
            foreach ($courses as $course) {
                try {
                    // Esborrar agenda existent
                    $course->schedule = null;
                    $course->save();
                    
                    // Generar nova agenda
                    $this->generateWeeklySchedules($course);
                    $regeneratedCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Error amb {$course->code}: " . $e->getMessage();
                }
            }
            
            // Comptar sessions omeses per dies no lectius
            $totalSkipped = 0;
            foreach ($courses as $course) {
                if ($course->schedule && is_array($course->schedule)) {
                    $totalSessions = $course->sessions ?? $course->hours ?? 1;
                    $actualSessions = count($course->schedule);
                    $totalSkipped += ($totalSessions - $actualSessions);
                }
            }
            
            $message = "S' han regenerat {$regeneratedCount} agendes.";
            if ($totalSkipped > 0) {
                $message .= " S'han omès {$totalSkipped} sessions per dies no lectius.";
            }
            if ($errorCount > 0) {
                $message .= " Hi ha hagut {$errorCount} errors.";
                if (count($errors) <= 3) {
                    $message .= " Errors: " . implode(', ', $errors);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'regenerated' => $regeneratedCount,
                'errors' => $errorCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Exportar calendari a Excel (quadrimestre complet)
     */
    public function exportCalendar(Request $request)
    {
        $seasonId = $request->get('season_id');
        
        // Obtenir temporada per al nom del fitxer
        $season = $seasonId ? 
            \App\Models\CampusSeason::find($seasonId) : 
            \App\Models\CampusSeason::getDefaultForCalendar();
            
        $seasonName = $season ? str_replace(' ', '_', $season->name) : 'complet';
        $fileName = "calendari_{$seasonName}.xlsx";
        
        return \Excel::download(new \App\Exports\CalendarExport($seasonId), $fileName);
    }
    
    /**
     * Determina el trimestre a partir d'una data
     */
    private function getSemesterFromDate($date)
    {
        $month = $date->month;
        return ($month <= 6) ? '1Q' : '2Q';
    }
    
    public function searchCourses(Request $request)
    {
        $search = $request->get('search', '');
        
        if (strlen($search) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'El terme de cerca ha de tenir al menys 2 caràcters'
            ]);
        }
        
        // Buscar cursos base (sin parent) de cualquier temporada
        $courses = CampusCourse::whereNull('parent_id')
            ->where(function($query) use ($search) {
                $query->where('code', 'like', '%' . $search . '%')
                      ->orWhere('title', 'like', '%' . $search . '%');
            })
            ->with(['season'])
            ->orderBy('title')
            ->limit(10) // Limitar resultados para mejor rendimiento
            ->get(['id', 'code', 'title', 'season_id']);
        
        $coursesData = $courses->map(function($course) {
            return [
                'id' => $course->id,
                'code' => $course->code,
                'title' => $course->title,
                'season_name' => $course->season ? $course->season->name : 'Sense temporada',
                'season_id' => $course->season_id
            ];
        });
        
        return response()->json([
            'success' => true,
            'courses' => $coursesData,
            'count' => $coursesData->count()
        ]);
    }
    
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:campus_courses,id',
            'space_id' => 'required|exists:campus_spaces,id',
            'time_slot_id' => 'required|exists:campus_time_slots,id',
            'semester' => 'required|in:1Q,2Q'
        ]);
        // dd(($request->all()));
        
        $schedule = CampusCourseSchedule::create($validated);
        
        // Detect conflicts
        $conflicts = $schedule->detectConflicts();
        if (!empty($conflicts)) {
            $schedule->status = 'conflict';
            $schedule->notes = implode(', ', $conflicts);
        } else {
            $schedule->status = 'assigned';
        }
        $schedule->save();
        
        return response()->json([
            'success' => true,
            'schedule' => $schedule->load(['course', 'space', 'timeSlot']),
            'conflicts' => $conflicts
        ]);
    }
    
    public function spaces()
    {
        $spaces = CampusSpace::with('courseSchedules')
            ->when(request('type'), function($query, $type) {
                return $query->where('type', $type);
            })
            ->when(request('is_active') !== null, function($query) {
                return $query->where('is_active', request('is_active'));
            })
            ->orderBy('type')
            ->orderBy('capacity', 'desc')
            ->get();
            
        return view('campus.resources.spaces.index', compact('spaces'));
    }
    
    public function getNextCode()
    {
        $lastCourse = CampusCourse::orderBy('id', 'desc')->first();
        $nextId = $lastCourse ? $lastCourse->id + 1 : 1;
        $nextCode = 'CRS-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        
        return response()->json([
            'nextCode' => $nextCode,
            'nextId' => $nextId
        ]);
    }
    
    public function teachers()
    {
        $semester = request()->get('semester', '1Q');
        
        $teachers = CampusTeacher::with(['teacherSchedules' => function($query) use ($semester) {
            $query->where('semester', $semester)
                  ->with('timeSlot');
        }])
        ->where('status', 'active')
        ->get();
        
        return view('campus.resources.teachers', compact('teachers', 'semester'));
    }
    
    public function timeSlots()
    {
        $timeSlots = CampusTimeSlot::with(['courseSchedules' => function($query) {
            $query->with(['course', 'space']);
        }])
            ->when(request('day_of_week'), function($query, $day) {
                return $query->where('day_of_week', $day);
            })
            ->when(request('code'), function($query, $code) {
                return $query->where('code', $code);
            })
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
        
        return view('campus.resources.timeslots.index', compact('timeSlots'));
    }
    
    public function storeSpace(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:campus_spaces,code',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:100',
            'type' => 'required|in:sala_actes,mitjana,petita,polivalent,extern',
            'description' => 'nullable|string|max:500',
            'equipment' => 'nullable|string|max:255'
        ]);
        
        $space = CampusSpace::create($validated);
        
        return response()->json([
            'success' => true,
            'space' => $space,
            'message' => 'Espai creat correctament'
        ]);
    }
    
    public function storeTimeSlot(Request $request)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|integer|min:1|max:5',
            'code' => 'required|string|max:20',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean'
        ]);
        
        // Por defecto activo si no se especifica
        $validated['is_active'] = $validated['is_active'] ?? true;
        
        $timeSlot = CampusTimeSlot::create($validated);
        
        return response()->json([
            'success' => true,
            'timeSlot' => $timeSlot,
            'message' => 'Franja creada correctament'
        ]);
    }
    
    // CRUD Methods for Spaces
    public function editSpace($id)
    {
        $space = CampusSpace::findOrFail($id);
        return response()->json($space);
    }
    
    public function updateSpace(Request $request, $id)
    {
        $space = CampusSpace::findOrFail($id);
        
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:campus_spaces,code,' . $id,
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:100',
            'type' => 'required|in:sala_actes,mitjana,petita,polivalent,extern',
            'description' => 'nullable|string|max:500',
            'equipment' => 'nullable|string|max:255',
            'is_active' => 'required|boolean'
        ]);
        
        $space->update($validated);
        
        return response()->json([
            'success' => true,
            'space' => $space,
            'message' => 'Espai actualitzat correctament'
        ]);
    }
    
    public function destroySpace($id)
    {
        $space = CampusSpace::findOrFail($id);
        
        // Verificar si hay asignaciones activas
        if ($space->courseSchedules()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No es pot eliminar un espai amb assignacions actives'
            ], 422);
        }
        
        $space->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Espai eliminat correctament'
        ]);
    }
    
    // CRUD Methods for TimeSlots
    public function editTimeSlot($id)
    {
        $timeSlot = CampusTimeSlot::findOrFail($id);
        return response()->json($timeSlot);
    }
    
    public function updateTimeSlot(Request $request, $id)
    {
        $timeSlot = CampusTimeSlot::findOrFail($id);
        
        $validated = $request->validate([
            'day_of_week' => 'required|integer|min:1|max:5',
            'code' => 'required|string|max:20',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'required|string|max:255',
            'is_active' => 'required|boolean'
        ]);
        
        $timeSlot->update($validated);
        
        return response()->json([
            'success' => true,
            'timeSlot' => $timeSlot,
            'message' => 'Franja actualitzada correctament'
        ]);
    }
    
    public function destroyTimeSlot($id)
    {
        $timeSlot = CampusTimeSlot::findOrFail($id);
        
        // Verificar si hay asignaciones activas
        if ($timeSlot->courseSchedules()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No es pot eliminar una franja amb assignacions actives'
            ], 422);
        }
        
        $timeSlot->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Franja eliminada correctament'
        ]);
    }
}
