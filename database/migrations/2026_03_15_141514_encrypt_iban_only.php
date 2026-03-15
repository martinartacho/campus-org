<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\CampusTeacher;
use App\Models\CampusTeacherPayment;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 🔐 Encriptar SOLO IBAN de profesores
        $teachers = CampusTeacher::all();
        foreach ($teachers as $teacher) {
            if ($teacher->iban) {
                try {
                    // Verificar si ya está encriptado
                    if (!str_starts_with($teacher->iban, 'eyJ') && strlen($teacher->iban) < 100) {
                        $teacher->iban = $teacher->iban; // Laravel lo encriptará automáticamente
                        $teacher->save();
                        echo "✅ IBAN de profesor {$teacher->id} encriptado\n";
                    } else {
                        echo "⚠️ IBAN de profesor {$teacher->id} ya estaba encriptado\n";
                    }
                } catch (\Exception $e) {
                    echo "❌ Error encriptando IBAN de profesor {$teacher->id}: " . $e->getMessage() . "\n";
                }
            }
        }

        // 🔐 Encriptar SOLO IBAN de pagos de profesores
        $payments = CampusTeacherPayment::all();
        foreach ($payments as $payment) {
            if ($payment->iban) {
                try {
                    // Verificar si ya está encriptado
                    if (!str_starts_with($payment->iban, 'eyJ') && strlen($payment->iban) < 100) {
                        $payment->iban = $payment->iban; // Laravel lo encriptará automáticamente
                        $payment->save();
                        echo "✅ IBAN de pago {$payment->id} encriptado\n";
                    } else {
                        echo "⚠️ IBAN de pago {$payment->id} ya estaba encriptado\n";
                    }
                } catch (\Exception $e) {
                    echo "❌ Error encriptando IBAN de pago {$payment->id}: " . $e->getMessage() . "\n";
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 🔄 Desencriptar IBAN (si es necesario revertir)
        // Laravel maneja esto automáticamente cuando se quita el cast
    }
};
