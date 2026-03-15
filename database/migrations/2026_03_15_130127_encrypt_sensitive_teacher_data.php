<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\CampusTeacher;
use App\Models\CampusTeacherPayment;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 🔐 Encriptar SOLO datos realmente sensibles de profesores
        $teachers = CampusTeacher::all();
        foreach ($teachers as $teacher) {
            if ($teacher->iban) {
                $teacher->iban = $teacher->iban; // IBAN - muy sensible
            }
            if ($teacher->bank_titular) {
                $teacher->bank_titular = $teacher->bank_titular; // Titular bancario - sensible
            }
            if ($teacher->fiscal_id) {
                $teacher->fiscal_id = $teacher->fiscal_id; // ID fiscal - sensible
            }
            if ($teacher->dni) {
                $teacher->dni = $teacher->dni; // DNI - muy sensible
            }
            if ($teacher->address) {
                $teacher->address = $teacher->address; // Dirección completa - sensible
            }
            // ❌ NO encriptar email, phone, postal_code - necesarios para búsquedas
            $teacher->save();
        }

        // 🔐 Encriptar SOLO datos sensibles de pagos de profesores
        $payments = CampusTeacherPayment::all();
        foreach ($payments as $payment) {
            if ($payment->fiscal_id) {
                $payment->fiscal_id = $payment->fiscal_id; // ID fiscal - sensible
            }
            if ($payment->iban) {
                $payment->iban = $payment->iban; // IBAN - muy sensible
            }
            if ($payment->bank_titular) {
                $payment->bank_titular = $payment->bank_titular; // Titular bancario - sensible
            }
            if ($payment->fiscal_situation) {
                $payment->fiscal_situation = $payment->fiscal_situation; // Situación fiscal - sensible
            }
            if ($payment->invoice) {
                $payment->invoice = $payment->invoice; // Datos de factura - sensible
            }
            if ($payment->observacions) {
                $payment->observacions = $payment->observacions; // Observaciones privadas
            }
            // ❌ NO encriptar first_name, last_name, postal_code, city - necesarios para búsquedas
            $payment->save();
        }

        // 🔐 Encriptar SOLO datos sensibles de usuarios
        $users = User::all();
        foreach ($users as $user) {
            if ($user->fcm_token) {
                $user->fcm_token = $user->fcm_token; // Token FCM - sensible
            }
            // ❌ NO encriptar email - necesario para login y búsquedas
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 🔄 Desencriptar datos (si es necesario revertir)
        // Nota: Los datos encriptados permanecerán encriptados
        // Esta migración es principalmente para activar los casts
    }
};
