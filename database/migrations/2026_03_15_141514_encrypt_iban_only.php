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
                $teacher->iban = $teacher->iban; // Laravel lo encriptará automáticamente
                $teacher->save();
            }
        }

        // 🔐 Encriptar SOLO IBAN de pagos de profesores
        $payments = CampusTeacherPayment::all();
        foreach ($payments as $payment) {
            if ($payment->iban) {
                $payment->iban = $payment->iban; // Laravel lo encriptará automáticamente
                $payment->save();
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
