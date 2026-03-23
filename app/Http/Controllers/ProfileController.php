<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\CampusTeacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'notificationPreferences' => $this->getNotificationPreferences(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateLanguage(Request $request)
    {
        $request->validate([
            'language' => ['required', 'in:en,es,ca'],
        ]);

        // Guardar preferencia de usuario en JSON
        auth()->user()->setLanguage($request->language);

        // Actualizar el locale para la sesión actual
        app()->setLocale($request->language);
        session()->put('locale', $request->language);

        return redirect()->route('profile.edit')->with('status', 'language-updated');
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'notifications_email_enabled' => 'nullable|boolean',
            'notifications_web_enabled' => 'nullable|boolean',
            'notifications_support_email' => 'nullable|boolean',
            'notifications_support_web' => 'nullable|boolean',
            'notifications_department_email' => 'nullable|boolean',
            'notifications_department_web' => 'nullable|boolean',
            'notifications_admin_email' => 'nullable|boolean',
            'notifications_admin_web' => 'nullable|boolean',
            'notifications_frequency' => 'required|in:immediate,daily,weekly',
        ]);

        $user = auth()->user();
        $preferences = $user->preferences();

        // Update notification preferences
        $notificationPrefs = [
            'email_enabled' => $request->boolean('notifications_email_enabled', true),
            'web_enabled' => $request->boolean('notifications_web_enabled', true),
            'support_email' => $request->boolean('notifications_support_email', true),
            'support_web' => $request->boolean('notifications_support_web', true),
            'department_email' => $request->boolean('notifications_department_email', true),
            'department_web' => $request->boolean('notifications_department_web', true),
            'admin_email' => $request->boolean('notifications_admin_email', true),
            'admin_web' => $request->boolean('notifications_admin_web', true),
            'frequency' => $request->notifications_frequency,
        ];

        $preferences->updateNotificationPreferences($notificationPrefs);

        return redirect()->route('profile.edit')->with('status', 'notifications-updated');
    }

    /**
     * Get notification preferences for the current user
     */
    private function getNotificationPreferences()
    {
        $user = auth()->user();
        $preferences = $user->preferences();
        
        if ($preferences) {
            $prefs = $preferences->getNotificationPreferences();
            
            // Map JSON keys to form field names
            return [
                'notifications_email_enabled' => $prefs['email_enabled'] ?? true,
                'notifications_web_enabled' => $prefs['web_enabled'] ?? true,
                'notifications_support_email' => $prefs['support_email'] ?? true,
                'notifications_support_web' => $prefs['support_web'] ?? true,
                'notifications_department_email' => $prefs['department_email'] ?? true,
                'notifications_department_web' => $prefs['department_web'] ?? true,
                'notifications_admin_email' => $prefs['admin_email'] ?? true,
                'notifications_admin_web' => $prefs['admin_web'] ?? true,
                'notifications_frequency' => $prefs['frequency'] ?? 'immediate',
            ];
        }
        
        // Default values
        return [
            'notifications_email_enabled' => true,
            'notifications_web_enabled' => true,
            'notifications_support_email' => true,
            'notifications_support_web' => true,
            'notifications_department_email' => true,
            'notifications_department_web' => true,
            'notifications_admin_email' => true,
            'notifications_admin_web' => true,
            'notifications_frequency' => 'immediate',
        ];
    }

    /**
     * Update user's banking data.
     */
    public function updateBankingData(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        // Validar dades
        $validated = $request->validate([
            'iban' => ['required', 'string', 'regex:/^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$/'],
            'bank_titular' => ['required', 'string', 'max:255'],
            'fiscal_id' => ['nullable', 'string', 'max:50'],
            'fiscal_situation' => ['required', 'string', 'in:autonom,employee,pensioner,altre'],
            'invoice' => ['nullable', 'boolean'],
        ], [
            'iban.required' => __('L\'IBAN és obligatori'),
            'iban.regex' => __('L\'IBAN no té el format correcte'),
            'bank_titular.required' => __('El titular del compte és obligatori'),
            'fiscal_situation.required' => __('La situació fiscal és obligatòria'),
            'fiscal_situation.in' => __('La situació fiscal seleccionada no és vàlida'),
        ]);

        // Obtenir o crear el perfil de professor
        $teacher = $user->teacherProfile ?? new CampusTeacher();
        
        // Actualitzar dades
        $teacher->user_id = $user->id;
        $teacher->iban = $validated['iban'];
        $teacher->bank_titular = $validated['bank_titular'];
        $teacher->fiscal_id = $validated['fiscal_id'];
        $teacher->fiscal_situation = $validated['fiscal_situation'];
        $teacher->invoice = $validated['invoice'] ?? '0';
        
        // Guardar
        $teacher->save();
        
        return Redirect::route('profile.edit')->with('status', 'banking-data-updated');
    }

    /**
     * Generate banking data PDF.
     */
    public function generateBankingPDF(Request $request): JsonResponse
    {
        $user = Auth::user();
        $teacher = $user->teacherProfile;
        
        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => __('No hi ha dades bancàries disponibles')
            ], 404);
        }

        // Generar PDF (aquí aniria la lògica de generació de PDF)
        try {
            $pdfContent = $this->generateBankingPDFContent($teacher);
            
            // Guardar PDF a storage
            $filename = 'dades_bancaries_' . $user->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
            Storage::disk('public')->put('pdfs/' . $filename, $pdfContent);
            
            return response()->json([
                'success' => true,
                'message' => __('PDF generat correctament'),
                'filename' => $filename,
                'url' => Storage::url('pdfs/' . $filename)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error generant el PDF') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF content for banking data.
     */
    private function generateBankingPDFContent(CampusTeacher $teacher): string
    {
        // Aquesta és una versió simple - es pot millorar amb una llibreria PDF real
        $content = "
        <html>
        <head>
            <title>" . __('Dades Bancàries') . "</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .header { text-align: center; margin-bottom: 30px; }
                .section { margin-bottom: 20px; }
                .field { margin-bottom: 10px; }
                .label { font-weight: bold; }
                .value { margin-left: 10px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>" . __('Dades Bancàries de Cobrament') . "</h1>
                <p>" . __('Generat el') . " " . date('d/m/Y H:i') . "</p>
            </div>
            
            <div class='section'>
                <h2>" . __('Dades del Professor') . "</h2>
                <div class='field'>
                    <span class='label'>" . __('Nom') . ":</span>
                    <span class='value'>" . $teacher->full_name . "</span>
                </div>
                <div class='field'>
                    <span class='label'>" . __('DNI') . ":</span>
                    <span class='value'>" . $teacher->dni . "</span>
                </div>
                <div class='field'>
                    <span class='label'>" . __('Correu') . ":</span>
                    <span class='value'>" . $teacher->email . "</span>
                </div>
            </div>
            
            <div class='section'>
                <h2>" . __('Dades Bancàries') . "</h2>
                <div class='field'>
                    <span class='label'>" . __('IBAN') . ":</span>
                    <span class='value'>" . $teacher->formatted_iban . "</span>
                </div>
                <div class='field'>
                    <span class='label'>" . __('Titular del Compte') . ":</span>
                    <span class='value'>" . $teacher->bank_titular . "</span>
                </div>
                <div class='field'>
                    <span class='label'>" . __('Identificació Fiscal') . ":</span>
                    <span class='value'>" . ($teacher->fiscal_id ?: '-') . "</span>
                </div>
                <div class='field'>
                    <span class='label'>" . __('Situació Fiscal') . ":</span>
                    <span class='value'>" . $this->getFiscalSituationLabel($teacher->fiscal_situation) . "</span>
                </div>
                <div class='field'>
                    <span class='label'>" . __('Factura') . ":</span>
                    <span class='value'>" . ($teacher->invoice == '1' ? __('Sí') : __('No')) . "</span>
                </div>
            </div>
        </body>
        </html>";
        
        return $content;
    }

    /**
     * Get fiscal situation label.
     */
    private function getFiscalSituationLabel(?string $situation): string
    {
        $labels = [
            'autonom' => __('Autònom/a'),
            'employee' => __('Treballador/a per compte alié'),
            'pensioner' => __('Pensionista o jubilat/jubilada'),
            'altre' => __('Altre (no llistat)'),
        ];
        
        return $labels[$situation] ?? '-';
    }

}
