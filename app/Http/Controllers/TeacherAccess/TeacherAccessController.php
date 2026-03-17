<?php

namespace App\Http\Controllers\TeacherAccess;

use App\Http\Controllers\Controller;
use App\Models\TeacherAccessToken;
use App\Models\CampusTeacher;
use App\Models\CampusTeacherPayment;
use App\Models\ConsentHistory;
use App\Models\CampusSeason;
use App\Models\CampusCourse;
use App\Models\CampusCourseTeacher;
use App\Models\TreasuryData;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\SimpleConsentPDFService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TeacherAccessController extends Controller
{

    /**
     * Función UNIFICADA para actualizar datos personales y de pago al profesor
     */

    public function updatePersonalData(Request $request, string $token)
        {
            $accessToken = TeacherAccessToken::where('token', $token)
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->firstOrFail();

            $user = User::findOrFail($accessToken->teacher_id);
            
            // Verificar si es el proceso final (solo autorizaciones)
            if ($request->has('end_autoritzacio_dades') && $request->has('end_declaracio_fiscal')) {
                return $this->processFinalConsent($request, $accessToken, $user);
            }

            $needsPayment = $request->input('needs_payment');
            
            // DEBUG: Ver qué llega en el request
            \Log::info('DEBUG - Request data:', [
                'needs_payment' => $needsPayment,
                'all_request' => $request->all(),
                'payment_option' => $request->input('payment_option'),
                'own_fee' => $request->input('own_fee'),
                'ceded_fee' => $request->input('ceded_fee'),
                'waived_fee' => $request->input('waived_fee')
            ]);

            /*
            |--------------------------------------------------------------------------
            | VALIDACIÓN BASE
            |--------------------------------------------------------------------------
            */
            $rules = [
                'first_name'  => 'required|string|max:255',
                'last_name'   => 'required|string|max:255',
                'email'       => 'required|email|max:255|unique:users,email,' . $user->id,
                'phone'       => 'required|string|max:20',
                'dni'         => 'required|string|max:20',
                'address'     => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:10',
                'city'        => 'nullable|string|max:255',
                'needs_payment' => 'nullable|string',
            ];

            /*
            |--------------------------------------------------------------------------
            | VALIDACIÓN CONDICIONAL
            |--------------------------------------------------------------------------
            */
            if ($needsPayment === 'own_fee') {

                $rules = array_merge($rules, [
                    'fiscal_id'        => 'nullable|string|max:20',
                    'iban' => 'nullable|string|max:34|regex:/^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$/',
                    'bank_titular'     => 'nullable|string|max:255',
                    'fiscal_situation' => 'nullable|string|max:255',
                ]);

            } elseif ($needsPayment === 'ceded_fee') {

                $rules = array_merge($rules, [
                    'beneficiary_first_name' => 'required|string|max:255',
                    'beneficiary_email'      => 'required|email|max:255',
                    'beneficiary_phone'      => 'required|string|max:20',

                    'beneficiary_fiscal_id'        => 'nullable|string|max:20',
                    'beneficiary_address'          => 'nullable|string|max:255',
                    'beneficiary_postal_code'      => 'nullable|string|max:10',
                    'beneficiary_city'             => 'nullable|string|max:255',
                    'beneficiary_iban' => 'nullable|string|max:34|regex:/^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$/',
                    'beneficiary_bank_titular'     => 'nullable|string|max:255',
                    'beneficiary_fiscal_situation' => 'nullable|string|max:255',
                ]);
            }
            
            // Si es waived_fee, verificar que no s'enviïn dades bancaries
            if ($needsPayment === 'waived_fee') {
                // Netejar camps bancaris si s'han enviat per error
                $request->merge([
                    'fiscal_id' => null,
                    'iban' => null,
                    'bank_titular' => null,
                    'invoice' => null,
                    'fiscal_situation' => null,
                    'beneficiary_iban' => null,
                    'beneficiary_bank_titular' => null,
                    'beneficiary_invoice' => null,
                ]);
                
                \Log::info('WAIVED_FEE: Camps bancaris netejats per evitar guardar dades innecessàries');
            }

            $validated = $request->validate($rules);

            /*
            |--------------------------------------------------------------------------
            | NORMALIZAR CHECKBOXES
            |--------------------------------------------------------------------------
            */
            $invoice = $request->input('invoice') == '1';
            $beneficiaryInvoice = $request->input('beneficiary_invoice') == '1';
            $endAutoritzacio = $request->has('end_autoritzacio_dades');
            $endDeclaracio = $request->has('end_end_declaracio_fiscal');

            /*
            |--------------------------------------------------------------------------
            | ACTUALIZAR USER
            |--------------------------------------------------------------------------
            */
            $user->update([
                'name'  => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | ACTUALIZAR TEACHER
            |--------------------------------------------------------------------------
            */
            $teacherData = [
                'first_name'  => $validated['first_name'],
                'last_name'   => $validated['last_name'],
                'email'       => $validated['email'],
                'phone'       => $validated['phone'],
                'dni'         => $validated['dni'],
                'address'     => $validated['address'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'city'        => $validated['city'] ?? null,
                'needs_payment' => $needsPayment,
                'observacions' => $request->input('observacions'),
            ];
            
            // Només guardar dades bancaries si no és waived_fee
            if ($needsPayment !== 'waived_fee') {
                $teacherData['fiscal_id'] = $validated['fiscal_id'] ?? $validated['dni'];
                $teacherData['iban'] = $validated['iban'] ?? null;
                $teacherData['bank_titular'] = $validated['bank_titular'] ?? null;
                $teacherData['fiscal_situation'] = $validated['fiscal_situation'] ?? null;
                $teacherData['invoice'] = $invoice;
            } else {
                // Per waived_fee, netejar dades bancaries
                $teacherData['fiscal_id'] = null;
                $teacherData['iban'] = null;
                $teacherData['bank_titular'] = null;
                $teacherData['fiscal_situation'] = null;
                $teacherData['invoice'] = false;
                
                \Log::info('WAIVED_FEE: Dades bancaries netejades del model CampusTeacher');
            }

            $teacher = CampusTeacher::updateOrCreate(
                ['user_id' => $user->id],
                $teacherData
            );

            /*
            |--------------------------------------------------------------------------
            | GUARDAR PAYMENT
            |--------------------------------------------------------------------------
            */
            $seasonId = $request->input('season_id');
            $courseId = $request->input('course_id');

            if ($teacher && $seasonId && $courseId) {

                if ($needsPayment === 'ceded_fee') {

                    CampusTeacherPayment::updateOrCreate(
                        [
                            'teacher_id' => $teacher->id,
                            'course_id'  => $courseId,
                            'season_id'  => $seasonId
                        ],
                        [
                            'payment_option' => $needsPayment,
                            'first_name'     => $validated['beneficiary_first_name'],
                            'fiscal_id'      => $validated['beneficiary_fiscal_id'] ?? null,
                            'address'        => $validated['beneficiary_address'] ?? null,
                            'postal_code'    => $validated['beneficiary_postal_code'] ?? null,
                            'city'           => $validated['beneficiary_city'] ?? null,
                            'iban'           => $validated['beneficiary_iban'] ?? null,
                            'bank_titular'   => $validated['beneficiary_bank_titular'] ?? null,
                            'fiscal_situation' => $validated['beneficiary_fiscal_situation'] ?? null,
                            'invoice'        => $beneficiaryInvoice,
                            'observacions'   => $request->input('observacions2'),
                            'completed_at'   => now(),
                        ]
                    );

                } else {

                    CampusTeacherPayment::updateOrCreate(
                        [
                            'teacher_id' => $teacher->id,
                            'course_id'  => $courseId,
                            'season_id'  => $seasonId
                        ],
                        [
                            'payment_option' => $needsPayment,
                            'first_name'     => $teacher->first_name,
                            'last_name'      => $teacher->last_name,
                            'fiscal_id'      => $teacher->fiscal_id,
                            'address'        => $teacher->address,
                            'postal_code'    => $teacher->postal_code,
                            'city'           => $teacher->city,
                            'iban'           => $teacher->iban,
                            'bank_titular'   => $teacher->bank_titular,
                            'fiscal_situation' => $teacher->fiscal_situation,
                            'invoice'        => $teacher->invoice,
                            'observacions'   => $teacher->observacions,
                            'completed_at'   => now(),
                        ]
                    );
                }
            }

            return back()->with('success', 'Dades actualitzades correctament');
        }

        
    public function show(string $token, string $purpose, string $courseCode = null)
    {
        \Log::info('=== TEACHER ACCESS START ===');
        \Log::info('Token recibido:', ['token' => $token]);
        
        // 1. Buscar season actual
        $season = CampusSeason::where('is_current', true)->first();       
       
        // 2. Buscar el token
        $accessToken = TeacherAccessToken::where('token', $token)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();
            
        if (!$accessToken) {
            \Log::error('Token no encontrado o expirado:', ['token' => $token]);
            abort(404, 'Enlace no válido o expirado');
        }
        
        \Log::info('Token encontrado:', [
            'id' => $accessToken->id,
            'teacher_id' => $accessToken->teacher_id,
            'expires_at' => $accessToken->expires_at,
            'used_at' => $accessToken->used_at
        ]);

        // 3. Buscar el usuario
        $user = User::find($accessToken->teacher_id);
        
        if (!$user) {
            \Log::error('Usuario no encontrado:', ['teacher_id' => $accessToken->teacher_id]);
            abort(404, 'Usuario no encontrado');
        }
        
        \Log::info('Usuario encontrado:', [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone
        ]);

        // 4. Buscar el profesor relacionado
        $teacher = CampusTeacher::where('user_id', $user->id)->first();
        
        // Si no existe, creamos un objeto vacío
        if (!$teacher) {
            \Log::warning('CampusTeacher no encontrado, se usará objeto vacío');
            $teacher = new CampusTeacher(['user_id' => $user->id]);
        } else {
            \Log::info('CampusTeacher encontrado:', [
                'teacher_id' => $teacher->id,
                'first_name' => $teacher->first_name,
                'last_name' => $teacher->last_name,
                'phone' => $teacher->phone
            ]);
        }
       
        // dd('linea 81 no hay courseCode'. $courseCode.' No se puede ejecutar la linea 83 ');   
        // 5. Buscar asignación de curso
       
        $course = CampusCourse::where('code', $courseCode)->firstOrFail();
        
        $courseasignat = CampusCourseTeacher::where('teacher_id', $teacher->id)
            ->whereHas('course', function($query) use ($courseCode) {
                $query->where('code', $courseCode);
            })
            ->with('course')
            ->first();
     
        
        /* if ($courseasignat && $courseasignat->course_id) {
            $course = CampusCourse::find($courseasignat->course_id);
        } */

        // 6. Buscar datos de pago del profesor
        $payment = CampusTeacherPayment::where('teacher_id', $teacher->id)
            ->where('course_id', $course->id)
            ->where('season_id', $season->id ?? null)
            ->first();

        \Log::info('=== TEACHER ACCESS END ===');

        return view('teacher-access.form-payments-acordeo', [
            'token' => $accessToken,
            'purpose' => $purpose,
            'user' => $user,
            'season' => $season,
            'teacher' => $teacher,
            'course' => $course,
            'courseasignat' => $courseasignat,
            'payment' => $payment,

        ])->with('success', 'Dades guardades correctament');
    }

    public function store(Request $request, string $token)
    {
        \Log::info('=== TEACHER ACCESS STORE ===');
        DB::beginTransaction();
        \Log::info('Token recibido:', ['token' => $token]);
       //   dd($request->all());    
       
        try {
            // Validar token
            \Log::info('=== INICIO VALIDACIÓN TOKEN ===');
            $accessToken = TeacherAccessToken::where('token', $token)
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->firstOrFail();
            \Log::info('Token validado correctamente');
            
            $user = User::findOrFail($accessToken->teacher_id);
            \Log::info('User encontrado:', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]);
           

            // Determinar qué formulario se está enviando
            \Log::info('=== DETERMINANDO FORMULARIO ===');
            \Log::info('Contenido request:', ['has_consent_rgpd' => $request->has('consent_rgpd'), 'has_payment_option' => $request->has('payment_option')]);
            
            if ($request->has('consent_rgpd')) {
                \Log::info('=== PROCESANDO FORMULARIO CONSENTIMIENTO ===');
                // Formulario 1: Datos básicos + RGPD
                // return $this->handleBasicDataForm($request, $user, $accessToken,);
            } elseif ($request->has('payment_option')) {
                \Log::info('=== PROCESANDO FORMULARIO PAGOS ===');
                // Formulario 2: Datos de pago
              //   return $this->handlePaymentDataForm($request, $user, $accessToken);
            } else {
                \Log::error('=== FORMULARIO NO RECONOCIDO ===');
                throw new \Exception('Formulario no válido');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('=== ERROR EN TEACHER ACCESS STORE ===');
            Log::error('Error al guardar datos del profesor: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            Log::error('Línea del error: ' . $e->getLine());
            Log::error('Archivo del error: ' . $e->getFile());
            
            // Generar referencia única para este error
            $errorReference = 'ERR-' . date('YmdHis') . '-' . strtoupper(substr(md5($e->getMessage()), 0, 6));
            Log::error("Referencia de error: {$errorReference}");
            
            return back()
                ->withInput()
                ->withErrors([
                    'error' => "Error al guardar los datos. Por favor, contacta con soporte técnico con la referencia: {$errorReference}"
                ]);
        }
    }
       
    /**
     * Validar formato IBAN
     */
    private function validateIBAN($iban)
    {
        // Limpiar espacios
        $iban = strtoupper(str_replace(' ', '', $iban));
        
        // Validar con regex para formato español
        if (!preg_match('/^ES\d{22}$/', $iban)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Obtener etiqueta legible para la opción de pago
     */
    private function getPaymentOptionLabel(string $option): string
    {
        $labels = [
            'own_fee' => '✅ Accepto el cobrament',
            'ceded_fee' => '📄 Cedeixo la titularitat',
            'waived_fee' => '⚠️ Renuncio voluntàriament al cobrament',
        ];
        
        return $labels[$option] ?? $option;
    }
    
    /**
     * Generar PDF final de consentimiento con autorizaciones
     */
    private function generateFinalConsentPdf($teacher, $user, $season, $course, $payment, $request)
    {
        \Log::info('=== INICIO GENERACIÓN PDF FINAL CONSENTIMIENTO ===');
        
        $acceptedAt = now();
        $seasonSlug = $season->slug ?? $season->id;
        $courseId = $course->code ?? 'unknown';
        
        // Definir ruta para el PDF final
        $finalConsentPath = "consents/teachers/{$teacher->id}/final_consent_{$seasonSlug}_{$courseId}.pdf";
        
        // Calcular checksum para el documento final
        $finalChecksum = hash('sha256', implode('|', [
            $teacher->id,
            $seasonSlug,
            $courseId,
            $payment->payment_option,
            $acceptedAt->timestamp,
            $payment->fiscal_id,
            $request->ip(),
            'final_consent_with_authorizations'
        ]));
        
        // Preparar datos para la vista del PDF
        $finalConsentData = [
            'teacher' => $teacher,
            'user' => $user,
            'season' => $season,
            'course' => $course,
            'payment' => $payment,
            'paymentOption' => $payment->payment_option,
            'paymentOptionLabel' => $this->getPaymentOptionLabel($payment->payment_option),
            'fiscalId' => $payment->fiscal_id ?? 'N/A',
            'address' => $payment->address ?? 'N/A',
            'postalCode' => $payment->postal_code ?? 'N/A',
            'city' => $payment->city ?? 'N/A',
            'iban' => $payment->iban ?? 'N/A',
            'bank_titular' => $payment->bank_titular ?? 'N/A',
            'fiscalSituation' => $payment->fiscal_situation ?? 'N/A',
            'acceptedAt' => $acceptedAt,
            'checksum' => $finalChecksum,
            'autoritzacioDades' => $payment->metadata['end_autoritzacio_dades'] ?? false,
            'declaracioFiscal' => $payment->metadata['end_declaracio_fiscal'] ?? false,
            'seasonSlug' => $seasonSlug,
            'isFinalConsent' => true,
        ];
        
        \Log::info('Final consent data for PDF:', ['final_consent_data' => $finalConsentData]);

        // Generar PDF
        $pdf = Pdf::loadView('treasury.consents.teacher-payment', $finalConsentData);
        
        // Guardar PDF a storage
        Storage::disk('local')->put($finalConsentPath, $pdf->output());
       
        \Log::info('PDF FINAL CONSENTIMIENTO generado:', ['path' => $finalConsentPath]);   

        // Actualizar consent_histories con la ruta del PDF final
        ConsentHistory::updateOrCreate(
            [
                'teacher_id' => $user->id, // Usar user_id en lugar de teacher->id
                'season' => $seasonSlug,
            ],
            [
                'accepted_at' => $acceptedAt,
                'checksum' => $finalChecksum,
                'document_path' => $finalConsentPath,
                'final_consent_document_path' => $finalConsentPath,
                'final_consent_accepted_at' => $acceptedAt,
                'final_consent_checksum' => $finalChecksum,
            ]
        );
        
        \Log::info('ConsentHistory actualizado con PDF final:', [
            'teacher_id' => $user->id, // Log correcto
            'season' => $seasonSlug, 
            'path' => $finalConsentPath
        ]);     
        
        // Actualizar el registro de pago con la ruta del PDF final en metadata
        $existingMetadata = $payment->metadata ?? [];
        $payment->update([
            'metadata' => array_merge($existingMetadata, [
                'final_consent_document_path' => $finalConsentPath,
                'final_consent_checksum' => $finalChecksum,
                'final_consent_generated_at' => $acceptedAt->toDateTimeString(),
                'ip_address' => $request->ip(),
            ]),
        ]);
        
        \Log::info('Payment record actualizado con PDF final:', [
            'teacher_id' => $teacher->id,
            'season' => $seasonSlug,
            'path' => $finalConsentPath,
            'checksum' => $finalChecksum
        ]);
        
        \Log::info('=== FIN GENERACIÓN PDF FINAL CONSENTIMIENTO ===');
    }
    
    //Función para mostrar success
    public function success(Request $request, string $token)
    {
        // Buscar token
        $accessToken = TeacherAccessToken::where('token', $token)->firstOrFail();
        $user = User::findOrFail($accessToken->teacher_id);
        $teacher = CampusTeacher::where('user_id', $user->id)->first();
        
        // Obtener último consentimiento y datos relacionados
        $latestConsent = null;
        $latestPayment = null;
        $courseInfo = null;
        
        if ($teacher) {
            $latestConsent = ConsentHistory::where('teacher_id', $user->id)
                ->latest('accepted_at')
                ->first();
                
            // Obtener el pago más reciente del profesor
            $latestPayment = CampusTeacherPayment::where('teacher_id', $teacher->id)
                ->latest('updated_at')
                ->first();
                
            // Si hay pago, obtener datos del curso
            if ($latestPayment) {
                $courseInfo = CampusCourse::find($latestPayment->course_id);
            }
        }
        
        // Determinar mensaje según parámetro
        $message = $request->input('message', 'default');
        
        $messages = [
            'basic_data_saved' => 'Les dades personals s\'han registrat correctament.',
            'payment_saved' => 'Les dades de dades bancaries s\'han registrat correctament.',
            'waived_payment_saved' => 'Has renunciat voluntàriament al cobrament. Les dades s\'han registrat correctament.',
            'final_consent_saved' => 'El consentiment final s\'ha registrat correctament. Procés completat!',
            'default' => 'Les dades s\'han registrat correctament.',
        ];
        
        return view('teacher-access.success', [
            'teacher' => $teacher,
            'user' => $user,
            'latestConsent' => $latestConsent,
            'latestPayment' => $latestPayment,
            'courseInfo' => $courseInfo,
            'token' => $accessToken,
            'message' => $messages[$message] ?? $messages['default'],
        ]);
    }

    /**
     * Procesar el consentimiento final: generar PDF y finalizar
     */
    private function processFinalConsent($request, $accessToken, $user)
    {
        \Log::info('=== PROCESANDO CONSENTIMIENTO FINAL ===');
        
        try {
            // 1. Obtener datos existentes
            $teacher = CampusTeacher::where('user_id', $user->id)->first();
            if (!$teacher) {
                throw new \Exception('No se encontró el profesor relacionado');
            }

            // 2. Obtener datos de pago usando course_id y season_id del request
            \Log::info('Buscando datos de pago con:', [
                'teacher_id' => $teacher->id,
                'course_id' => $request->input('course_id'),
                'season_id' => $request->input('season_id'),
                'request_all' => $request->all()
            ]);
            
            $payment = CampusTeacherPayment::where('teacher_id', $teacher->id)
                ->where('course_id', $request->input('course_id'))
                ->where('season_id', $request->input('season_id'))
                ->first();

            \Log::info('Resultado búsqueda payment:', [
                'payment_found' => $payment ? 'YES' : 'NO',
                'payment_id' => $payment?->id,
                'payment_option' => $payment?->payment_option
            ]);

            if (!$payment) {
                // En lugar de fallar, generar PDF con datos básicos del profesor
                \Log::warning('No se encontraron datos de pago específicos, usando datos básicos del profesor', [
                    'teacher_id' => $teacher->id,
                    'course_id' => $request->input('course_id'),
                    'season_id' => $request->input('season_id')
                ]);
                
                // Crear un objeto payment básico para que el PDF funcione
                $payment = new \stdClass();
                $payment->payment_option = 'own_fee'; // Asumimos que cobra
                $payment->fiscal_id = $teacher->dni;
                $payment->iban = $teacher->masked_iban;
                $payment->bank_titular = $teacher->first_name . ' ' . $teacher->last_name;
                $payment->metadata = [];
            }

            // 3. Obtener temporada y curso
            $season = CampusSeason::find($request->input('season_id'));
            $course = CampusCourse::find($request->input('course_id'));

            if (!$season || !$course) {
                throw new \Exception('No se encontró la temporada o el curso');
            }

            \Log::info('Datos encontrados:', [
                'teacher_id' => $teacher->id,
                'payment_id' => $payment->id,
                'season_id' => $season->id,
                'course_id' => $course->id
            ]);

            // 4. Generar PDF usando TCPDF
            $pdfService = new SimpleConsentPDFService();
            $finalConsentPath = $pdfService->generateSimplePDF(
                $teacher,
                $season,
                $course,
                $payment,
                $payment->metadata['end_autoritzacio_dades'] ?? false,
                $payment->metadata['end_declaracio_fiscal'] ?? false
            );

            // 5. Actualizar metadatos del payment con las autorizaciones
            $existingMetadata = $payment->metadata ?? [];
            $payment->update([
                'metadata' => array_merge($existingMetadata, [
                    'end_autoritzacio_dades' => true,
                    'end_end_declaracio_fiscal' => true,
                    'final_consent_accepted_at' => now()->toDateTimeString(),
                    'ip_address' => $request->ip(),
                ])
            ]);

            // 7. Actualizar consent_histories con la ruta del PDF final
        ConsentHistory::updateOrCreate(
            [
                'teacher_id' => $user->id, // Usar user_id en lugar de teacher->id
                'season' => $season->slug ?? $season->id,
            ],
            [
                'accepted_at' => now(),
                'checksum' => hash('sha256', implode('|', [
                    $teacher->id,
                    $season->slug ?? $season->id,
                    $course->code ?? 'unknown',
                    $payment->payment_option,
                    now()->timestamp,
                    $payment->fiscal_id,
                    $request->ip(),
                    'final_consent_with_authorizations'
                ])),
                'document_path' => $finalConsentPath,
                'final_consent_document_path' => $finalConsentPath,
                'final_consent_accepted_at' => now(),
                'final_consent_checksum' => hash('sha256', implode('|', [
                    $teacher->id,
                    $season->slug ?? $season->id,
                    $course->code ?? 'unknown',
                    $payment->payment_option,
                    now()->timestamp,
                    $payment->fiscal_id,
                    $request->ip(),
                    'final_consent_with_authorizations'
                ])),
            ]
        );
        
        \Log::info('ConsentHistory actualizado con PDF final:', [
            'teacher_id' => $user->id, // Log correcto
            'season' => $season->slug ?? $season->id, 
            'path' => $finalConsentPath
        ]);

        // 8. Marcar token como usado
        $accessToken->update(['used_at' => now()]);

        \Log::info('=== CONSENTIMIENTO FINAL PROCESADO CORRECTAMENTE ===');

        // 9. Redirigir a página de éxito
        return redirect()->route('teacher.access.success', $accessToken->token)
            ->with('message', 'final_consent_saved');

        } catch (\Exception $e) {
            \Log::error('Error en processFinalConsent: ' . $e->getMessage());
            // Generar referencia única para este error
            $errorReference = 'ERR-' . date('YmdHis') . '-' . strtoupper(substr(md5($e->getMessage()), 0, 6));
            Log::error("Referencia de error: {$errorReference}");
            
            return back()
                ->withInput()
                ->withErrors([
                    'error' => "Error al guardar los datos. Por favor, contacta con soporte técnico con la referencia: {$errorReference}"
                ]);
        }
    }
}