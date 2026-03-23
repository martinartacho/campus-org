<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\CampusTeacher;

class EncryptBankingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Iniciant xifrat de dades bancàries existents...');
        
        try {
            // Obtenir tots els teachers amb dades bancàries
            $teachers = CampusTeacher::whereNotNull('iban')
                ->orWhereNotNull('bank_titular')
                ->orWhereNotNull('fiscal_id')
                ->get();
            
            $this->command->info("Trobats {$teachers->count()} professors amb dades bancàries...");
            
            foreach ($teachers as $teacher) {
                $this->command->info("Processant professor ID: {$teacher->id} - {$teacher->full_name}");
                
                // Guardar els valors originals
                $originalData = [
                    'iban' => $teacher->iban,
                    'bank_titular' => $teacher->bank_titular,
                    'fiscal_id' => $teacher->fiscal_id,
                ];
                
                // Actualitzar amb dades xifrades manualment
                $updateData = [];
                
                if (!empty($originalData['iban'])) {
                    // Verificar si ja està xifrat
                    if (!$this->isEncrypted($originalData['iban'])) {
                        $updateData['iban'] = Crypt::encrypt($originalData['iban']);
                        $this->command->info("  - IBAN xifrat");
                    } else {
                        $this->command->info("  - IBAN ja estava xifrat");
                    }
                }
                
                if (!empty($originalData['bank_titular'])) {
                    if (!$this->isEncrypted($originalData['bank_titular'])) {
                        $updateData['bank_titular'] = Crypt::encrypt($originalData['bank_titular']);
                        $this->command->info("  - Bank titular xifrat");
                    } else {
                        $this->command->info("  - Bank titular ja estava xifrat");
                    }
                }
                
                if (!empty($originalData['fiscal_id'])) {
                    if (!$this->isEncrypted($originalData['fiscal_id'])) {
                        $updateData['fiscal_id'] = Crypt::encrypt($originalData['fiscal_id']);
                        $this->command->info("  - Fiscal ID xifrat");
                    } else {
                        $this->command->info("  - Fiscal ID ja estava xifrat");
                    }
                }
                
                // Actualitzar directament a la BD
                if (!empty($updateData)) {
                    DB::table('campus_teachers')
                        ->where('id', $teacher->id)
                        ->update($updateData);
                    
                    $this->command->info("  - Dades actualitzades correctament");
                }
                
                $this->command->info("  - Professor {$teacher->id} processat ✓");
            }
            
            $this->command->info('✅ Xifrat de dades bancàries completat correctament!');
            
        } catch (\Exception $e) {
            $this->command->error('❌ Error durant el xifrat: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Verificar si un valor ja està xifrat.
     */
    private function isEncrypted($value): bool
    {
        if (!is_string($value)) {
            return false;
        }
        
        // Els valors xifrats per Laravel comencen amb eyJpdiI6...
        return str_starts_with($value, 'eyJpdiI6') || str_starts_with($value, 'eyJ');
    }
}
