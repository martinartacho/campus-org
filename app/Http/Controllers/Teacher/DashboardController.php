<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CampusTeacher;
use App\Models\CampusSeason;
use App\Models\ConsentHistory;
use App\Models\CampusTeacherPayment;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        Log::info('Teacher Dashboard - User ID: ' . $user->id);
        
        try {
            // Obtener datos del teacher
            $teacher = CampusTeacher::where('user_id', $user->id)->first();
            
            if (!$teacher) {
                return view('teacher.dashboard', [
                    'teacher' => null,
                    'season' => null,
                    'seasons' => [],
                    'teacherCourses' => collect(),
                    'stats' => [],
                    'consentments' => collect(),
                    'currentSeason' => null,
                    'debug' => 'No teacher found',
                    'error' => null,
                ]);
            }
            
            // Obtener temporada actual
            $currentSeason = CampusSeason::where('is_current', true)->first();
            
            // Obtener consentimientos del teacher
            Log::info('Teacher ID for consentments: ' . $teacher->id);
            Log::info('User ID: ' . $user->id);
            
            $consentments = ConsentHistory::where('teacher_id', $teacher->id)
                ->with(['course', 'season'])
                ->latest('accepted_at')
                ->get();
                
            Log::info('Consentments found: ' . $consentments->count());
            
            // Calcular estadísticas
            $stats = [
                'total_courses' => $teacher->courses()->count(),
                'active_courses' => $teacher->courses()->where('status', 'active')->count(),
                'completed_courses' => $teacher->courses()->where('status', 'completed')->count(),
                'total_students' => $teacher->courses()->sum('confirmed_students_count'),
                'pending_consents' => $consentments->whereNull('document_path')->count(),
                'completed_consents' => $consentments->whereNotNull('document_path')->count(),
                'today_classes' => 0, // TODO: Implementar lógica de clases de hoy
                'upcoming_registrations' => 0, // TODO: Implementar lógica de registros
            ];
            
            // Obtener cursos del teacher con información de consentimiento
            $teacherCourses = $teacher->courses()
                ->with(['category', 'season'])
                ->get()
                ->map(function ($course) use ($consentments) {
                    $courseConsent = $consentments
                        ->where('course_id', $course->id)
                        ->where('season_id', $course->season_id)
                        ->first();
                    
                    $course->consent_status = $courseConsent ? 
                        ($courseConsent->document_path ? 'completed' : 'pending') : null;
                    $course->consent_document = $courseConsent;
                    
                    return $course;
                });
            
            Log::info('Teacher Dashboard - Stats calculated', ['stats' => $stats]);
            
            return view('teacher.dashboard', [
                'teacher' => $teacher,
                'season' => $currentSeason,
                'seasons' => CampusSeason::orderBy('created_at', 'desc')->get(),
                'teacherCourses' => $teacherCourses,
                'stats' => $stats,
                'consentments' => $consentments,
                'currentSeason' => $currentSeason,
                'debug' => null,
                'error' => null,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in Teacher Dashboard: ' . $e->getMessage());
            
            return view('teacher.dashboard', [
                'teacher' => null,
                'season' => null,
                'seasons' => [],
                'teacherCourses' => collect(),
                'stats' => [],
                'consentments' => collect(),
                'currentSeason' => null,
                'debug' => 'Error: ' . $e->getMessage(),
                'error' => 'Error al cargar los datos del dashboard',
            ]);
        }
    }
}
