<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo " Iniciando encriptación LIMPIA de IBANs...\n";
        
        // Procesar profesores
        $teachers = DB::table('campus_teachers')->whereNotNull('iban')->get();
        $encryptedCount = 0;
        
        foreach ($teachers as $teacher) {
            try {
                // Verificar que no esté ya encriptado
                if (!str_starts_with($teacher->iban, 'eyJ') && strlen($teacher->iban) < 100) {
                    $encryptedIban = encrypt($teacher->iban);
                    
                    DB::table('campus_teachers')
                        ->where('id', $teacher->id)
                        ->update(['iban' => $encryptedIban]);
                    
                    $encryptedCount++;
                    echo " Profesor {$teacher->id}: IBAN encriptado\n";
                } else {
                    echo " Profesor {$teacher->id}: IBAN ya encriptado\n";
                }
            } catch (\Exception $e) {
                echo " Profesor {$teacher->id}: Error - " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n Profesores procesados: {$encryptedCount} IBANs encriptados\n";
        
        // Procesar pagos
        $payments = DB::table('campus_teacher_payments')->whereNotNull('iban')->get();
        $paymentEncryptedCount = 0;
        
        foreach ($payments as $payment) {
            try {
                if (!str_starts_with($payment->iban, 'eyJ') && strlen($payment->iban) < 100) {
                    $encryptedIban = encrypt($payment->iban);
                    
                    DB::table('campus_teacher_payments')
                        ->where('id', $payment->id)
                        ->update(['iban' => $encryptedIban]);
                    
                    $paymentEncryptedCount++;
                    echo " Pago {$payment->id}: IBAN encriptado\n";
                } else {
                    echo " Pago {$payment->id}: IBAN ya encriptado\n";
                }
            } catch (\Exception $e) {
                echo " Pago {$payment->id}: Error - " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n Pagos procesados: {$paymentEncryptedCount} IBANs encriptados\n";
        echo " Encriptación LIMPIA completada\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo " Esta migración no tiene rollback automático\n";
        echo " Para revertir, necesitarías los datos originales\n";
    }
};
