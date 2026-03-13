<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\CampusCourseStudent;
use App\Models\CampusRegistration;
use Illuminate\Support\Collection;

class StudentDashboardData
{
    public function build(User $user): array
    {
        try {
            $student = $user->student;
            
            if (!$student) {
                return [
                    'student' => null,
                    'studentStats' => $this->getEmptyStats(),
                    'studentCourses' => collect(),
                    'recentActivity' => collect(),
                    'upcomingClasses' => collect(),
                    'grades' => collect(),
                    'debug' => 'Student not found for user: ' . $user->id,
                    'error' => null,
                ];
            }

            // Obtener cursos del estudiante (USAR CampusCourseStudent - DATOS REALES)
            $studentCourses = CampusCourseStudent::where('student_id', $student->id)
                ->with(['course', 'season'])
                ->get();

            // Obtener matrículas (intentar pero puede estar vacío)
            $registrations = CampusRegistration::where('student_id', $student->id)
                ->with(['course', 'season'])
                ->get();

            // Debug info
            $debugInfo = [
                'student_id' => $student->id,
                'student_code' => $student->student_code,
                'registrations_count' => $registrations->count(),
                'registrations_data' => $registrations->toArray(),
                'student_courses_count' => $studentCourses->count(),
            ];

            // Calcular estadísticas
            $studentStats = $this->getStudentStats($student, $studentCourses, $registrations);

            // Actividad reciente
            $recentActivity = $this->getRecentActivity($registrations);

            // Próximas clases (simulado - podría venir de sistema de horarios)
            $upcomingClasses = $this->getUpcomingClasses($registrations);

            // Notas recientes (simulado - podría venir de sistema de calificaciones)
            $grades = $this->getRecentGrades($student);

            return [
                'student' => $student,
                'studentStats' => $studentStats,
                'studentCourses' => $studentCourses, // USAR CampusCourseStudent - DATOS REALES
                'recentActivity' => $recentActivity,
                'upcomingClasses' => $upcomingClasses,
                'grades' => $grades,
                'debug' => $debugInfo,
                'error' => null,
            ];

        } catch (\Exception $e) {
            return [
                'student' => $user->student ?? null,
                'studentStats' => $this->getEmptyStats(),
                'studentCourses' => collect(),
                'recentActivity' => collect(),
                'upcomingClasses' => collect(),
                'grades' => collect(),
                'debug' => null,
                'error' => 'Error loading dashboard: ' . $e->getMessage(),
            ];
        }
    }

    private function getStudentStats($student, $studentCourses, $registrations): array
    {
        // Estadísticas basadas en CampusRegistration (DATOS REALES)
        $totalCourses = $registrations->count();
        $activeCourses = $registrations->where('status', 'confirmed')->count();
        $completedCourses = $registrations->where('status', 'completed')->count();
        $pendingCourses = $registrations->where('status', 'pending')->count();
        
        // Matrículas recientes (últimos 30 días)
        $recentRegistrations = $registrations->where('created_at', '>=', now()->subDays(30))->count();

        return [
            'total_courses' => $totalCourses,
            'active_courses' => $activeCourses,
            'completed_courses' => $completedCourses,
            'pending_courses' => $pendingCourses,
            'recent_registrations' => $recentRegistrations,
            // Eliminados: attendance_rate y average_grade - NO PROCEDE para Universidad Popular
        ];
    }

    private function getEmptyStats(): array
    {
        return [
            'total_courses' => 0,
            'active_courses' => 0,
            'completed_courses' => 0,
            'pending_courses' => 0,
            'recent_registrations' => 0,
            // Eliminados: attendance_rate y average_grade - NO PROCEDE para Universidad Popular
        ];
    }

    private function getRecentActivity($registrations): Collection
    {
        // Actividad reciente basada en CampusRegistration (SOLO MATRÍCULAS REALES)
        $activities = collect();
        
        foreach ($registrations->take(5) as $registration) {
            $activities->push([
                'type' => 'registration',
                'title' => 'Nova matrícula: ' . ($registration->course->title ?? 'Curso'),
                'date' => $registration->created_at,
                'icon' => 'bi-book-plus',
                'color' => 'blue'
            ]);
        }

        return $activities->sortByDesc('date')->take(5);
    }

    private function getUpcomingClasses($registrations): Collection
    {
        // Próximas clases - NO PROCEDE, mostrará "--" 
        return collect(); // Vacío por ahora
    }

    private function getRecentGrades($student): Collection
    {
        // Notas recientes - NO PROCEDE para Universidad Popular
        return collect(); // Vacío por ahora
    }

    public static function from(User $user): array
    {
        return (new self())->build($user);
    }
}
