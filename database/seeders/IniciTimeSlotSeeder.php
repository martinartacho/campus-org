<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CampusTimeSlot;

class IniciTimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Creación de Franjas Horarias Iniciales ===');
        
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

        $created = 0;
        $updated = 0;
        
        foreach ($timeSlots as $timeSlot) {
            $existing = CampusTimeSlot::where('day_of_week', $timeSlot['day_of_week'])
                                   ->where('start_time', $timeSlot['start_time'])
                                   ->where('end_time', $timeSlot['end_time'])
                                   ->first();
            
            if ($existing) {
                $existing->update($timeSlot);
                $updated++;
            } else {
                CampusTimeSlot::create($timeSlot);
                $created++;
            }
        }
        
        $this->command->info("✅ Franjas horarias creadas: {$created}");
        $this->command->info("🔄 Franjas horarias actualizadas: {$updated}");
        $this->command->info("📊 Total franjas horarias: " . CampusTimeSlot::count());
        $this->command->info('=== FIN DE CREACIÓN DE FRANJAS HORARIAS ===');
    }
}
