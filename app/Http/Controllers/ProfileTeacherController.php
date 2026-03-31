<?php

namespace App\Http\Controllers;

use App\Models\CampusTeacher;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileTeacherController extends Controller
{
    /**
     * Show the teacher profile edit form.
     */
    public function edit(): View
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
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'in:' . $user->email],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'city' => ['nullable', 'string', 'max:100'],
            'payment_type' => ['required', 'in:waived,own,ceded'],
            'observacions' => ['nullable', 'string', 'max:1000'],
            'data_consent' => ['nullable', 'boolean'],
            'fiscal_responsibility' => ['nullable', 'boolean'],
        ];

        // Afegir validació bancària si és 'own'
        if ($request->input('payment_type') === 'own') {
            $bankingRules = [
                'dni' => ['required', 'string', 'max:20'],
                'bank_titular' => ['required', 'string', 'max:255'],
                'fiscal_id' => ['nullable', 'string', 'max:20'],
                'fiscal_situation' => ['required', 'in:autonom,employee,pensioner,other'],
                'invoice' => ['nullable', 'boolean'],
            ];
            
            // Només validar IBAN si s'ha proporcionat un valor
            if (!empty($request->input('iban'))) {
                $bankingRules['iban'] = ['required', 'string'];
            }
            
            $rules = array_merge($rules, $bankingRules);
        }

        $messages = [
            'first_name.required' => __('El nom és obligatori'),
            'last_name.required' => __('Els cognoms són obligatoris'),
            'email.required' => __('El correu és obligatori'),
            'email.email' => __('El correu no és vàlid'),
            'email.in' => __('El correu ha de coincidir amb el teu correu d\'usuari'),
            'payment_type.required' => __('Has de seleccionar un tipus de cobrament'),
            'payment_type.in' => __('El tipus de cobrament seleccionat no és vàlid'),
            'observacions.max' => __('Les observacions no poden superar els 1000 caràcters'),
            'data_consent.required' => __('Has d\'acceptar el consentiment de dades'),
            'fiscal_responsibility.required' => __('Has d\'acceptar la responsabilitat fiscal'),
            // Missatges bancaris
            'dni.required' => __('El DNI és obligatori'),
            'iban.required' => __('L\'IBAN és obligatori'),
            'bank_titular.required' => __('El titular del compte és obligatori'),
            'fiscal_situation.required' => __('La situació fiscal és obligatòria'),
            'fiscal_situation.in' => __('La situació fiscal seleccionada no és vàlida'),
        ];

        $validated = $request->validate($rules, $messages);

        // Sincronitzar fiscal_id amb dni si està buit
        if ($request->input('payment_type') === 'own' && empty($validated['fiscal_id'])) {
            $validated['fiscal_id'] = $validated['dni'];
            \Log::info('Fiscal ID synchronized with DNI', [
                'dni' => $validated['dni'],
                'fiscal_id' => $validated['fiscal_id']
            ]);
        }

        // Només processar IBAN si s'ha proporcionat i és diferent del guardat
        if (isset($validated['iban']) && $request->input('payment_type') === 'own' && !empty($validated['iban'])) {
            $currentIban = $teacher->decrypted_iban;
            $newIban = $validated['iban'];
            
            // Si l'IBAN és igual al guardat, no processar
            if ($currentIban === $newIban) {
                unset($validated['iban']);
                \Log::info('IBAN unchanged, skipping encryption', [
                    'teacher_id' => $teacher->id,
                    'iban' => 'unchanged'
                ]);
            } else {
                \Log::info('IBAN changed, processing encryption', [
                    'teacher_id' => $teacher->id,
                    'old_iban_length' => strlen($currentIban),
                    'new_iban_length' => strlen($newIban)
                ]);
            }
        } elseif ($request->input('payment_type') === 'own' && empty($request->input('iban'))) {
            // Si l'IBAN està buit, no processar-lo
            unset($validated['iban']);
            \Log::info('IBAN empty, skipping processing', [
                'teacher_id' => $teacher->id
            ]);
        }

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

    /**
     * Generate PDF with teacher data and save to server.
     */
    public function generatePDF(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $teacher = $user->teacherProfile;

        if (!$teacher) {
            return response()->json(['error' => 'No tens perfil de professor associat.'], 404);
        }

        // Comprovar si té les autoritzacions necessàries
        if (!$teacher->data_consent || !$teacher->fiscal_responsibility) {
            return redirect()->route('teacher.profile')
            ->with('error', 'Cal acceptar les autoritzacions necessàries.');
        }

        try {
            // Crear directori si no existeix
            $directory = storage_path('app/consents/teachers/' . $teacher->id);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Nom del fitxer amb data
            $filename = 'consent_dades_certes_' . now()->format('Ymd') . '.pdf';
            $filepath = $directory . '/' . $filename;

            // Dades pel PDF
            $pdfData = [
                'teacher' => $teacher,
                'user' => $user,
                'date' => now()->format('d/m/Y'),
                'payment_type' => $teacher->payment_type,
                'data_consent' => $teacher->data_consent,
                'fiscal_responsibility' => $teacher->fiscal_responsibility,
                'declaracioFiscal' => $teacher->fiscal_responsibility,
                'autoritzacioDades' => $teacher->data_consent,
            ];

            // Generar PDF
            $pdf = \PDF::loadView('teacher.profile.pdf', $pdfData);
            
            // Guardar al servidor
            $pdf->save($filepath);

            // Sincronitzar taules
            $this->syncConsentHistory($teacher, $filepath);
            $this->syncTeacherPayments($teacher);

            // Enviar notificacions
            $this->sendNotifications($teacher, $filename);

            // Retornar enllaç de descàrrega
            $downloadUrl = route('teacher.profile.download', ['filename' => $filename]);

            return redirect()->route('teacher.profile.download', [
                'filename' => $filename
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating PDF', [
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('teacher.profile')
            ->with('error', 'No tens perfil de professor associat.');
           
        }
    }



    /**
     * Sincronitzar consent history
     */
    private function syncConsentHistory($teacher, $filepath): void
    {
        // Aquí implementar la sincronització amb consent_histories
        \Log::info('Sync consent history', [
            'teacher_id' => $teacher->id,
            'filepath' => $filepath
        ]);
    }

    /**
     * Sincronitzar teacher payments
     */
    private function syncTeacherPayments($teacher): void
    {
        // Aquí implementar la sincronització amb campus_teacher_payments
        \Log::info('Sync teacher payments', [
            'teacher_id' => $teacher->id,
            'payment_type' => $teacher->payment_type
        ]);
    }

    /**
     * Enviar notificacions
     */
    private function sendNotifications($teacher, $filename): void
    {
        // Aquí implementar les notificacions al professor i tresoreria
        \Log::info('Send notifications', [
            'teacher_id' => $teacher->id,
            'filename' => $filename
        ]);
    }

    /**
     * Download PDF from server
     */
    public function downloadPDF($filename): Response|RedirectResponse
    {
        $user = Auth::user();
        $teacher = $user->teacherProfile;

        if (!$teacher) {
            return redirect()->route('teacher.profile')
                ->with('error', 'Error generant el PDF.');
        }

        $filepath = storage_path('app/consents/teachers/' . $teacher->id . '/' . $filename);

        if (!file_exists($filepath)) {
            return response()->json(['error' => 'Fitxer no trobat.'], 404);
        }

        return redirect()->route('teacher.profile')
            ->with('success', 'PDF generat correctament. Pots descarregar-lo des d\'aquí.');

    }

}
