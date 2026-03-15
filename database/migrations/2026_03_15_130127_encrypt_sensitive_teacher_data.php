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
        // 🔐 Encriptar datos existentes de profesores
        $teachers = CampusTeacher::all();
        foreach ($teachers as $teacher) {
            if ($teacher->iban) {
                $teacher->iban = $teacher->iban; // Laravel lo encriptará automáticamente
            }
            if ($teacher->bank_titular) {
                $teacher->bank_titular = $teacher->bank_titular;
            }
            if ($teacher->fiscal_id) {
                $teacher->fiscal_id = $teacher->fiscal_id;
            }
            if ($teacher->dni) {
                $teacher->dni = $teacher->dni;
            }
            if ($teacher->phone) {
                $teacher->phone = $teacher->phone;
            }
            if ($teacher->address) {
                $teacher->address = $teacher->address;
            }
            if ($teacher->postal_code) {
                $teacher->postal_code = $teacher->postal_code;
            }
            if ($teacher->email) {
                $teacher->email = $teacher->email;
            }
            $teacher->save();
        }

        // 🔐 Encriptar datos existentes de pagos de profesores
        $payments = CampusTeacherPayment::all();
        foreach ($payments as $payment) {
            if ($payment->first_name) {
                $payment->first_name = $payment->first_name;
            }
            if ($payment->last_name) {
                $payment->last_name = $payment->last_name;
            }
            if ($payment->fiscal_id) {
                $payment->fiscal_id = $payment->fiscal_id;
            }
            if ($payment->postal_code) {
                $payment->postal_code = $payment->postal_code;
            }
            if ($payment->city) {
                $payment->city = $payment->city;
            }
            if ($payment->iban) {
                $payment->iban = $payment->iban;
            }
            if ($payment->bank_titular) {
                $payment->bank_titular = $payment->bank_titular;
            }
            if ($payment->fiscal_situation) {
                $payment->fiscal_situation = $payment->fiscal_situation;
            }
            if ($payment->invoice) {
                $payment->invoice = $payment->invoice;
            }
            if ($payment->observacions) {
                $payment->observacions = $payment->observacions;
            }
            $payment->save();
        }

        // 🔐 Encriptar datos existentes de usuarios
        $users = User::all();
        foreach ($users as $user) {
            if ($user->email) {
                $user->email = $user->email;
            }
            if ($user->fcm_token) {
                $user->fcm_token = $user->fcm_token;
            }
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
