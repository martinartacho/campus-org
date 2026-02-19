<?php

namespace App\Services\Dashboard;

use App\Models\CampusCourse;
use App\Models\CampusSeason;
use App\Models\CampusTeacher;
use App\Models\CampusRegistration;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TeacherDashboardData
{
    public function build($user): array
    {
        try {
            // Debug: Verificar usuario y relación
            Log::info('TeacherDashboardData - User ID: ' . $user->id);
            Log::info('TeacherDashboardData - User email: ' . $user->email);
            Log::info('TeacherDashboardData - User roles: ' . $user->getRoleNames()->implode(', '));

            // Verificar si el usuario tiene rol de profesor
            if (!$user->hasRole('teacher')) {
                Log::warning('TeacherDashboardData - User does not have teacher role');
                return [
                    'teacher' => null,
                    'teacherCourses' => collect(),
                    'stats' => [],
                    'season' => null,
                    'seasons' => collect(),
                    'allCourses' => collect(),
                    'debug' => 'User does not have teacher role'
                ];
            }

            // Buscar el perfil de profesor
            // $teacher = CampusTeacher::where('user_id', $user->id)->first();
            $teacher = $teacher ?? $user->teacher;

            if (!$teacher) {
                Log::warning('TeacherDashboardData - No teacher profile found for user ID: ' . $user->id);
                return [
                    'teacher' => null,
                    'teacherCourses' => collect(),
                    'stats' => [],
                    'season' => null,
                    'seasons' => collect(),
                    'allCourses' => collect(),
                    'debug' => 'No teacher profile found'
                ];
            }

            Log::info('TeacherDashboardData - Teacher found: ' . $teacher->teacher_code);

            // Obtener temporada actual
            $currentSeason = CampusSeason::where('is_current', true)->first();
            $seasons = CampusSeason::active()->orderBy('season_start', 'desc')->get();

            Log::info('TeacherDashboardData - Current season: ' . ($currentSeason ? $currentSeason->name : 'none'));

            // Obtener cursos del profesor usando la relación correcta
            // IMPORTANTE: Usar la relación definida en el modelo CampusTeacher
            $coursesQuery = $teacher->courses()
                ->with(['season', 'category'])
                ->withCount([
                    'registrations as confirmed_students_count' => function ($q) {
                        $q->where('status', 'confirmed');
                    },
                    'registrations as completed_students_count' => function ($q) {
                        $q->where('status', 'completed');
                    }
                ]);

            // Debug: Verificar consulta SQL
            $sql = $coursesQuery->toSql();
            Log::info('TeacherDashboardData - Courses query SQL: ' . $sql);

            // Mostrar todos los cursos del teacher sin filtrar por temporada
            // if ($currentSeason) {
            //     $coursesQuery->where('season_id', $currentSeason->id);
            //     Log::info('TeacherDashboardData - Filtering by season: ' . $currentSeason->id);
            // }
            Log::info('TeacherDashboardData - Showing ALL courses for teacher');

            $teacherCourses = $coursesQuery->get();
            Log::info('TeacherDashboardData - Teacher courses count: ' . $teacherCourses->count());

            // Obtener todos los cursos del profesor para estadísticas totales
            $allTeacherCourses = $teacher->courses()->get();
            Log::info('TeacherDashboardData - All teacher courses count: ' . $allTeacherCourses->count());

            // Calcular estadísticas
            $totalStudents = 0;
            $activeCoursesCount = 0;
            $todayClasses = 0;
            $upcomingClasses = 0;

            foreach ($teacherCourses as $course) {
                Log::info('TeacherDashboardData - Course: ' . $course->title . ' | Students: ' . $course->confirmed_students_count);
                $totalStudents += $course->confirmed_students_count;
                
                if ($course->is_active && $course->isCurrentlyActive()) {
                    $activeCoursesCount++;
                    
                    // Calcular clases próximas
                    if ($course->schedule) {
                        $today = Carbon::now()->dayOfWeekIso; // 1=Lunes, 7=Domingo
                        $currentTime = Carbon::now()->format('H:i');
                        
                        foreach ($course->schedule as $schedule) {
                            $dayMap = [
                                'Dilluns' => 1,
                                'Dimarts' => 2,
                                'Dimecres' => 3,
                                'Dijous' => 4,
                                'Divendres' => 5,
                                'Dissabte' => 6,
                                'Diumenge' => 7
                            ];
                            
                            $dayNumber = $dayMap[$schedule['day']] ?? 0;
                            
                            if ($dayNumber > 0) {
                                // Clases de hoy
                                if ($dayNumber == $today) {
                                    $todayClasses++;
                                    Log::info('TeacherDashboardData - Today class: ' . $schedule['day'] . ' ' . $schedule['start'] . '-' . $schedule['end']);
                                }
                                
                                // Clases próximas (próximos 7 días)
                                if ($dayNumber >= $today && $dayNumber <= $today + 7) {
                                    $upcomingClasses++;
                                }
                            }
                        }
                    }
                }
            }

            // Obtener consentimientos del teacher
            $consentments = \App\Models\ConsentHistory::where('teacher_id', $user->id)
                ->with(['course', 'season'])
                ->latest('accepted_at')
                ->get();
            
            Log::info('TeacherDashboardData - Consentments found: ' . $consentments->count());
            
            $stats = [
                'total_courses' => $teacherCourses->count(),
                'active_courses' => $activeCoursesCount,
                'total_students' => $totalStudents,
                'active_students' => $totalStudents,
                'today_classes' => $todayClasses,
                'upcoming_classes' => $upcomingClasses,
                'completed_courses' => $teacherCourses->where('end_date', '<', now())->count(),
                'upcoming_registrations' => CampusRegistration::whereIn('course_id', $teacherCourses->pluck('id'))
                    ->where('status', 'pending')
                    ->count(),
                'pending_consents' => $consentments->whereNull('document_path')->count(),
                'completed_consents' => $consentments->whereNotNull('document_path')->count(),
            ];

            Log::info('TeacherDashboardData - Stats calculated', $stats);

            return [
                'teacher' => $teacher,
                'season' => $currentSeason,
                'seasons' => $seasons,
                'teacherCourses' => $teacherCourses,
                'allCourses' => $allTeacherCourses,
                'stats' => $stats,
                'consentments' => $consentments,
                'debug' => [
                    'user_id' => $user->id,
                    'has_teacher_role' => $user->hasRole('teacher'),
                    'teacher_found' => $teacher ? true : false,
                    'courses_count' => $teacherCourses->count(),
                ]
            ];

        } catch (\Throwable $e) {
            Log::error('TeacherDashboardData error: ' . $e->getMessage());
            Log::error('TeacherDashboardData trace: ' . $e->getTraceAsString());
            
            return [
                'teacher' => null,
                'teacherCourses' => collect(),
                'stats' => [],
                'season' => null,
                'seasons' => collect(),
                'allCourses' => collect(),
                'error' => $e->getMessage()
            ];
        }
    }

    public static function from(User $user): array
    {
        $teacher = $user->teacher; // relación User -> CampusTeacher

        $courses = $teacher
            ->courses()
            ->withCount('students')
            ->with('campus')
            ->get()
            ->map(fn ($course) => [
                'id'        => $course->id,
                'name'      => $course->name,
                'campus'    => $course->campus?->name,
                'students'  => $course->students_count,
                'role'      => $course->pivot->role,
                'hours'     => $course->pivot->hours_assigned,
                'is_main'   => $course->pivot->role === 'main',
            ]);

        return [
            'teacher' => $teacher,
            'courses' => $courses,
        ];
    }
}