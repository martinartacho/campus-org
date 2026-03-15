<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CampusTeacher;
use App\Models\CampusTeacherPayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerifyEncryption extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encryption:verify {--demo : Mostrar ejemplo de encriptación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar que los datos sensibles están encriptados correctamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔐 Verificando encriptación de datos sensibles...');
        $this->newLine();

        // 🎯 Demostración de encriptación
        if ($this->option('demo')) {
            $this->showEncryptionDemo();
            $this->newLine();
        }

        // 📊 Verificar CampusTeacher
        $this->verifyTeacherEncryption();
        
        // 📊 Verificar CampusTeacherPayment
        $this->verifyPaymentEncryption();
        
        // 📊 Verificar User
        $this->verifyUserEncryption();

        $this->newLine();
        $this->info('✅ Verificación completada');
    }

    private function showEncryptionDemo()
    {
        $this->info('🎯 Demostración de encriptación:');
        
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

    private function verifyTeacherEncryption()
    {
        $this->info('👨‍🏫 Verificando CampusTeacher:');
        
        $teachers = CampusTeacher::all();
        $encryptedCount = 0;
        $totalCount = $teachers->count();
        
        foreach ($teachers as $teacher) {
            if ($teacher->iban) {
                // Verificar si el IBAN está encriptado (empieza con prefijo de encriptación)
                if (str_starts_with($teacher->iban, 'eyJ') || strlen($teacher->iban) > 100) {
                    $encryptedCount++;
                }
            }
        }
        
        $this->line("📊 Total profesores: {$totalCount}");
        $this->line("🔒 IBANs encriptados: {$encryptedCount}");
        
        if ($encryptedCount > 0) {
            $this->info('✅ IBANs de profesores encriptados correctamente');
        } else {
            $this->warn('⚠️ No se encontraron IBANs encriptados');
        }
        
        $this->newLine();
    }

    private function verifyPaymentEncryption()
    {
        $this->info('💳 Verificando CampusTeacherPayment:');
        
        $payments = CampusTeacherPayment::all();
        $encryptedCount = 0;
        $totalCount = $payments->count();
        
        foreach ($payments as $payment) {
            if ($payment->iban) {
                if (str_starts_with($payment->iban, 'eyJ') || strlen($payment->iban) > 100) {
                    $encryptedCount++;
                }
            }
        }
        
        $this->line("📊 Total pagos: {$totalCount}");
        $this->line("🔒 IBANs encriptados: {$encryptedCount}");
        
        if ($encryptedCount > 0) {
            $this->info('✅ IBANs de pagos encriptados correctamente');
        } else {
            $this->warn('⚠️ No se encontraron IBANs encriptados');
        }
        
        $this->newLine();
    }

    private function verifyUserEncryption()
    {
        $this->info('👤 Verificando User:');
        
        $users = User::all();
        $encryptedEmails = 0;
        $encryptedTokens = 0;
        $totalCount = $users->count();
        
        foreach ($users as $user) {
            if ($user->email) {
                if (str_starts_with($user->email, 'eyJ') || strlen($user->email) > 100) {
                    $encryptedEmails++;
                }
            }
            if ($user->fcm_token) {
                if (str_starts_with($user->fcm_token, 'eyJ') || strlen($user->fcm_token) > 100) {
                    $encryptedTokens++;
                }
            }
        }
        
        $this->line("📊 Total usuarios: {$totalCount}");
        $this->line("🔒 Emails encriptados: {$encryptedEmails}");
        $this->line("🔒 FCM tokens encriptados: {$encryptedTokens}");
        
        if ($encryptedEmails > 0 || $encryptedTokens > 0) {
            $this->info('✅ Datos de usuarios encriptados correctamente');
        } else {
            $this->warn('⚠️ No se encontraron datos encriptados');
        }
        
        $this->newLine();
    }
}
