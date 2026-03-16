<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CampusTeacher;
use App\Models\CampusTeacherPayment;

class VerifyIbanEncryption extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encryption:verify-iban {--demo : Mostrar ejemplo de encriptación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar que los IBANs están encriptados correctamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔐 Verificando encriptación de IBANs...');
        $this->newLine();

        // 🎯 Demostración de encriptación
        if ($this->option('demo')) {
            $this->showEncryptionDemo();
            $this->newLine();
        }

        // 📊 Verificar CampusTeacher
        $this->verifyTeacherIbanEncryption();
        
        // 📊 Verificar CampusTeacherPayment
        $this->verifyPaymentIbanEncryption();

        $this->newLine();
        $this->info('✅ Verificación completada');
    }

    private function showEncryptionDemo()
    {
        $this->info('🎯 Demostración de encriptación de IBAN:');
        
        // Ejemplo de IBAN
        $iban = 'ES1234567890123456789012345';
        $this->line("📝 IBAN original: {$iban}");
        
        // Simular encriptación (Laravel lo hace automáticamente)
        $encrypted = encrypt($iban);
        $this->line("🔒 IBAN encriptado: " . substr($encrypted, 0, 50) . '...');
        
        $decrypted = decrypt($encrypted);
        $this->line("🔓 IBAN desencriptado: {$decrypted}");
        
        $this->newLine();
    }

    private function verifyTeacherIbanEncryption()
    {
        $this->info('👨‍🏫 Verificando IBANs en CampusTeacher:');
        
        $teachers = CampusTeacher::all();
        $encryptedCount = 0;
        $totalCount = $teachers->count();
        $withIbanCount = 0;
        
        foreach ($teachers as $teacher) {
            if ($teacher->iban) {
                $withIbanCount++;
                // Verificar si el IBAN está encriptado (empieza con prefijo de encriptación)
                if (str_starts_with($teacher->iban, 'eyJ') || strlen($teacher->iban) > 100) {
                    $encryptedCount++;
                }
            }
        }
        
        $this->line("📊 Total profesores: {$totalCount}");
        $this->line("💳 Con IBAN: {$withIbanCount}");
        $this->line("🔒 IBANs encriptados: {$encryptedCount}");
        
        if ($encryptedCount > 0) {
            $this->info('✅ IBANs de profesores encriptados correctamente');
        } else {
            $this->warn('⚠️ No se encontraron IBANs encriptados');
        }
        
        $this->newLine();
    }

    private function verifyPaymentIbanEncryption()
    {
        $this->info('💳 Verificando IBANs en CampusTeacherPayment:');
        
        $payments = CampusTeacherPayment::all();
        $encryptedCount = 0;
        $totalCount = $payments->count();
        $withIbanCount = 0;
        
        foreach ($payments as $payment) {
            if ($payment->iban) {
                $withIbanCount++;
                if (str_starts_with($payment->iban, 'eyJ') || strlen($payment->iban) > 100) {
                    $encryptedCount++;
                }
            }
        }
        
        $this->line("📊 Total pagos: {$totalCount}");
        $this->line("💳 Con IBAN: {$withIbanCount}");
        $this->line("🔒 IBANs encriptados: {$encryptedCount}");
        
        if ($encryptedCount > 0) {
            $this->info('✅ IBANs de pagos encriptados correctamente');
        } else {
            $this->warn('⚠️ No se encontraron IBANs encriptados');
        }
        
        $this->newLine();
    }
}
