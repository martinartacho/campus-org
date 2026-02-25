<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CampusTimeSlot;

class CampusTimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timeSlots = [];
        
        // Para cada día de la semana (lunes a viernes)
        foreach ([1, 2, 3, 4, 5] as $dayOfWeek) {
            // Matí 11:00-12:30
            $timeSlots[] = [
                'day_of_week' => $dayOfWeek,
                'code' => 'M11',
                'start_time' => '11:00:00',
                'end_time' => '12:30:00',
                'description' => 'Matí 11:00-12:30',
                'is_active' => true,
            ];
            
            // Tarda 16:00-17:30
            $timeSlots[] = [
                'day_of_week' => $dayOfWeek,
                'code' => 'T16',
                'start_time' => '16:00:00',
                'end_time' => '17:30:00',
                'description' => 'Tarda 16:00-17:30',
                'is_active' => true,
            ];
            
            // Tarda 18:00-19:30
            $timeSlots[] = [
                'day_of_week' => $dayOfWeek,
                'code' => 'T18',
                'start_time' => '18:00:00',
                'end_time' => '19:30:00',
                'description' => 'Tarda 18:00-19:30',
                'is_active' => true,
            ];
        }

        foreach ($timeSlots as $timeSlot) {
            CampusTimeSlot::create($timeSlot);
        }
    }
}
