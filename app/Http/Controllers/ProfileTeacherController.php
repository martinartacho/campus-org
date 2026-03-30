<?php

namespace App\Http\Controllers;

use App\Models\CampusTeacher;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileTeacherController extends Controller
{
    /**
     * Show the teacher profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        $teacher = $user->teacherProfile;
        
        if (!$teacher) {
            return redirect()->route('dashboard')
                ->with('error', __('No tens perfil de professor associat.'));
        }
        
        return view('teacher.profile.edit-3-groups', compact('teacher'));
    }
    
    /**
     * Update the teacher profile.
     */
    public function update(Request $request): RedirectResponse
    {
        \Log::info('ProfileTeacherController::update STARTED', [
            'method' => $request->method(),
            'all_data' => $request->all(),
            'user_id' => Auth::id(),
        ]);
        
        $user = Auth::user();
        $teacher = $user->teacherProfile;
 
        if (!$teacher) {
            return redirect()->route('dashboard')
                ->with('error', __('No tens perfil de professor associat.'));
        }

        // Validació específica per teacher
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'in:' . $user->email],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'city' => ['nullable', 'string', 'max:100'],
            'payment_type' => ['required', 'in:waived,own,ceded'],
        ], [
            'first_name.required' => __('El nom és obligatori'),
            'last_name.required' => __('Els cognoms són obligatoris'),
            'email.required' => __('El correu és obligatori'),
            'email.email' => __('El correu no és vàlid'),
            'email.in' => __('El correu ha de coincidir amb el teu correu d\'usuari'),
            'payment_type.required' => __('Has de seleccionar un tipus de cobrament'),
            'payment_type.in' => __('El tipus de cobrament seleccionat no és vàlid'),
        ]);

        \Log::info('Validation passed', [
            'validated' => $validated,
            'teacher_id' => $teacher->id,
        ]);

        // Actualitzar dades del teacher
        $teacher->update($validated);
        
        \Log::info('Teacher updated successfully', [
            'teacher_id' => $teacher->id,
            'new_data' => $teacher->fresh()->toArray()
        ]);

        return redirect()->route('teacher.profile')
            ->with('success', __('Dades del professor actualitzades correctament'));
    }
}
