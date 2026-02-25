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
