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
     * Show the teacher profile edit form.
     */
    public function teacherEdit()
    {
        $user = Auth::user();
        $teacher = $user->teacherProfile;
        
        if (!$teacher) {
            return redirect()->route('dashboard')
                ->with('error', __('No tens perfil de professor associat.'));
        }
        
        return view('teacher.profile.edit', compact('teacher'));
    }
    
    /**
     * Update the teacher profile.
     */
/**
     * Update the teacher profile.
     */
    public function teacherUpdate(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $teacher = $user->teacherProfile;
 
        if (!$teacher) {
            return redirect()->route('dashboard')
                ->with('error', __('No tens perfil de professor associat.'));
        }
 
        // Validació base per a tots els tipus
        $baseRules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'city' => ['nullable', 'string', 'max:100'],
            'payment_type' => ['required', 'in:waived,own,ceded'],
        ];
 
        // Validació específica segons tipus de pagament
        $paymentType = $request->input('payment_type');
 
        if ($paymentType === 'waived') {
            $baseRules['waived_confirmation'] = ['required', 'accepted'];
        } elseif ($paymentType === 'own') {
            $baseRules = array_merge($baseRules, [
                'dni' => ['required', 'string', 'max:20'],
                'iban' => ['required', 'string', 'regex:/^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$/'],
                'bank_titular' => ['required', 'string', 'max:255'],
                'fiscal_id' => ['nullable', 'string', 'max:20'],
                'fiscal_situation' => ['required', 'in:autonom,employee,pensioner,other'],
                'invoice' => ['nullable', 'boolean'],
                'own_confirmation' => ['required', 'accepted'],
            ]);
        } elseif ($paymentType === 'ceded') {
            $baseRules = array_merge($baseRules, [
                'beneficiary_dni' => ['required', 'string', 'max:20'],
                'beneficiary_iban' => ['required', 'string', 'regex:/^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$/'],
                'beneficiary_titular' => ['required', 'string', 'max:255'],
                'beneficiary_fiscal_situation' => ['required', 'in:autonom,employee,pensioner,other'],
                'beneficiary_city' => ['required', 'string', 'max:100'],
                'beneficiary_postal_code' => ['nullable', 'string', 'max:10'],
                'beneficiary_invoice' => ['nullable', 'boolean'],
                'ceded_confirmation' => ['required', 'accepted'],
            ]);
        }
 
        $validated = $request->validate($baseRules, [
            // Missatges base
            'first_name.required' => __('El nom és obligatori'),
            'last_name.required' => __('Els cognoms són obligatoris'),
            'email.required' => __('El correu és obligatori'),
            'email.email' => __('El correu no és vàlid'),
            'payment_type.required' => __('Has de seleccionar un tipus de cobrament'),
            'payment_type.in' => __('El tipus de cobrament seleccionat no és vàlid'),
 
            // Missatges waived
            'waived_confirmation.required' => __('Has de confirmar que no cobraràs'),
            'waived_confirmation.accepted' => __('Has de confirmar que no cobraràs'),
 
            // Missatges own
            'dni.required' => __('El DNI és obligatori'),
            'iban.required' => __('L\'IBAN és obligatori'),
            'iban.regex' => __('L\'IBAN no té el format correcte'),
            'bank_titular.required' => __('El titular del compte és obligatori'),
            'fiscal_situation.required' => __('La situació fiscal és obligatòria'),
            'fiscal_situation.in' => __('La situació fiscal seleccionada no és vàlida'),
            'own_confirmation.required' => __('Has de confirmar que cobraràs'),
            'own_confirmation.accepted' => __('Has de confirmar que cobraràs'),
 
            // Missatges ceded
            'beneficiary_dni.required' => __('El DNI del beneficiari és obligatori'),
            'beneficiary_iban.required' => __('L\'IBAN del beneficiari és obligatori'),
            'beneficiary_iban.regex' => __('L\'IBAN del beneficiari no té el format correcte'),
            'beneficiary_titular.required' => __('El titular del beneficiari és obligatori'),
            'beneficiary_fiscal_situation.required' => __('La situació fiscal del beneficiari és obligatòria'),
            'beneficiary_fiscal_situation.in' => __('La situació fiscal del beneficiari no és vàlida'),
            'beneficiary_city.required' => __('La ciutat del beneficiari és obligatòria'),
            'ceded_confirmation.required' => __('Has de confirmar que cedeixes el cobrament'),
            'ceded_confirmation.accepted' => __('Has de confirmar que cedeixes el cobrament'),
        ]);
 
        // Actualitzar dades base
        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'postal_code' => $validated['postal_code'],
            'city' => $validated['city'],
            'payment_type' => $validated['payment_type'],
        ];
 
        // Actualitzar dades específiques segons tipus
        if ($paymentType === 'waived') {
            $updateData['waived_confirmation'] = true;
            $updateData['payment_status'] = 'confirmed';
            $updateData['payment_confirmed_at'] = now();
        } elseif ($paymentType === 'own') {
            $updateData = array_merge($updateData, [
                'dni' => $validated['dni'],
                'iban' => $validated['iban'],
                'bank_titular' => $validated['bank_titular'],
                'fiscal_id' => $validated['fiscal_id'] ?? null,
                'fiscal_situation' => $validated['fiscal_situation'],
                'invoice' => $validated['invoice'] ?? false,
                'own_confirmation' => true,
                'payment_status' => 'confirmed',
                'payment_confirmed_at' => now(),
            ]);
        } elseif ($paymentType === 'ceded') {
            $updateData = array_merge($updateData, [
                'beneficiary_dni' => $validated['beneficiary_dni'],
                'beneficiary_iban' => $validated['beneficiary_iban'],
                'beneficiary_titular' => $validated['beneficiary_titular'],
                'beneficiary_fiscal_situation' => $validated['beneficiary_fiscal_situation'],
                'beneficiary_city' => $validated['beneficiary_city'],
                'beneficiary_postal_code' => $validated['beneficiary_postal_code'] ?? null,
                'beneficiary_invoice' => $validated['beneficiary_invoice'] ?? false,
                'ceded_confirmation' => true,
                'payment_status' => 'confirmed',
                'payment_confirmed_at' => now(),
            ]);
        }
 
        // Actualitzar professor
        $teacher->update($updateData);
 
        // Missatge d'èxit segons tipus
        $successMessage = match($paymentType) {
            'waived' => __('Dades de no cobrament guardades correctament'),
            'own' => __('Dades de cobrament propi guardades correctament'),
            'ceded' => __('Dades de cobrament cedit guardades correctament'),
            default => __('Dades guardades correctament'),
        };
 
        return redirect()->route('teacher.profile')
            ->with('success', $successMessage);
    }

    /**
     * Download private PDF securely.
     */
    public function downloadPDF($path)
    {
        $user = Auth::user();
        
        // Verificar que el path pertany a l'usuari actual
        if (!str_starts_with($path, 'pdfs/tresoreria/' . $user->teacherProfile->id . '/')) {
            abort(403, __('Accés no autoritzat'));
        }
        
        if (!Storage::disk('local')->exists($path)) {
            abort(404, __('PDF no trobat'));
        }
        
        $fileContents = Storage::disk('local')->get($path);
        $filename = basename($path);
        
        return response($fileContents)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Generate payment data PDF.
     */
    public function generatePaymentPDF(Request $request): JsonResponse
    {
        $user = Auth::user();
        $teacher = $user->teacherProfile;
        
        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => __('No hi ha dades de pagament disponibles')
            ], 404);
        }

        // Generar PDF segons tipus de cobrament
        try {
            $pdfContent = $this->generatePaymentPDFContent($teacher);
            
            // Guardar PDF a storage privat
            $filename = 'dades_pagament_' . $user->id . '_' . $teacher->payment_type . '_' . date('Y-m-d_H-i-s') . '.pdf';
            Storage::disk('local')->put('pdfs/tresoreria/' . $teacher->id . '/' . $filename, $pdfContent);
            
            // Crear URL temporal per descarregar
            $temporaryUrl = URL::temporarySignedRoute(
                'pdfs.download', 
                now()->addMinutes(15), 
                ['path' => 'pdfs/tresoreria/' . $teacher->id . '/' . $filename]
            );
            
            return response()->json([
                'success' => true,
                'message' => __('PDF de pagament generat correctament'),
                'filename' => $filename,
                'url' => $temporaryUrl,
                'download_url' => $temporaryUrl
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error generant el PDF de pagament') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF content for payment data.
     */
    private function generatePaymentPDFContent(CampusTeacher $teacher): string
    {
        $user = $teacher->user;
        $paymentType = $teacher->payment_type;
        
        // Contingut segons tipus de cobrament
        $content = match($paymentType) {
            'waived' => $this->generateWaivedPDFContent($teacher, $user),
            'own' => $this->generateOwnPDFContent($teacher, $user),
            'ceded' => $this->generateCededPDFContent($teacher, $user),
            default => 'Tipus de cobrament no vàlid'
        };
        
        return $content;
    }
    
    /**
     * Generate PDF content for waived payment.
     */
    private function generateWaivedPDFContent(CampusTeacher $teacher, User $user): string
    {
        return "
        <h1>Confirmació de No Cobrament</h1>
        <h2>Dades del Professor</h2>
        <p><strong>Nom:</strong> {$teacher->first_name} {$teacher->last_name}</p>
        <p><strong>Correu:</strong> {$teacher->email}</p>
        <p><strong>DNI:</strong> {$teacher->dni}</p>
        <p><strong>Telèfon:</strong> {$teacher->phone}</p>
        <p><strong>Adreça:</strong> {$teacher->address}</p>
        <p><strong>Ciutat:</strong> {$teacher->city}</p>
        <p><strong>CP:</strong> {$teacher->postal_code}</p>
        
        <h2>Confirmació</h2>
        <p><strong>Opció seleccionada:</strong> No cobraré per la meva docència</p>
        <p><strong>Data de confirmació:</strong> " . now()->format('d/m/Y H:i') . "</p>
        <p><strong>ID Professor:</strong> {$teacher->id}</p>
        ";
    }
    
    /**
     * Generate PDF content for own payment.
     */
    private function generateOwnPDFContent(CampusTeacher $teacher, User $user): string
    {
        return "
        <h1>Dades de Cobrament Propi</h1>
        <h2>Dades del Professor</h2>
        <p><strong>Nom:</strong> {$teacher->first_name} {$teacher->last_name}</p>
        <p><strong>Correu:</strong> {$teacher->email}</p>
        <p><strong>DNI:</strong> {$teacher->dni}</p>
        
        <h2>Dades Bancàries</h2>
        <p><strong>IBAN:</strong> {$teacher->iban}</p>
        <p><strong>Titular:</strong> {$teacher->bank_titular}</p>
        <p><strong>Situació Fiscal:</strong> {$teacher->fiscal_situation}</p>
        <p><strong>Presentarà factura:</strong> " . ($teacher->invoice ? 'Sí' : 'No') . "</p>
        
        <h2>Confirmació</h2>
        <p><strong>Opció seleccionada:</strong> Cobraré jo mateix/a</p>
        <p><strong>Data de confirmació:</strong> " . now()->format('d/m/Y H:i') . "</p>
        <p><strong>ID Professor:</strong> {$teacher->id}</p>
        ";
    }
    
    /**
     * Generate PDF content for ceded payment.
     */
    private function generateCededPDFContent(CampusTeacher $teacher, User $user): string
    {
        return "
        <h1>Dades de Cobrament Cedit</h1>
        <h2>Dades del Professor</h2>
        <p><strong>Nom:</strong> {$teacher->first_name} {$teacher->last_name}</p>
        <p><strong>Correu:</strong> {$teacher->email}</p>
        
        <h2>Dades del Beneficiari</h2>
        <p><strong>DNI Beneficiari:</strong> {$teacher->beneficiary_dni}</p>
        <p><strong>IBAN Beneficiari:</strong> {$teacher->beneficiary_iban}</p>
        <p><strong>Titular Beneficiari:</strong> {$teacher->beneficiary_titular}</p>
        <p><strong>Situació Fiscal Beneficiari:</strong> {$teacher->beneficiary_fiscal_situation}</p>
        <p><strong>Ciutat Beneficiari:</strong> {$teacher->beneficiary_city}</p>
        <p><strong>Presentarà factura:</strong> " . ($teacher->beneficiary_invoice ? 'Sí' : 'No') . "</p>
        
        <h2>Confirmació</h2>
        <p><strong>Opció seleccionada:</strong> Cedeixo el cobrament al beneficiari</p>
        <p><strong>Data de confirmació:</strong> " . now()->format('d/m/Y H:i') . "</p>
        <p><strong>ID Professor:</strong> {$teacher->id}</p>
        ";
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
            
            // Guardar PDF a storage privat (seguretat!)
            $filename = 'dades_bancaries_' . $user->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
            Storage::disk('local')->put('pdfs/tresoreria/' . $teacher->id . '/' . $filename, $pdfContent);
            
            // Crear URL temporal per descarregar
            $temporaryUrl = URL::temporarySignedRoute(
                'pdfs.download', 
                now()->addMinutes(15), 
                ['path' => 'pdfs/tresoreria/' . $teacher->id . '/' . $filename]
            );
            
            return response()->json([
                'success' => true,
                'message' => __('PDF generat correctament'),
                'filename' => $filename,
                'url' => $temporaryUrl,
                'download_url' => $temporaryUrl
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
