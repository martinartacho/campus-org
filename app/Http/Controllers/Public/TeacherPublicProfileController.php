<?php

namespace App\Http\Controllers\Public;

use App\Models\TeacherAccessToken;
use App\Models\CampusTeacher;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeacherPublicProfileController extends Controller
{

    public function tabDadesPersonals(Request $request, string $token)
    {
        \Log::info('=== TAB DADES PERSONALS START ===');
        \Log::info('Token:', ['token' => $token]);
        dd('STOP TAB DADES PERSONALS START');
        // Validar token
        $accessToken = TeacherAccessToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        // Obtener usuario y profesor
        $user = User::findOrFail($accessToken->teacher_id);
        $teacher = CampusTeacher::where('user_id', $user->id)->first();

        // Validar datos personales
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'dni' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'city' => ['nullable', 'string', 'max:100'],
        ]);

        // Actualizar datos del usuario
        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
        ]);

        // Actualizar datos del profesor
        if ($teacher) {
            $teacher->update([
                'phone' => $data['phone'],
                'dni' => $data['dni'],
                'address' => $data['address'],
                'postal_code' => $data['postal_code'],
                'city' => $data['city'],
            ]);
        } else {
            // Crear profesor si no existe
            CampusTeacher::create([
                'user_id' => $user->id,
                'phone' => $data['phone'],
                'dni' => $data['dni'],
                'address' => $data['address'],
                'postal_code' => $data['postal_code'],
                'city' => $data['city'],
            ]);
        }

        // Obtener datos actualizados para mostrar
        $updatedUser = $user->fresh();
        $updatedTeacher = CampusTeacher::where('user_id', $user->id)->first();

        \Log::info('Datos actualizados correctamente');

        // Redirigir de vuelta al formulario con mensaje de éxito
        return redirect()->route('teacher.access.payments', $token)
            ->with('success', 'Datos personales guardados correctamente. Puedes continuar con los datos de pago.');
    }

    public function update(Request $request, string $token)
    {
        \Log::info('=== UPDATE START TeacherPublicProfileController ===');
        \Log::info('Token:', ['token' => $token]);
        
        $accessToken = TeacherAccessToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        // Obtener usuario y profesor
        $user = User::findOrFail($accessToken->teacher_id);
        $teacher = CampusTeacher::where('user_id', $user->id)->firstOrFail();

        // VALIDACIONES
        $data = $request->validate([
            'email' => ['required', 'email'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'consent_rgpd' => ['required', 'accepted'],
            // 'needs_payment' => ['nullable', 'boolean'],
            'dni' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string'],
            'iban' => ['nullable', 'string'],
            'bank_holder' => ['nullable', 'string'],
        ]);

        \Log::info('Datos validados:', $data);

        // Actualizar profesor
        $teacher->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'dni' => $data['dni'] ?? null,
        ]);

        // Actualizar usuario
        $user->update([
            'email' => $data['email'],
            'name' => $data['first_name'] . ' ' . $data['last_name'],
        ]);

        // Marcar token como usado
        $accessToken->update(['used_at' => now()]);

        \Log::info('=== UPDATE COMPLETED TeacherPublicProfileController ===');

        return view('treasury.public.completed');
    }
}