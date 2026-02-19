<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Models\CampusCourse;
use App\Models\CampusTeacher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseTeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:campus.teachers.assign');
    }

    /**
     * Mostrar profesores asignados al curso
     */
    public function index(CampusCourse $course)
    {
        $teachers = CampusTeacher::orderBy('last_name')->get();

        $assignedTeachers = $course->teachers()
            ->withPivot(['role', 'hours_assigned'])
            ->get()
            ->keyBy('id');

        return view('campus.courses.teachers', compact(
            'course',
            'teachers',
            'assignedTeachers'
        ));
    }

    /**
     * Asignar profesor al curso
     */


    public function store(Request $request, CampusCourse $course)
    {
        $data = $request->validate([
            'teacher_id' => [
                'required',
                'exists:campus_teachers,id',
            ],
            'role' => [
                'required',
                Rule::in(array_keys(CampusCourse::TEACHER_ROLES)),
            ],
            'hours_assigned' => [
                'required',
                'numeric',
                'min:0.5',
                'max:999.99',
            ],
        ]);

        // Horas ya asignadas (excluyendo si el profe ya está asignado)
        $alreadyAssignedHours = $course->teachers()
            ->wherePivot('teacher_id', '!=', $data['teacher_id'])
            ->sum('hours_assigned');

        if (($alreadyAssignedHours + $data['hours_assigned']) > $course->hours) {
            return back()
                ->withErrors([
                    'hours_assigned' => __('campus.course_hours_exceeded', [
                        'total' => $course->hours,
                        'assigned' => $alreadyAssignedHours,
                    ])
                ])
                ->withInput();
        }

        $course->teachers()->syncWithoutDetaching([
            $data['teacher_id'] => [
                'role' => $data['role'],
                'hours_assigned' => $data['hours_assigned'],
                'assigned_at' => now(),
            ]
        ]);

        return redirect()
            ->route('campus.courses.teachers', $course)
            ->with('success', __('campus.teacher_assigned'));
    }


    /**
     * Desasignar profesor del curso
     */
    public function destroy(CampusCourse $course, CampusTeacher $teacher)
    {
        $course->teachers()->detach($teacher->id);

        return redirect()
            ->route('campus.courses.teachers', $course)
            ->with('success', __('campus.teacher_removed'));
    }
}
