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

        // Obtenir l'últim PDF generat per aquest professor
        $latestPdf = $this->getLatestPdf($teacher);
        
        // Obtenir llistat de tots els PDFs (màxim 3)
        $allPdfs = $this->getAllPdfs($teacher);
        
        // Verificar si el PDF està actualitzat segons la data límit
        $hasUpdatedPdf = $this->hasUpdatedPdfAfterDeadline($teacher);
        
        return view('teacher.profile.edit', compact('teacher', 'latestPdf', 'allPdfs', 'hasUpdatedPdf'));
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
            $filename = 'consent_dades_teacher_' . now()->format('Ymd_Hi') . '.pdf';
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
                'token' => null, // No s'utilitza token en aquest flux
                'ipAddress' => $request->ip(),
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

    /**
     * Get all PDF files for a teacher (max 3)
     */
    private function getAllPdfs(CampusTeacher $teacher): array
    {
        $directory = storage_path('app/consents/teachers/' . $teacher->id);
        
        if (!is_dir($directory)) {
            return [];
        }

        $files = glob($directory . '/consent_dades_teacher_*.pdf');
        
        if (empty($files)) {
            return [];
        }

        // Ordenar per data de modificació (el més recent primer)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        // Limitar a màxim 3 fitxers
        $files = array_slice($files, 0, 3);

        $pdfs = [];
        foreach ($files as $file) {
            $filename = basename($file);
            $pdfs[] = [
                'filename' => $filename,
                'download_url' => route('teacher.profile.download', ['filename' => $filename]),
                'file_path' => 'storage/app/consents/teachers/' . $teacher->id . '/' . $filename,
                'created_at' => date('d/m/Y H:i', filemtime($file)),
                'size' => $this->formatFileSize(filesize($file))
            ];
        }

        return $pdfs;
    }

    /**
     * Format file size in human readable format
     */
    private function formatFileSize($bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } else {
            return '0 bytes';
        }
    }

    /**
     * Check if teacher has updated PDF after deadline
     */
    private function hasUpdatedPdfAfterDeadline(CampusTeacher $teacher): bool
    {
        $deadline = Setting::get('pdf_update_deadline', '2026-03-15');
        $deadlineDate = \Carbon\Carbon::parse($deadline);
        
        // Obtenir l'últim PDF del professor
        $latestPdf = $this->getLatestPdf($teacher);
        
        if (!$latestPdf) {
            return false; // No té PDF
        }
        
        // Obtenir data de modificació del fitxer
        $pdfPath = storage_path('app/consents/teachers/' . $teacher->id . '/' . $latestPdf['filename']);
        $pdfModifiedTime = filemtime($pdfPath);
        $pdfDate = \Carbon\Carbon::createFromTimestamp($pdfModifiedTime);
        
        return $pdfDate->greaterThan($deadlineDate);
    }

    /**
     * Get the latest PDF file for a teacher
     */
    private function getLatestPdf(CampusTeacher $teacher): ?array
    {
        $directory = storage_path('app/consents/teachers/' . $teacher->id);
        
        if (!is_dir($directory)) {
            return null;
        }

        $files = glob($directory . '/consent_dades_teacher_*.pdf');
        
        if (empty($files)) {
            return null;
        }

        // Ordenar per data de modificació (el més recent primer)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $latestFile = $files[0];
        $filename = basename($latestFile);
        
        return [
            'filename' => $filename,
            'download_url' => route('teacher.profile.download', ['filename' => $filename]),
            'file_path' => 'storage/app/consents/teachers/' . $teacher->id . '/' . $filename,
            'created_at' => date('d/m/Y H:i', filemtime($latestFile))
        ];
    }

}
