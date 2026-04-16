<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Models\CampusRegistration;
use App\Models\CampusStudent;
use App\Models\CampusCourse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:campus.registrations.view')->only(['index', 'show']);
        $this->middleware('can:campus.registrations.create')->only(['create', 'store']);
        $this->middleware('can:campus.registrations.edit')->only(['edit', 'update']);
        $this->middleware('can:campus.registrations.delete')->only(['destroy']);
        $this->middleware('can:campus.registrations.validate')->only(['validateRegistration']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = CampusRegistration::with(['student', 'course'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('dni', 'like', "%{$search}%");
                })
                ->orWhereHas('course', function ($courseQuery) use ($search) {
                    $courseQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })
                ->orWhere('registration_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->get('payment_status'));
        }

        $registrations = $query->paginate(25);

        // Estadísticas
        $stats = [
            'total' => CampusRegistration::count(),
            'confirmed' => CampusRegistration::where('status', 'confirmed')->count(),
            'pending' => CampusRegistration::where('status', 'pending')->count(),
            'paid' => CampusRegistration::where('payment_status', 'paid')->count(),
            'total_amount' => CampusRegistration::sum('amount'),
        ];

        return view('campus.registrations.index', compact('registrations', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('campus.registrations.create');
        
        $students = \App\Models\CampusStudent::orderBy('last_name')->orderBy('first_name')->get();
        $courses = \App\Models\CampusCourse::with('season')->orderBy('title')->get();
        
        return view('campus.registrations.create', compact('students', 'courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('campus.registrations.create');
        
        $validated = $request->validate([
            'student_id' => 'required|exists:campus_students,id',
            'course_id' => 'required|exists:campus_courses,id',
            'registration_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,paid,partial,cancelled',
            'payment_due_date' => 'nullable|date|after_or_equal:registration_date',
            'payment_method' => 'required|in:web,presencial,bank_transfer,other',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Add user_id to track who created the registration
        $validated['user_id'] = auth()->id() ?? null;
        
        // Check for unique student-course combination
        $existing = CampusRegistration::where('student_id', $validated['student_id'])
            ->where('course_id', $validated['course_id'])
            ->first();
            
        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('campus.registration_already_exists'));
        }
        
        // Get season_id from course
        $course = \App\Models\CampusCourse::find($validated['course_id']);
        if (!$course || !$course->season_id) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('campus.course_without_season_error'));
        }
        $validated['season_id'] = $course->season_id;
        
        // Generate unique registration code
        $validated['registration_code'] = 'REG-' . date('Y') . '-' . str_pad(CampusRegistration::count() + 1, 6, '0', STR_PAD_LEFT);
        $validated['status'] = $validated['payment_status'] === 'paid' ? 'confirmed' : 'pending';
        
        try {
            $registration = CampusRegistration::create($validated);
            
            return redirect()
                ->route('campus.registrations.show', $registration->id)
                ->with('success', __('campus.registration_created_successfully'));
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('campus.registration_create_error') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('campus.registrations.view');
        
        $registration = CampusRegistration::with([
            'student',
            'course',
            'course.season'
        ])->findOrFail($id);
        
        // Get related course student if exists
        $courseStudent = \App\Models\CampusCourseStudent::where('student_id', $registration->student_id)
            ->where('course_id', $registration->course_id)
            ->first();
        
        return view('campus.registrations.show', compact('registration', 'courseStudent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('campus.registrations.edit');
        
        $registration = CampusRegistration::findOrFail($id);
        $students = \App\Models\CampusStudent::orderBy('last_name')->orderBy('first_name')->get();
        $courses = \App\Models\CampusCourse::with('season')->orderBy('title')->get();
        
        return view('campus.registrations.edit', compact('registration', 'students', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('campus.registrations.edit');
        
        $registration = CampusRegistration::findOrFail($id);
        
        $validated = $request->validate([
            'student_id' => 'required|exists:campus_students,id',
            'course_id' => 'required|exists:campus_courses,id',
            'registration_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,cancelled,completed,failed',
            'payment_status' => 'required|in:pending,paid,partial,cancelled',
            'payment_due_date' => 'nullable|date|after_or_equal:registration_date',
            'payment_method' => 'required|in:web,presencial,bank_transfer,other',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Add user_id to track who updated the registration
        $validated['user_id'] = auth()->id() ?? null;
        
        // Check for unique student-course combination (excluding current registration)
        $existing = CampusRegistration::where('student_id', $validated['student_id'])
            ->where('course_id', $validated['course_id'])
            ->where('id', '!=', $registration->id)
            ->first();
            
        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('campus.registration_already_exists'));
        }
        
        try {
            $registration->update($validated);
            
            return redirect()
                ->route('campus.registrations.show', $registration->id)
                ->with('success', __('campus.registration_updated_successfully'));
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('campus.registration_update_error') . ': ' . $e->getMessage());
        }
    }

    /**
     * Validate registration and create course student relationship.
     */
    public function validateRegistration(string $id)
    {
        try {
            $registration = CampusRegistration::findOrFail($id);
            
            // Update registration status to confirmed
            $registration->update(['status' => 'confirmed']);
            
            // Sync with campus_course_student table
            $this->syncWithCourseStudent($registration);
            
            return redirect()->back()
                ->with('success', __('campus.registration_validated_successfully'));
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('campus.registration_validation_error') . ': ' . $e->getMessage());
        }
    }

    /**
     * Sync registration with campus_course_student table.
     */
    private function syncWithCourseStudent(CampusRegistration $registration): void
    {
        // Check if there's already a course student record
        $courseStudent = \App\Models\CampusCourseStudent::where('student_id', $registration->student_id)
            ->where('course_id', $registration->course_id)
            ->where('season_id', $registration->season_id)
            ->first();

        if (!$courseStudent) {
            // Create new course student record
            \App\Models\CampusCourseStudent::create([
                'student_id' => $registration->student_id,
                'course_id' => $registration->course_id,
                'season_id' => $registration->season_id,
                'enrollment_date' => $registration->registration_date,
                'academic_status' => 'enrolled',
                'start_date' => $registration->registration_date,
                'notes' => 'Matrícula creada automàticament des de registre: ' . $registration->registration_code
            ]);
        } else {
            // Update existing course student record
            $courseStudent->update([
                'academic_status' => 'enrolled',
                'enrollment_date' => $registration->registration_date,
                'start_date' => $registration->registration_date,
                'notes' => ($courseStudent->notes ?? '') . "\n\nMatrícula actualitzada: " . $registration->registration_code
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('campus.registrations.delete');
        
        try {
            $registration = CampusRegistration::findOrFail($id);
            
            // Check if there's a related course student
            $courseStudent = \App\Models\CampusCourseStudent::where('student_id', $registration->student_id)
                ->where('course_id', $registration->course_id)
                ->first();
                
            if ($courseStudent) {
                // Update course student status instead of deleting
                $courseStudent->update([
                    'academic_status' => 'dropped',
                    'end_date' => now(),
                    'notes' => ($courseStudent->notes ?? '') . "\n\nBaixa per eliminació de registre: " . now()->format('d/m/Y H:i')
                ]);
            }
            
            $registration->delete();
            
            return redirect()
                ->route('campus.registrations.index')
                ->with('success', __('campus.registration_deleted_successfully'));
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('campus.registration_delete_error') . ': ' . $e->getMessage());
        }
    }
}
