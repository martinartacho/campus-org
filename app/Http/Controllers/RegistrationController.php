<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CampusCourse;
use App\Models\CampusRegistration;
use App\Models\CampusStudent;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Show registration form
     */
    public function create()
    {
        $cart = Cart::getCurrent();

        if (!$cart || $cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Tu carrito está vacío. Añade cursos antes de matricularte.');
        }

        // Load cart with items and validate
        $cart->load(['items.course', 'items.course.season', 'items.course.category']);

        // Check for invalid items
        $invalidItems = [];
        $validItems = collect();

        foreach ($cart->items as $item) {
            $issues = $item->getValidationIssues();
            if (!empty($issues)) {
                $invalidItems[] = [
                    'item' => $item,
                    'issues' => $issues
                ];
            } else {
                $validItems->push($item);
            }
        }

        if (!empty($invalidItems)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Hay problemas con algunos cursos en tu carrito. Por favor, revísalos antes de continuar.');
        }

        if ($validItems->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'No hay cursos válidos en tu carrito.');
        }

        // Get user data if logged in
        $user = auth()->user();
        $student = null;

        if ($user) {
            $student = CampusStudent::where('user_id', $user->id)->first();
        }

        return view('registration.create', compact(
            'cart',
            'validItems',
            'user',
            'student'
        ));
    }

    /**
     * Process registration and create Stripe checkout session
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'dni' => 'required|string|max:20',
            'birth_date' => 'required|date|before:today',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'accept_terms' => 'required|accepted',
        ]);

        try {
            $cart = Cart::getCurrent();

            if (!$cart || $cart->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tu carrito está vacío'
                ], 400);
            }

            // Validate cart items
            $cart->load(['items.course']);
            $validItems = collect();

            foreach ($cart->items as $item) {
                if (!$item->hasValidationIssues()) {
                    $validItems->push($item);
                }
            }

            if ($validItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay cursos válidos en tu carrito'
                ], 400);
            }

            DB::beginTransaction();

            // Create or get student record
            $student = $this->createOrUpdateStudent($request);

            // Create pending registrations
            $registrations = [];
            $totalAmount = 0;

            foreach ($validItems as $item) {
                // Check if registration already exists
                $existingRegistration = CampusRegistration::where('student_id', $student->id)
                                                        ->where('course_id', $item->course_id)
                                                        ->first();
                
                if ($existingRegistration) {
                    throw new \Exception("Ya tienes una matrícula para el curso: {$item->course->title}");
                }
                
                $registration = CampusRegistration::create([
                    'student_id' => $student->id,
                    'course_id' => $item->course_id,
                    'season_id' => $item->course->season_id,
                    'user_id' => auth()->id(),
                    'registration_code' => $this->generateRegistrationCode(),
                    'registration_date' => now(),
                    'status' => 'pending',
                    'amount' => $item->price_at_time,
                    'payment_status' => 'pending',
                    'payment_due_date' => now()->addDays(7),
                    'metadata' => [
                        'cart_item_id' => $item->id,
                        'price_at_time' => $item->price_at_time,
                        'course_snapshot' => $item->course_snapshot,
                        'student_data' => [
                            'first_name' => $student->first_name,
                            'last_name' => $student->last_name,
                            'email' => $student->email,
                            'phone' => $student->phone,
                            'dni' => $student->dni,
                            'birth_date' => $student->birth_date,
                            'address' => $student->address,
                            'city' => $student->city,
                            'postal_code' => $student->postal_code,
                            'student_code' => $student->student_code,
                        ],
                        'payment_data' => [
                            'amount' => $item->price_at_time,
                            'currency' => 'EUR',
                            'payment_method' => 'stripe',
                            'registration_date' => now()->format('Y-m-d H:i:s'),
                        ]
                    ]
                ]);

                $registrations[] = $registration;
                $totalAmount += $item->price_at_time;

                // Update course available spots (reserve temporarily)
                if ($item->course->max_students) {
                    $item->course->decrement('max_students', 1);
                }
            }

            // Mark cart as converted
            $cart->markAsConverted();

            DB::commit();

            // Create Stripe checkout session if there's a payment
            if ($totalAmount > 0) {
                $lineItems = [];
                $metadata = [
                    'student_id' => $student->id,
                    'registration_ids' => implode(',', array_map(fn($r) => $r->id, $registrations)),
                    'cart_id' => $cart->id,
                    'email' => $request->email
                ];

                foreach ($validItems as $item) {
                    $lineItems[] = [
                        'name' => $item->course_title,
                        'description' => "Curso: {$item->course_code} - {$item->course->season->name}",
                        'price' => $item->price_at_time,
                        'quantity' => 1,
                        'metadata' => [
                            'course_id' => $item->course_id,
                            'registration_id' => $registrations[array_search($item, $validItems->toArray())]->id
                        ]
                    ];
                }

                $checkoutSession = $this->stripeService->createCheckoutSession($lineItems, $metadata);

                // Update registrations with stripe session ID
                foreach ($registrations as $registration) {
                    $registration->update([
                        'metadata->stripe_session_id' => $checkoutSession->id
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'redirect_url' => $checkoutSession->url,
                    'session_id' => $checkoutSession->id
                ]);
            } else {
                // Free courses - confirm registrations immediately
                foreach ($registrations as $registration) {
                    $registration->update([
                        'status' => 'confirmed',
                        'payment_status' => 'paid',
                        'payment_method' => 'free'
                    ]);
                    
                    // Sync with campus_course_student
                    \App\Models\CampusCourseStudent::updateOrCreate(
                        [
                            'course_id' => $registration->course_id,
                            'student_id' => $registration->student_id,
                        ],
                        [
                            'registration_id' => $registration->id,
                            'academic_status' => \App\Models\CampusCourseStudent::STATUS_ACTIVE,
                            'enrollment_date' => now()->format('Y-m-d'),
                            'start_date' => $registration->course->start_date ?? now()->format('Y-m-d'),
                            'end_date' => $registration->course->end_date,
                            'season_id' => $registration->season_id,
                            'metadata' => array_merge($registration->metadata ?? [], [
                                'payment_confirmed_at' => now()->format('Y-m-d H:i:s'),
                                'payment_method' => 'free',
                                'payment_amount' => $registration->amount,
                            ])
                        ]
                    );
                }

                return response()->json([
                    'success' => true,
                    'redirect_url' => route('payment.success') . '?free=true',
                    'free' => true
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la matriculación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Payment success page
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        $isFree = $request->get('free') === 'true';

        if ($isFree) {
            // Free registration - get recent registrations for current user/student
            $registrations = $this->getRecentRegistrations();
            
            if ($registrations->isEmpty()) {
                return redirect()
                    ->route('catalog.index')
                    ->with('error', 'No se encontraron matriculaciones recientes.');
            }

            return view('registration.success', compact(
                'registrations',
                'isFree'
            ));
        }

        if (!$sessionId) {
            return redirect()
                ->route('catalog.index')
                ->with('error', 'Sesión de pago no encontrada.');
        }

        try {
            $session = $this->stripeService->retrieveCheckoutSession($sessionId);

            if (!$this->stripeService->isPaymentSuccessful($session)) {
                return redirect()
                    ->route('catalog.index')
                    ->with('error', 'El pago no se ha completado correctamente.');
            }

            // Get registrations from metadata
            $registrationIds = explode(',', $session->metadata->registration_ids ?? '');
            $registrations = CampusRegistration::whereIn('id', $registrationIds)->get();

            if ($registrations->isEmpty()) {
                return redirect()
                    ->route('catalog.index')
                    ->with('error', 'No se encontraron las matriculaciones asociadas a este pago.');
            }

            return view('registration.success', compact(
                'registrations',
                'session',
                'isFree'
            ));

        } catch (\Exception $e) {
            Log::error('Payment success error: ' . $e->getMessage());
            
            return redirect()
                ->route('catalog.index')
                ->with('error', 'Error al verificar el pago. Por favor, contacta con soporte.');
        }
    }

    /**
     * Payment cancel page
     */
    public function cancel(Request $request)
    {
        $sessionId = $request->get('session_id');

        if ($sessionId) {
            try {
                $session = $this->stripeService->retrieveCheckoutSession($sessionId);
                
                // Find and cancel pending registrations
                $registrationIds = explode(',', $session->metadata->registration_ids ?? '');
                
                CampusRegistration::whereIn('id', $registrationIds)
                    ->where('status', 'pending')
                    ->update(['status' => 'cancelled']);

                // Restore course spots
                foreach ($registrationIds as $registrationId) {
                    $registration = CampusRegistration::find($registrationId);
                    if ($registration && $registration->course && $registration->course->max_students) {
                        $registration->course->increment('max_students', 1);
                    }
                }

            } catch (\Exception $e) {
                Log::error('Payment cancel error: ' . $e->getMessage());
            }
        }

        return view('registration.cancel');
    }

    /**
     * Stripe webhook handler
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('stripe-signature');

        $event = $this->stripeService->verifyWebhook($payload, $sigHeader);

        if (!$event) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event->data->object);
                    break;

                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentIntentFailed($event->data->object);
                    break;

                default:
                    Log::info('Unhandled webhook event', ['event_type' => $event->type]);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Webhook processing error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Create or update student record
     */
    private function createOrUpdateStudent(Request $request)
    {
        // Check if user with this email already exists
        $existingUser = \App\Models\User::where('email', $request->email)->first();
        
        if ($existingUser) {
            // Case 1: User exists - create student record and assign student role
            $student = CampusStudent::firstOrCreate(
                ['user_id' => $existingUser->id],
                [
                    'student_code' => $this->generateStudentCode(),
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'dni' => $request->dni,
                    'birth_date' => $request->birth_date,
                    'address' => $request->address,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'enrollment_date' => now(),
                    'is_active' => true,
                ]
            );
            
            // Assign student role if not already assigned
            $studentRole = \Spatie\Permission\Models\Role::where('name', 'student')->first();
            if ($studentRole && !$existingUser->hasRole('student')) {
                $existingUser->assignRole($studentRole);
            }
            
            return $student;
            
        } else {
            // Case 2: User doesn't exist - create user + student + role
            $userData = [
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => \Illuminate\Support\Facades\Hash::make($request->dni ?? 'password123'), // Temporary password
                'email_verified_at' => now(),
            ];
            
            $user = \App\Models\User::create($userData);
            
            // Create student record
            $student = CampusStudent::create([
                'user_id' => $user->id,
                'student_code' => $this->generateStudentCode(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'dni' => $request->dni,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'enrollment_date' => now(),
                'is_active' => true,
            ]);
            
            // Assign student role
            $studentRole = \Spatie\Permission\Models\Role::where('name', 'student')->first();
            if ($studentRole) {
                $user->assignRole($studentRole);
            }
            
            return $student;
        }
    }

    /**
     * Generate unique student code
     */
    private function generateStudentCode(): string
    {
        do {
            $code = 'STU-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (CampusStudent::where('student_code', $code)->exists());

        return $code;
    }

    /**
     * Generate and download invoice
     */
    public function invoice($registrationId)
    {
        $registration = CampusRegistration::with(['student', 'course', 'course.season'])
            ->findOrFail($registrationId);

        // Allow access if:
        // 1. User is authenticated and owns the registration, OR
        // 2. Registration was created in the last 24 hours (for guest users), OR
        // 3. Payment is confirmed (regardless of authentication)
        $canAccess = false;
        
        if (auth()->check()) {
            $canAccess = auth()->id() === $registration->user_id;
        } else {
            // Allow access for recent registrations (within 24 hours) OR confirmed payments
            $isRecent = $registration->created_at->diffInHours(now()) <= 24;
            $isPaid = $registration->payment_status === 'paid';
            $canAccess = $isRecent || $isPaid;
        }
        
        if (!$canAccess) {
            abort(403, 'No tienes permiso para ver esta factura');
        }

        // Generate PDF invoice
        $pdf = $this->generateInvoicePDF($registration);

        return $pdf->download("factura-{$registration->registration_code}.pdf");
    }

    /**
     * Generate unique registration code
     */
    private function generateRegistrationCode(): string
    {
        do {
            $code = 'REG-' . date('Y') . '-' . strtoupper(Str::random(8));
        } while (CampusRegistration::where('registration_code', $code)->exists());

        return $code;
    }

    /**
     * Generate PDF invoice
     */
    private function generateInvoicePDF($registration)
    {
        $data = [
            'registration' => $registration,
            'student' => $registration->student,
            'course' => $registration->course,
            'season' => $registration->course->season,
            'issue_date' => now()->format('d/m/Y'),
            'due_date' => $registration->payment_due_date->format('d/m/Y'),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('registration.invoice', $data);
        
        return $pdf;
    }

    /**
     * Get recent registrations for current user/student
     */
    private function getRecentRegistrations()
    {
        $query = CampusRegistration::with(['course', 'course.season', 'course.category'])
            ->where('created_at', '>=', now()->subMinutes(30))
            ->orderBy('created_at', 'desc');

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            // For guest users, we need to match by email from recent session
            $query->whereNull('user_id');
        }

        return $query->get();
    }

    /**
     * Handle checkout session completed webhook
     */
    private function handleCheckoutSessionCompleted($session)
    {
        // Get registrations from metadata
        $registrationIds = explode(',', $session->metadata->registration_ids ?? '');
        
        $registrations = CampusRegistration::whereIn('id', $registrationIds)->get();

        if ($registrations->isEmpty()) {
            Log::error('No registrations found for session', ['session_id' => $session->id]);
            return;
        }

        // Update registrations to confirmed
        foreach ($registrations as $registration) {
            $registration->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_completed_at' => now(),
                'payment_method' => 'stripe',
                'stripe_session_id' => $session->id
            ]);
            
            // Sync with campus_course_student
            \App\Models\CampusCourseStudent::updateOrCreate(
                [
                    'course_id' => $registration->course_id,
                    'student_id' => $registration->student_id,
                ],
                [
                    'registration_id' => $registration->id,
                    'academic_status' => \App\Models\CampusCourseStudent::STATUS_ACTIVE,
                    'enrollment_date' => now()->format('Y-m-d'),
                    'start_date' => $registration->course->start_date ?? now()->format('Y-m-d'),
                    'end_date' => $registration->course->end_date,
                    'season_id' => $registration->season_id,
                    'metadata' => array_merge($registration->metadata ?? [], [
                        'payment_confirmed_at' => now()->format('Y-m-d H:i:s'),
                        'stripe_session_id' => $session->id,
                        'payment_amount' => $registration->amount,
                        'stripe_payment_intent_id' => $session->payment_intent ?? null,
                    ])
                ]
            );
        }

        Log::info('Payment completed and synced', [
            'session_id' => $session->id,
            'registration_count' => $registrations->count()
        ]);
    }

    /**
     * Handle payment intent succeeded webhook
     */
    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        // Additional processing if needed
        Log::info('Payment succeeded', ['payment_intent' => $paymentIntent->id]);
    }

    /**
     * Handle payment intent failed webhook
     */
    private function handlePaymentIntentFailed($paymentIntent)
    {
        // Find registrations associated with this payment and mark as failed
        // This would require storing payment_intent_id in registration metadata
        Log::error('Payment failed', ['payment_intent' => $paymentIntent->id]);
    }
}
