<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Log;

class StripeService
{
    protected $secretKey;
    protected $webhookSecret;

    public function __construct()
    {
        $this->secretKey = config('services.stripe.secret');
        $this->webhookSecret = config('services.stripe.webhook_secret');
        
        Stripe::setApiKey($this->secretKey);
    }

    /**
     * Create a Stripe Checkout Session for course registration
     */
    public function createCheckoutSession(array $items, array $metadata = []): Session
    {
        $lineItems = [];

        foreach ($items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['name'],
                        'description' => $item['description'] ?? '',
                        'metadata' => $item['metadata'] ?? []
                    ],
                    'unit_amount' => $item['price'] * 100, // Convert to cents
                ],
                'quantity' => $item['quantity'] ?? 1,
            ];
        }

        $sessionData = [
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel'),
            'metadata' => $metadata,
            'allow_promotion_codes' => false,
            'billing_address_collection' => 'required',
            'customer_email' => $metadata['email'] ?? null,
        ];

        return Session::create($sessionData);
    }

    /**
     * Retrieve a checkout session
     */
    public function retrieveCheckoutSession(string $sessionId): Session
    {
        return Session::retrieve($sessionId);
    }

    /**
     * Verify and process webhook
     */
    public function verifyWebhook(string $payload, string $sigHeader): ?object
    {
        try {
            // Skip verification for test signatures or in local environment
            if (app()->environment('local') && $sigHeader === 'test_signature') {
                Log::info('Stripe webhook verification skipped for test', [
                    'environment' => app()->environment(),
                    'signature' => $sigHeader
                ]);
                
                // Create mock event for testing
                $data = json_decode($payload, true);
                $sessionObject = $data['data']['object'];
                
                // Asegurar que metadata sea objeto
                if (isset($sessionObject['metadata']) && is_array($sessionObject['metadata'])) {
                    $sessionObject['metadata'] = (object) $sessionObject['metadata'];
                }
                
                return (object) [
                    'type' => $data['type'] ?? 'checkout.session.completed',
                    'data' => (object) [
                        'object' => (object) $sessionObject
                    ],
                    'id' => 'test_' . time()
                ];
            }
            
            $event = Webhook::constructEvent(
                $payload, 
                $sigHeader, 
                $this->webhookSecret
            );

            Log::info('Stripe webhook verified', [
                'event_type' => $event->type,
                'event_id' => $event->id
            ]);

            return $event;
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
                'signature' => $sigHeader
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing error', [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Create a refund
     */
    public function createRefund(string $paymentIntentId, int $amount = null): object
    {
        $refundData = [
            'payment_intent' => $paymentIntentId,
        ];

        if ($amount) {
            $refundData['amount'] = $amount;
        }

        return \Stripe\Refund::create($refundData);
    }

    /**
     * Get payment intent details
     */
    public function getPaymentIntent(string $paymentIntentId): object
    {
        return \Stripe\PaymentIntent::retrieve($paymentIntentId);
    }

    /**
     * Check if payment is successful
     */
    public function isPaymentSuccessful(object $session): bool
    {
        return $session->payment_status === 'paid' && 
               $session->status === 'complete';
    }

    /**
     * Get customer information from session
     */
    public function getCustomerInfo(object $session): array
    {
        return [
            'email' => $session->customer_details->email ?? null,
            'name' => $session->customer_details->name ?? null,
            'phone' => $session->customer_details->phone ?? null,
            'address' => $session->customer_details->address ?? null,
        ];
    }

    /**
     * Calculate total amount from line items
     */
    public function calculateTotal(array $items): float
    {
        $total = 0;
        
        foreach ($items as $item) {
            $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        }
        
        return $total;
    }

    /**
     * Format price for Stripe (in cents)
     */
    public function formatPrice(float $price): int
    {
        return (int) round($price * 100);
    }

    /**
     * Format price from Stripe (from cents)
     */
    public function formatPriceFromStripe(int $price): float
    {
        return $price / 100;
    }
}
