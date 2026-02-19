<?php

namespace App\Http\Controllers\Treasury;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TreasuryData;
use App\Models\ConsentHistory;
use App\Models\CampusSeason;
use App\Models\CampusTeacherPayment;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TeachersRgpdExport;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherTreasuryController extends Controller
{


    public function index(Request $request)
    {
        $this->authorize('campus.teachers.view');

        // Obtenir totes les temporades per al selector
        $seasons = CampusSeason::orderBy('season_start', 'desc')->get();
        
        // Obtenir la temporada seleccionada o l'actual
        $selectedSeasonSlug = $request->input('season');
        
        if ($selectedSeasonSlug) {
            $selectedSeason = CampusSeason::where('slug', $selectedSeasonSlug)->first();
        } else {
            $selectedSeason = CampusSeason::where('is_current', true)->first();
            $selectedSeasonSlug = $selectedSeason->slug ?? null;
        }

        $teachers = collect();

        if ($selectedSeason) {
            // Obtenir usuaris amb rol teacher que tinguin cursos a la temporada actual
            $teachers = User::role('teacher')
                ->whereHas('teacherProfile.courses', function($query) use ($selectedSeason) {
                    $query->where('season_id', $selectedSeason->id);
                })
                ->with([
                    'teacherProfile.courses' => function($query) use ($selectedSeason) {
                        $query->where('season_id', $selectedSeason->id)
                            ->withPivot('role', 'hours_assigned');
                    },
                    'consents' => function($query) use ($selectedSeason) {
                        $query->where('season', $selectedSeason->slug);
                    },
                    'teacherProfile.payments' => function($query) use ($selectedSeason) {
                        $query->where('season_id', $selectedSeason->id);
                    }
                ])
                ->get()
                ->map(function($user) use ($selectedSeason) {
                    $teacherProfile = $user->teacherProfile;
                    $courses = $teacherProfile->courses ?? collect();
                    
                    // Agrupar dades bancariess per curs
                    $paymentsByCourse = $teacherProfile->payments->keyBy('course_id');
                    
                    // Processar cada curs
                    $coursesWithData = $courses->map(function($course) use ($paymentsByCourse, $user, $selectedSeason, $teacherProfile) {
                        $payment = $paymentsByCourse->get($course->id);
                        
                        // Verificar si existe el PDF de pago del profesor
                        $paymentPdfPath = "consents/teachers/{$teacherProfile->id}/payment_" . ($selectedSeason->slug ?? 'unknown') . "_{$course->code}.pdf";
                        $paymentPdfExists = Storage::disk('local')->exists($paymentPdfPath);
                        
                        // Verificar si hay un token activo para recordatorio de pago
                        $activeToken = \App\Models\TeacherAccessToken::where('teacher_id', $user->id)
                            ->where('metadata', 'LIKE', '%' . $course->code . '%')
                            ->first();
                        
                        return [
                            'course' => $course,
                            'pivot' => $course->pivot,
                            'payment' => $payment,
                            'has_payment_data' => $payment !== null,
                            'hours_assigned' => $course->pivot->hours_assigned ?? 0,
                            'role' => $course->pivot->role ?? 'teacher',
                            'course_code' => $course->code ?? 'Sense codi',
                            'course_title' => $course->title ?? 'Sense títol',
                            'payment_date' => $payment ? $payment->created_at : null,
                            'payment_formatted_date' => $payment ? $payment->created_at->format('d/m/Y') : null,
                            'payment_pdf_path' => $paymentPdfPath,
                            'payment_pdf_exists' => $paymentPdfExists,
                            'has_active_payment_token' => $activeToken !== null,
                            'payment_token_expires_at' => $activeToken ? $activeToken->expires_at->format('d/m/Y H:i') : null,
                        ];
                    });

                    // Consentiment RGPD
                    $rgpdConsent = $user->consents->first();
                    
                    return [
                        'user' => $user,
                        'teacher_profile' => $teacherProfile,
                        'courses' => $coursesWithData,
                        'rgpd_consent' => $rgpdConsent,
                        'has_rgpd_consent' => $rgpdConsent !== null,
                        'total_courses' => $coursesWithData->count(),
                        'invoice' => $teacherProfile->invoice,
                        'courses_with_payment' => $coursesWithData->where('has_payment_data', true)->count(),
                        'all_courses_have_payment' => $coursesWithData->count() > 0 && 
                                                    $coursesWithData->where('has_payment_data', true)->count() === $coursesWithData->count(),
                        'total_hours_assigned' => $coursesWithData->sum('hours_assigned'),
                        'average_hours_per_course' => $coursesWithData->count() > 0 ? round($coursesWithData->sum('hours_assigned') / $coursesWithData->count(), 2) : 0,
                        'max_hours_course' => $coursesWithData->max('hours_assigned'),
                        'min_hours_course' => $coursesWithData->min('hours_assigned'),
                        'courses_by_role' => $coursesWithData->groupBy('role')->map(fn($group) => $group->count()),
                    ];
                });
        }

        // Passem les variables amb els noms que espera la vista
        return view('treasury.teachers.index', [
            'teachersWithCourses' => $teachers,
            'season' => $selectedSeason,
            'seasons' => $seasons,
            'selectedSeasonSlug' => $selectedSeasonSlug,
            'selectedSeason' => $selectedSeason,
        ]);
    }

    public function rgpdIndex(Request $request)
    {
        $this->authorize('campus.teachers.view');

        // Obtenir totes les temporades per al selector
        $seasons = CampusSeason::orderBy('season_start', 'desc')->get();
        
        // Obtenir la temporada seleccionada o l'actual
        $selectedSeasonSlug = $request->input('season');
        
        if ($selectedSeasonSlug) {
            $selectedSeason = CampusSeason::where('slug', $selectedSeasonSlug)->first();
        } else {
            $selectedSeason = CampusSeason::where('is_current', true)->first();
            $selectedSeasonSlug = $selectedSeason->slug ?? null;
        }

        $teachers = collect();

        if ($selectedSeason) {
            // Obtenir usuaris amb rol teacher que tinguin cursos a la temporada actual
            $teachers = User::role('teacher')
                ->whereHas('teacherProfile.courses', function($query) use ($selectedSeason) {
                    $query->where('season_id', $selectedSeason->id);
                })
                ->with([
                    'teacherProfile.courses' => function($query) use ($selectedSeason) {
                        $query->where('season_id', $selectedSeason->id)
                            ->withPivot('role', 'hours_assigned');
                    },
                    'consents' => function($query) use ($selectedSeason) {
                        $query->where('season', $selectedSeason->slug);
                    }
                ])
                ->get()
                ->map(function($user) use ($selectedSeason) {
                    $teacherProfile = $user->teacherProfile;
                    $courses = $teacherProfile->courses ?? collect();
                    
                    // Consentiment RGPD
                    $rgpdConsent = $user->consents->first();
                    
                    return [
                        'user' => $user,
                        'teacher_profile' => $teacherProfile,
                        'courses' => $courses,
                        'rgpd_consent' => $rgpdConsent,
                        'has_rgpd_consent' => $rgpdConsent !== null,
                        'total_courses' => $courses->count(),
                    ];
                });
        }

        // Passem les variables amb els noms que espera la vista
        return view('treasury.teachers.rgpd-index', [
            'teachersWithCourses' => $teachers,
            'season' => $selectedSeason,
            'seasons' => $seasons,
            'selectedSeasonSlug' => $selectedSeasonSlug,
            'selectedSeason' => $selectedSeason,
        ]);
    }

    public function show(User $teacher)
    {
        $this->authorize('campus.teachers.financial_data.view');
        
        /*
        $season = CampusSeason::where('is_current', true)->first();
        $currentSeason = CampusSeason::current()->first();
        */
        
        $teacher->load('treasuryData');

        return view('treasury.teachers.show', compact('teacher'));
    }

    public function storeConsent(Request $request, User $teacher)
    {
        if (
            auth()->id() !== $teacher->id &&
            ! auth()->user()->can('campus.consents.request')
        ) {
            abort(403);
        }

        $season = CampusSeason::where('is_current', true)->first();
        $currentSeason = CampusSeason::current()->first();

        $seasonCode = $season->slug;
        $acceptedAt = now();

        $checksum = hash('sha256', implode('|', [
            $teacher->id,
            $seasonCode,
            $acceptedAt->timestamp,
            auth()->id(),
        ]));

        $pdf = Pdf::loadView('pdfs.teacher-consent', [
            'teacher' => $teacher,
            'season' => $season,
            'acceptedAt' => $acceptedAt,
            'delegatedBy' => auth()->id() !== $teacher->id ? auth()->user() : null,
            'delegatedReason' => auth()->id() !== $teacher->id
                 ? $request->input('delegated_reason') 
                : null,
            'checksum' => $checksum,
        ]);
        
        // Ruta para guardar el PDF (coherente con TeacherAccessController)
        // Usar ID del profesor, no del admin que está firmando
        $path = "consents/users/{$teacher->id}/consent_{$seasonCode}.pdf";
        
        Storage::disk('local')->put(  // guarda el pdf a la ubicacio de $path
            $path,
            $pdf->output()
        );
        // guarda a la tabla
        ConsentHistory::updateOrCreate([
            'teacher_id' => $teacher->id,
            'season' => $seasonCode,
            'accepted_at' => $acceptedAt,
            'checksum' => $checksum,
            'document_path' => $path ?? null, 

            // Delegació
            'delegated_by_user_id' => auth()->id() !== $teacher->id
                ? auth()->id()
                : null,

            'delegated_reason' => auth()->id() !== $teacher->id
                ? $request->input('delegated_reason') // CORREGIT
                : null,
        ]);
        // y cudar a la taula
        TreasuryData::updateOrCreate(
            [
                'teacher_id' => $teacher->id,
                'key' => 'consent_signed_at',
            ],
            [
                'value' => $acceptedAt->toDateTimeString(),
            ]
        );
        
        return redirect()
            ->route('campus.treasury.teachers.show', $teacher)
            ->with('success', 'Consentiment RGPD registrat correctament.');
    }


    public function export(Request $request, string $format)
    {
        $this->authorize('campus.teachers.financial_data.view');

        $seasonSlug = $request->input('season') ?? CampusSeason::where('is_current', true)->firstOrFail()->slug;
        $season = CampusSeason::where('slug', $seasonSlug)->firstOrFail();

        if ($format === 'csv') {
            return $this->exportCsv($seasonSlug);
        }

        return Excel::download(
            new TeachersRgpdExport($seasonSlug),
            "teachers_rgpd_{$seasonSlug}.xlsx"
        );
    }

    public function exportCsv(string $seasonSlug): StreamedResponse
    {
        $this->authorize('campus.teachers.financial_data.view');

        $season = CampusSeason::where('slug', $seasonSlug)->firstOrFail();
        $filename = "teachers_rgpd_{$seasonSlug}.csv";

        return response()->streamDownload(function () use ($seasonSlug) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'teacher_id',
                'name',
                'email',
                'rgpd_status',
                'rgpd_accepted_at',
                'delegated',
                'delegated_by',
                'season',
            ]);

            User::role('teacher')
                ->with(['consents' => function ($q) use ($seasonSlug) {
                    $q->where('season', $seasonSlug);
                }])
                ->orderBy('name')
                ->chunk(100, function ($teachers) use ($handle, $seasonSlug) {
                    foreach ($teachers as $teacher) {
                        $consent = $teacher->consents->first();

                        fputcsv($handle, [
                            $teacher->id,
                            $teacher->name,
                            $teacher->email,
                            $consent ? 'ACCEPTED' : 'PENDING',
                            $consent?->accepted_at?->format('Y-m-d H:i'),
                            $consent && $consent->delegated_by_user_id ? 'YES' : 'NO',
                            $consent?->delegated_by_user_id,
                            $seasonSlug,
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
    
    public function generateConsentPdf(User $teacher)
    {
        $this->authorize('consents.request');
       //  dd('nota viernes Pepe en linea 169 de generateConsentPdf');
       
        $season = CampusSeason::where('is_current', true)->first();
        $currentSeason = CampusSeason::current()->first();


        $existing = ConsentHistory::where('teacher_id', $teacher->id)
            ->where('season', $season)
            ->first();

        if ($existing) {
            return redirect()
                ->route('treasury.teachers.show', $teacher)
                ->with('success', 'El consentiment d’aquesta temporada ja existeix.');
        }


        $data = $teacher->treasuryData
            ->pluck('value', 'key')
            ->toArray();

        $acceptedAt = now();

        $pdf = Pdf::loadView('treasury.consents.pdf', [
            'teacher' => $teacher,
            'season' => $season,
            'data' => $data,
            'acceptedAt' => $acceptedAt,
        ]);

        $path = "consents/{$season}/teacher_{$teacher->id}.pdf";

        Storage::disk('private')->put($path, $pdf->output());

        $checksum = hash('sha256', Storage::disk('private')->get($path));

        ConsentHistory::updateOrCreate(
            [
                'teacher_id' => $teacher->id,
                'season' => $season,
            ],
            [
                'document_path' => $path ?? null,
                'accepted_at' => $acceptedAt,
                'checksum' => $checksum,
            ]
        );

        return redirect()
            ->route('treasury.teachers.show', $teacher)
            ->with('success', 'Consentiment PDF generat i registrat.');
    }


    public function consentHistory(User $teacher)
    {
        $this->authorize('campus.consents.view');
        
        $consents = ConsentHistory::where('teacher_id', $teacher->id)
            ->orderByDesc('season')
            ->get();


        return view('treasury.teachers.consents', compact('teacher', 'consents'));
    }

    public function downloadTeacherPaymentPdf(User $teacher, $season, $course)
    {
        $this->authorize('campus.teachers.view');
        
        $paymentPdfPath = "consents/teachers/{$teacher->id}/payment_{$season}_{$course}.pdf";
        
        if (!Storage::disk('local')->exists($paymentPdfPath)) {
            abort(404, 'PDF de pago no encontrado');
        }
        
        return Storage::disk('local')->download($paymentPdfPath, "payment_{$season}_{$course}.pdf");
    }



    public function downloadConsent(ConsentHistory $consent)
    {
        // Permitir acceso si:
        // 1. El usuario está autenticado y es el propietario
        // 2. El usuario está autenticado y tiene permisos
        // 3. No hay usuario autenticado (acceso por token) - permitir por ahora
        
        $user = auth()->user();
        
        if ($user && 
            $user->id !== $consent->teacher_id &&
            ! $user->can('campus.consents.view')
        ) {
            abort(403);
        }

        if (! Storage::disk('local')->exists($consent->document_path)) {
            abort(404, 'Document no trobat');
        }
     

        return Storage::disk('local')->download(
            $consent->document_path,
            basename($consent->document_path)
        );
    }

    public function downloadPayment(ConsentHistory $consent)
    {
        if (
            auth()->id() !== $consent->teacher_id &&
            ! auth()->user()->can('campus.consents.view')
        ) {
            abort(403);
        }

        if (! $consent->payment_document_path || $consent->payment_document_path === 'pending') {
            abort(404, 'Document de dades bancaries no trobat');
        }

        if (! Storage::disk('local')->exists($consent->payment_document_path)) {
            abort(404, 'Document de dades bancaries no trobat');
        }

        return Storage::disk('local')->download(
            $consent->payment_document_path,
            basename($consent->payment_document_path)
        );
    }

    /**
     * Importar profesores desde archivo Excel/CSV
     */
    public function import(Request $request)
    {
        $this->authorize('campus.teachers.create');
        
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
            'season' => 'nullable|string'
        ]);
        
        try {
            $file = $request->file('file');
            $season = $request->input('season');
            
            // Aquí implementaremos la lógica de importación
            // Por ahora, devolvemos un mensaje de éxito temporal
            return back()->with('success', __('campus.import_success'));
            
        } catch (\Exception $e) {
            return back()->with('error', __('campus.import_error') . ': ' . $e->getMessage());
        }
    }



}
