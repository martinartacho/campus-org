<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CampusCourseSchedule;
use App\Models\CampusCourse;
use App\Models\CampusSpace;
use App\Models\CampusTimeSlot;

class CampusCourseScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener algunos datos existentes
        $courses = CampusCourse::take(5)->get();
        $spaces = CampusSpace::where('is_active', true)->get();
        $timeSlots = CampusTimeSlot::where('is_active', true)->get();

        if ($courses->isEmpty() || $spaces->isEmpty() || $timeSlots->isEmpty()) {
            $this->command->warn('No hay suficientes datos para crear asignaciones. Ejecuta primero otros seeders.');
            return;
        }

        $schedules = [
            [
                'course_id' => $courses[0]->id ?? 1,
                'space_id' => $spaces->where('type', 'mitjana')->first()->id ?? 1,
                'time_slot_id' => $timeSlots->where('code', 'M11')->first()->id ?? 1,
                'semester' => '1Q',
                'status' => 'assigned',
                'session_count' => 12,
                'start_date' => '2024-09-01',
                'end_date' => '2024-12-20',
                'notes' => 'Assignació regular',
            ],
            [
                'course_id' => $courses[1]->id ?? 2,
                'space_id' => $spaces->where('type', 'petita')->first()->id ?? 2,
                'time_slot_id' => $timeSlots->where('code', 'T16')->first()->id ?? 2,
                'semester' => '1Q',
                'status' => 'assigned',
                'session_count' => 10,
                'start_date' => '2024-09-01',
                'end_date' => '2024-12-20',
                'notes' => 'Grup reduït',
            ],
            [
                'course_id' => $courses[2]->id ?? 3,
                'space_id' => $spaces->where('type', 'sala_actes')->first()->id ?? 3,
                'time_slot_id' => $timeSlots->where('code', 'T18')->first()->id ?? 3,
                'semester' => '1Q',
                'status' => 'conflict',
                'session_count' => 8,
                'start_date' => '2024-09-01',
                'end_date' => '2024-12-20',
                'notes' => 'Conflicte de capacitat',
            ],
            [
                'course_id' => $courses[3]->id ?? 4,
                'space_id' => $spaces->where('type', 'polivalent')->first()->id ?? 4,
                'time_slot_id' => $timeSlots->where('code', 'M11')->skip(1)->first()->id ?? 4,
                'semester' => '2Q',
                'status' => 'pending',
                'session_count' => 15,
                'start_date' => '2025-01-08',
                'end_date' => '2025-05-30',
                'notes' => 'Pendent de confirmar',
            ],
            [
                'course_id' => $courses[3]->id ?? 4,
                'space_id' => $spaces->where('type', 'extern')->first()->id ?? 5,
                'time_slot_id' => $timeSlots->where('code', 'T16')->skip(1)->first()->id ?? 5,
                'semester' => '2Q',
                'status' => 'assigned',
                'session_count' => 6,
                'start_date' => '2025-01-08',
                'end_date' => '2025-05-30',
                'notes' => 'Activitat exterior',
            ],
        ];

        foreach ($schedules as $schedule) {
            CampusCourseSchedule::create($schedule);
        }
    }
}
