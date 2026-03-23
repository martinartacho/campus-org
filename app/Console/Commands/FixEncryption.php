<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class FixEncryption extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banking:fix-encryption';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corregir xifrat de dades bancàries existents';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Corregint xifrat de dades bancàries...');
        
        try {
            // Obtenir tots els teachers amb IBAN no xifrat
            $teachers = DB::table('campus_teachers')
                ->whereNotNull('iban')
                ->whereRaw("iban NOT LIKE 'eyJpdiI6%'")
                ->get();
            
            $this->info("Trobats {$teachers->count()} professors amb IBAN no xifrat...");
            
            foreach ($teachers as $teacher) {
                $this->info("Processant professor ID: {$teacher->id}");
                
                try {
                    // Xifrar l'IBAN
                    if (!empty($teacher->iban)) {
                        $encryptedIban = Crypt::encrypt($teacher->iban);
                        DB::table('campus_teachers')
                            ->where('id', $teacher->id)
                            ->update(['iban' => $encryptedIban]);
                        
                        $this->info("  - IBAN xifrat correctament");
                    }
                    
                    // Xifrar el titular
                    if (!empty($teacher->bank_titular)) {
                        $encryptedTitular = Crypt::encrypt($teacher->bank_titular);
                        DB::table('campus_teachers')
                            ->where('id', $teacher->id)
                            ->update(['bank_titular' => $encryptedTitular]);
                        
                        $this->info("  - Bank titular xifrat correctament");
                    }
                    
                    // Xifrar el fiscal ID
                    if (!empty($teacher->fiscal_id)) {
                        $encryptedFiscalId = Crypt::encrypt($teacher->fiscal_id);
                        DB::table('campus_teachers')
                            ->where('id', $teacher->id)
                            ->update(['fiscal_id' => $encryptedFiscalId]);
                        
                        $this->info("  - Fiscal ID xifrat correctament");
                    }
                    
                    $this->info("  - Professor {$teacher->id} processat ✓");
                    
                } catch (\Exception $e) {
                    $this->error("  - Error processant professor {$teacher->id}: " . $e->getMessage());
                }
            }
            
            $this->info('✅ Correcció de xifrat completada!');
            
        } catch (\Exception $e) {
            $this->error('❌ Error durant la correcció: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
