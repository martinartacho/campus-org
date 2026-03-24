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
                ->get();
            
            $count = 0;
            
            foreach ($teachers as $teacher) {
                $iban = $teacher->iban;
                
                // Si està encriptat però mal format (comença amb s:)
                if (strpos($iban, 's:') === 0) {
                    // Extreure el valor real de la cadena serialitzada
                    preg_match('/s:\d+:"([^"]+)"/', $iban, $matches);
                    
                    if (isset($matches[1])) {
                        $realIban = $matches[1];
                        
                        // Guardar correctament amb xifratge manual
                        $encryptedIban = Crypt::encrypt($realIban);
                        
                        DB::table('campus_teachers')
                            ->where('id', $teacher->id)
                            ->update(['iban' => $encryptedIban]);
                        
                        $count++;
                        $this->line("Teacher ID {$teacher->id}: {$realIban}");
                    }
                }
            }
            
            $this->info("S'han corregit {$count} registres d'IBAN");
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
