<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CampusSpace;

class IniciSpaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Creación de Espacios/Aulas Iniciales ===');
        
        $spaces = [
            [
                'name' => 'Sala d\'Actes',
                'code' => 'SA',
                'capacity' => 50,
                'type' => 'sala_actes',
                'description' => 'Sala principal per a actes i grans esdeveniments',
                'equipment' => ['projector', 'audio', 'tv'],
                'is_active' => true,
            ],
            [
                'name' => 'Aula Mitjana 1',
                'code' => 'AM1',
                'capacity' => 25,
                'type' => 'mitjana',
                'description' => 'Aula de capacitat mitjana amb equipament bàsic',
                'equipment' => ['projector', 'ordinadors'],
                'is_active' => true,
            ],
            [
                'name' => 'Aula Mitjana 2',
                'code' => 'AM2',
                'capacity' => 25,
                'type' => 'mitjana',
                'description' => 'Aula de capacitat mitjana amb equipament bàsic',
                'equipment' => ['projector', 'tv'],
                'is_active' => true,
            ],
            [
                'name' => 'Aula Petita 1',
                'code' => 'AP1',
                'capacity' => 10,
                'type' => 'petita',
                'description' => 'Aula petita per a grups reduïts',
                'equipment' => ['projector'],
                'is_active' => true,
            ],
            [
                'name' => 'Aula Petita 2',
                'code' => 'AP2',
                'capacity' => 10,
                'type' => 'petita',
                'description' => 'Aula petita per a grups reduïts',
                'equipment' => ['tv'],
                'is_active' => true,
            ],
            [
                'name' => 'Sala Polivalent',
                'code' => 'SP',
                'capacity' => 30,
                'type' => 'polivalent',
                'description' => 'Sala polivalent per a diverses activitats',
                'equipment' => ['projector', 'audio', 'tv', 'ordinadors'],
                'is_active' => true,
            ],
            [
                'name' => 'Gimnàs',
                'code' => 'GYM',
                'capacity' => 40,
                'type' => 'extern',
                'description' => 'Espai extern per a activitats esportives',
                'equipment' => ['audio'],
                'is_active' => true,
            ],
            [
                'name' => 'Sala de Projeccions',
                'code' => 'PROJ',
                'capacity' => 20,
                'type' => 'extern',
                'description' => 'Sala equipada per a projeccions audiovisuals',
                'equipment' => ['projector', 'audio', 'tv'],
                'is_active' => true,
            ],
        ];

        $created = 0;
        $updated = 0;
        
        foreach ($spaces as $space) {
            $existing = CampusSpace::where('code', $space['code'])->first();
            
            if ($existing) {
                $existing->update($space);
                $updated++;
            } else {
                CampusSpace::create($space);
                $created++;
            }
        }
        
        $this->command->info("✅ Espacios creados: {$created}");
        $this->command->info("🔄 Espacios actualizados: {$updated}");
        $this->command->info("📊 Total espacios: " . CampusSpace::count());
        $this->command->info('=== FIN DE CREACIÓN DE ESPACIOS ===');
    }
}
