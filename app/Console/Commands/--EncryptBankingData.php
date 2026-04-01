<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\CampusTeacher;

class EncryptBankingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banking:encrypt-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xifrar dades bancàries existents a la BD';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciant xifrat de dades bancàries existents...');
        
        try {
            // Obtenir tots els teachers amb dades bancàries
            $teachers = CampusTeacher::where(function($query) {
                $query->whereNotNull('iban')
                      ->orWhereNotNull('bank_titular')
                      ->orWhereNotNull('fiscal_id');
            })->get();
            
            $this->info("Trobats {$teachers->count()} professors amb dades bancàries...");
            
            $processed = 0;
            $alreadyEncrypted = 0;
            
            foreach ($teachers as $teacher) {
                $this->info("Processant professor ID: {$teacher->id} - {$teacher->full_name}");
                
                $updateData = [];
                
                // Processar IBAN
                if (!empty($teacher->iban)) {
                    if (!$this->isEncrypted($teacher->iban)) {
                        // Usar el mètode manual per evitar conflictes
                        $encryptedValue = \Illuminate\Support\Facades\Crypt::encrypt($teacher->iban);
                        $updateData['iban'] = $encryptedValue;
                        $this->info("  - IBAN xifrat");
                        $processed++;
                    } else {
                        $this->info("  - IBAN ja estava xifrat");
                        $alreadyEncrypted++;
                    }
                }
                
                // Processar titular
                if (!empty($teacher->bank_titular)) {
                    if (!$this->isEncrypted($teacher->bank_titular)) {
                        $encryptedValue = \Illuminate\Support\Facades\Crypt::encrypt($teacher->bank_titular);
                        $updateData['bank_titular'] = $encryptedValue;
                        $this->info("  - Bank titular xifrat");
                        $processed++;
                    } else {
                        $this->info("  - Bank titular ja estava xifrat");
                        $alreadyEncrypted++;
                    }
                }
                
                // Processar fiscal ID
                if (!empty($teacher->fiscal_id)) {
                    if (!$this->isEncrypted($teacher->fiscal_id)) {
                        $encryptedValue = \Illuminate\Support\Facades\Crypt::encrypt($teacher->fiscal_id);
                        $updateData['fiscal_id'] = $encryptedValue;
                        $this->info("  - Fiscal ID xifrat");
                        $processed++;
                    } else {
                        $this->info("  - Fiscal ID ja estava xifrat");
                        $alreadyEncrypted++;
                    }
                }
                
                // Actualitzar directament a la BD
                if (!empty($updateData)) {
                    DB::table('campus_teachers')
                        ->where('id', $teacher->id)
                        ->update($updateData);
                    
                    $this->info("  - Dades actualitzades correctament");
                }
                
                $this->info("  - Professor {$teacher->id} processat ✓");
            }
            
            $this->info('✅ Xifrat de dades bancàries completat!');
            $this->info("📊 Estadístiques:");
            $this->info("   - Processats: {$processed}");
            $this->info("   - Ja xifrats: {$alreadyEncrypted}");
            $this->info("   - Total: {$teachers->count()}");
            
        } catch (\Exception $e) {
            $this->error('❌ Error durant el xifrat: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
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
        return str_starts_with($value, 'eyJpdiI6');
    }
}
