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

class ResourceController extends Controller
{
    public function calendar(Request $request)
    {
        $semester = $request->get('semester', '1Q');
        
        $timeSlots = CampusTimeSlot::with(['courseSchedules' => function($query) use ($semester) {
            $query->where('semester', $semester)
                  ->with(['course', 'space']);
        }])
        ->where('is_active', true)
        ->orderBy('day_of_week')
        ->orderBy('start_time')
        ->get();
        
        $spaces = CampusSpace::where('is_active', true)
            ->orderBy('type')
            ->orderBy('capacity', 'desc')
            ->get();
            
        return view('campus.resources.calendar', compact('timeSlots', 'spaces', 'semester'));
    }
    
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:campus_courses,id',
            'space_id' => 'required|exists:campus_spaces,id',
            'time_slot_id' => 'required|exists:campus_time_slots,id',
            'semester' => 'required|in:1Q,2Q'
        ]);
        
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
            ->where('is_active', true)
            ->get();
            
        return view('campus.resources.spaces', compact('spaces'));
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
        ->where('is_active', true)
        ->orderBy('day_of_week')
        ->orderBy('start_time')
        ->get();
        
        return response()->json($timeSlots);
    }
    
    public function storeSpace(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:campus_spaces,code',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:100',
            'type' => 'required|in:sala_actes,mitjana,petita,polivalent,extern',
            'description' => 'nullable|string|max:500',
            'equipment' => 'nullable|array'
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
            'code' => 'required|string|max:10',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'required|string|max:255'
        ]);
        
        $timeSlot = CampusTimeSlot::create($validated);
        
        return response()->json([
            'success' => true,
            'timeSlot' => $timeSlot,
            'message' => 'Franja creada correctament'
        ]);
    }
}
