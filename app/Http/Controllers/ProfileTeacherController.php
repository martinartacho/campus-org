<?php

namespace App\Http\Controllers;

use App\Models\CampusTeacher;
use App\Models\Setting;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        
        // Verificar si les dades bancàries estan congelades
        $isBankingDataFrozen = $this->isBankingDataFrozen();
        $canEditBankingData = $this->canEditBankingData();
        
        return view('teacher.profile.edit', compact('teacher', 'latestPdf', 'allPdfs', 'hasUpdatedPdf', 'isBankingDataFrozen', 'canEditBankingData'));
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

            // Redirigir a la vista de PDFs on es poden descarregar tots
            return redirect()->route('teacher.profile.pdfs')
                ->with('success', 'PDF generat correctament! Pots descarregar-lo des d\'aquí.');

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
        try {
            // 1. Notificació al teacher
            $this->sendTeacherNotification($teacher, $filename);
            
            // 2. Notificació a treasury
            $this->sendTreasuryNotification($teacher, $filename);
            
            Log::info('Notifications sent successfully', [
                'teacher_id' => $teacher->id,
                'filename' => $filename
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error sending notifications', [
                'teacher_id' => $teacher->id,
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Enviar notificació al teacher
     */
    private function sendTeacherNotification($teacher, $filename): void
    {
        // Crear notificación específica para el teacher
        $notification = Notification::create([
            'title' => 'PDF Generat Correctament',
            'content' => "El teu PDF ha estat generat correctament. Pots <a href='" . route('teacher.profile.pdfs') . "' class='text-blue-600 hover:text-blue-800 underline'>veure tots els teus PDFs aquí</a> o descarregar aquest fitxer: <a href='" . route('teacher.profile.download', $filename) . "' class='text-blue-600 hover:text-blue-800 underline'>" . $filename . "</a>.",
            'type' => 'pdf_generated',
            'sender_id' => 1, // Sistema
            'recipient_type' => 'specific',
            'recipient_ids' => [$teacher->user_id],
            'email_sent' => false,
            'web_sent' => false,
            'push_sent' => false,
            'is_published' => true,
            'published_at' => now(),
        ]);
        
        // Asignar destinatarios y enviar
        $notification->recipients()->sync([$teacher->user_id]);
        $this->sendNotificationChannels($notification, ['email', 'web', 'push']);
    }

    /**
     * Enviar notificació a treasury
     */
    private function sendTreasuryNotification($teacher, $filename): void
    {
        // Obtener usuarios con rol treasury
        $treasuryUsers = User::role('treasury')->pluck('id')->toArray();
        
        if (empty($treasuryUsers)) {
            Log::warning('No treasury users found for notification');
            return;
        }
        
        $notification = Notification::create([
            'title' => 'Nou PDF Generat - ' . $teacher->first_name . ' ' . $teacher->last_name,
            'content' => "El professor o la professora {$teacher->first_name} {$teacher->last_name} ha generat un nou PDF. Fitxer: {$filename}. Disponible per revisió al sistema.",
            'type' => 'pdf_generated',
            'sender_id' => 1, // Sistema
            'recipient_type' => 'role',
            'recipient_role' => 'treasury',
            'email_sent' => false,
            'web_sent' => false,
            'push_sent' => false,
            'is_published' => true,
            'published_at' => now(),
        ]);
        
        $notification->recipients()->sync($treasuryUsers);
        $this->sendNotificationChannels($notification, ['email', 'web', 'push']);
    }

    /**
     * Enviar notificació a través de múltiples canales
     */
    private function sendNotificationChannels(Notification $notification, array $channels): void
    {
        foreach ($channels as $channel) {
            try {
                switch ($channel) {
                    case 'email':
                        $this->sendEmailChannel($notification);
                        break;
                    case 'web':
                        $this->sendWebChannel($notification);
                        break;
                    case 'push':
                        $this->sendPushChannel($notification);
                        break;
                }
            } catch (\Exception $e) {
                Log::error("Error sending notification via {$channel}", [
                    'notification_id' => $notification->id,
                    'channel' => $channel,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Actualizar el estado general de la notificación
        $notification->update([
            'email_sent' => in_array('email', $channels),
            'web_sent' => in_array('web', $channels),
            'push_sent' => in_array('push', $channels),
        ]);
    }

    /**
     * Enviar notificació per email
     */
    private function sendEmailChannel(Notification $notification): void
    {
        $recipients = $notification->recipients;
        
        foreach ($recipients as $user) {
            try {
                // Verificar que el email sea válido
                if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                // Usar la clase Mailable existente
                $mail = new \App\Mail\NotificationMail($notification, $user);
                
                \Mail::to($user->email)->send($mail);
                
                // Marcar como enviado en la tabla pivot
                $notification->recipients()->updateExistingPivot($user->id, [
                    'email_sent' => true,
                    'email_sent_at' => now(),
                ]);
                
                Log::info("Email sent successfully", [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'notification_id' => $notification->id
                ]);
                
            } catch (\Exception $e) {
                Log::error("Error sending email", [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'notification_id' => $notification->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Enviar notificació web
     */
    private function sendWebChannel(Notification $notification): void
    {
        // Las notificaciones web ya están guardadas en la base de datos
        // Solo marcamos como enviadas en la tabla pivot
        $notification->recipients()->updateExistingPivot($notification->recipients->pluck('id'), [
            'web_sent' => true,
            'web_sent_at' => now(),
        ]);
        
        Log::info("Web notification sent", [
            'notification_id' => $notification->id,
            'recipients_count' => $notification->recipients()->count()
        ]);
    }

    /**
     * Enviar notificació push
     */
    private function sendPushChannel(Notification $notification): void
    {
        $recipients = $notification->recipients()->whereNotNull('fcm_token')->get();
        
        foreach ($recipients as $user) {
            try {
                if ($user->fcm_token) {
                    $fcmService = app(\App\Services\FCMService::class);
                    
                    $fcmService->sendNotification(
                        $user->fcm_token,
                        $notification->title,
                        $notification->content,
                        [
                            'type' => $notification->type,
                            'notification_id' => $notification->id,
                            'url' => route('notifications.show', $notification->id)
                        ]
                    );
                    
                    // Marcar como enviado en la tabla pivot
                    $notification->recipients()->updateExistingPivot($user->id, [
                        'push_sent' => true,
                        'push_sent_at' => now(),
                    ]);
                    
                    Log::info("Push notification sent", [
                        'user_id' => $user->id,
                        'notification_id' => $notification->id
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Error sending push notification", [
                    'user_id' => $user->id,
                    'notification_id' => $notification->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Show all teachers with their PDFs (for admin)
     */
    public function teachersPdfsPage(): View
    {
        $teachers = CampusTeacher::with(['user', 'courses'])->get();
        
        // Preparar dades per a les estadístiques
        $teachersWithPdfs = $teachers->filter(function($teacher) {
            return $teacher->hasPdfs();
        });
        
        $teachersWithoutPdfs = $teachers->filter(function($teacher) {
            return !$teacher->hasPdfs();
        });
        
        $teachersWithoutIban = $teachers->filter(function($teacher) {
            return empty($teacher->iban);
        });

        return view('campus.teachers.pdfs', compact(
            'teachers', 
            'teachersWithPdfs', 
            'teachersWithoutPdfs', 
            'teachersWithoutIban'
        ));
    }

    /**
     * Delete PDF for admin
     */
    public function deletePdfForAdmin(CampusTeacher $teacher, string $filename): RedirectResponse
    {
        // Verificar que l'usuari estigui autenticat
        if (!auth()->check()) {
            return redirect()->route('campus.teachers.pdfs')
                ->with('error', 'No tens permisos per eliminar aquest PDF.');
        }

        // Construir ruta completa del fitxer
        $filepath = storage_path('app/consents/teachers/' . $teacher->id . '/' . $filename);

        // Verificar que el fitxer existeixi
        if (!file_exists($filepath)) {
            return redirect()->route('campus.teachers.pdfs')
                ->with('error', 'Fitxer no trobat.');
        }

        // Verificar que sigui un PDF
        if (!str_ends_with($filename, '.pdf')) {
            return redirect()->route('campus.teachers.pdfs')
                ->with('error', 'Tipus de fitxer no permès.');
        }

        try {
            // Eliminar el fitxer
            if (unlink($filepath)) {
                // Registrar l'acció
                \Log::info('PDF eliminat per admin', [
                    'teacher_id' => $teacher->id,
                    'filename' => $filename,
                    'user_id' => auth()->id()
                ]);

                return redirect()->route('campus.teachers.pdfs')
                    ->with('success', 'PDF eliminat correctament.');
            } else {
                return redirect()->route('campus.teachers.pdfs')
                    ->with('error', 'No s\'ha pogut eliminar el PDF.');
            }
        } catch (\Exception $e) {
            \Log::error('Error eliminant PDF', [
                'teacher_id' => $teacher->id,
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('campus.teachers.pdfs')
                ->with('error', 'Error eliminant el PDF.');
        }
    }

    /**
     * Download PDF for admin
     */
    public function downloadPdfForAdmin(CampusTeacher $teacher, string $filename): BinaryFileResponse|RedirectResponse
    {
        // Verificar que l'usuari estigui autenticat
        if (!auth()->check()) {
            return redirect()->route('campus.teachers.pdfs')
                ->with('error', 'No tens permisos per descarregar aquest PDF.');
        }

        // Construir ruta completa del fitxer
        $filepath = storage_path('app/consents/teachers/' . $teacher->id . '/' . $filename);

        // Verificar que el fitxer existeixi
        if (!file_exists($filepath)) {
            return redirect()->route('campus.teachers.pdfs')
                ->with('error', 'Fitxer no trobat.');
        }

        // Verificar que sigui un PDF
        if (!str_ends_with($filename, '.pdf')) {
            return redirect()->route('campus.teachers.pdfs')
                ->with('error', 'Tipus de fitxer no permès.');
        }

        // Retornar el fitxer per descàrrega
        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Show PDF details for admin (without download)
     */
    public function showPdfForAdmin(CampusTeacher $teacher, string $filename): View
    {
        // Verificar que l'usuari estigui autenticat
        if (!auth()->check()) {
            return redirect()->route('campus.teachers.index')
                ->with('error', 'No tens permisos per veure aquest PDF.');
        }

        // Obtenir informació del PDF
        $pdfs = $teacher->getAllPdfs();
        $pdf = collect($pdfs)->firstWhere('filename', $filename);

        if (!$pdf) {
            return redirect()->route('campus.teachers.pdfs')
                ->with('error', 'PDF no trobat.');
        }

        return view('campus.teachers.pdf-show', compact('teacher', 'pdf'));
    }

    /**
     * Show PDF download page for admin (specific teacher)
     */
    public function pdfDownloadPageForAdmin(CampusTeacher $teacher): View|RedirectResponse
    {
        // Verificar permisos
        if (!auth()->user()->can('view', $teacher)) {
            return redirect()->route('campus.teachers.index')
                ->with('error', 'No tens permisos per veure els PDFs d\'aquest teacher.');
        }

        $allPdfs = $this->getAllPdfs($teacher);

        return view('admin.teachers.pdf-download', compact('teacher', 'allPdfs'));
    }

    /**
     * Show PDF download page
     */
    public function pdfDownloadPage(): View|RedirectResponse
    {
        $user = Auth::user();
        $teacher = $user->teacherProfile;

        if (!$teacher) {
            return redirect()->route('teacher.profile')
                ->with('error', 'Error carregant el perfil.');
        }

        $allPdfs = $this->getAllPdfs($teacher);

        return view('teacher.profile.pdf-download', compact('teacher', 'allPdfs'));
    }

    /**
     * Download PDF from server
     */
    public function downloadPDF($filename): \Symfony\Component\HttpFoundation\BinaryFileResponse|RedirectResponse
    {
        $user = Auth::user();
        $teacher = $user->teacherProfile;

        if (!$teacher) {
            return redirect()->route('teacher.profile')
                ->with('error', 'Error generant el PDF.');
        }

        // Validar que el filename sigui segur
        $filename = basename($filename);
        if (!preg_match('/^consent_dades_teacher_\d{8}_\d{4}\.pdf$/', $filename)) {
            return redirect()->route('teacher.profile')
                ->with('error', 'Nom de fitxer no vàlid.');
        }

        $filepath = storage_path('app/consents/teachers/' . $teacher->id . '/' . $filename);

        if (!file_exists($filepath)) {
            return redirect()->route('teacher.profile')
                ->with('error', 'Fitxer no trobat.');
        }

        // Verificar que el fitxer pertanyi al teacher correcte
        $expectedPrefix = 'consent_dades_teacher_';
        if (!str_starts_with($filename, $expectedPrefix)) {
            return redirect()->route('teacher.profile')
                ->with('error', 'Accés no autoritzat.');
        }

        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
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
            $filesize = filesize($file);
            
            // Formatar la mida del fitxer
            if ($filesize >= 1048576) { // >= 1MB
                $sizeFormatted = number_format($filesize / 1048576, 2) . ' MB';
            } elseif ($filesize >= 1024) { // >= 1KB
                $sizeFormatted = number_format($filesize / 1024, 2) . ' KB';
            } else {
                $sizeFormatted = $filesize . ' bytes';
            }
            
            $pdfs[] = [
                'filename' => $filename,
                'filepath' => $file,
                'size' => $filesize,
                'size_formatted' => $sizeFormatted,
                'modified_date' => date('d/m/Y H:i', filemtime($file)),
                'modified_timestamp' => filemtime($file),
                'created_at' => date('d/m/Y H:i', filemtime($file)), // Per compatibilitat amb vista existent
                'download_url' => route('teacher.profile.download', $filename),
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
     * Check if banking data is frozen (payment period)
     */
    private function isBankingDataFrozen(): bool
    {
        $freezeStart = Setting::get('payment_freeze_start');
        $freezeEnd = Setting::get('payment_freeze_end');
        
        if (!$freezeStart || !$freezeEnd) {
            return false; // No hi ha període de bloqueig configurat
        }
        
        $now = \Carbon\Carbon::now();
        $startDate = \Carbon\Carbon::parse($freezeStart);
        $endDate = \Carbon\Carbon::parse($freezeEnd);
        
        return $now->between($startDate, $endDate);
    }

    /**
     * Check if user can edit banking data (bypass freeze for admin/treasury)
     */
    private function canEditBankingData(): bool
    {
        // Admin i Treasury sempre poden editar
        if (auth()->user()->hasRole(['admin', 'treasury'])) {
            return true;
        }
        
        // Altres usuaris: verificar si no està congelat
        return !$this->isBankingDataFrozen();
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
