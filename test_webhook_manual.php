<?php

echo "=== PROBAR WEBHOOK MANUALMENTE ===\n";

// Simular payload de Stripe para el payment intent real
$webhookPayload = [
    'type' => 'checkout.session.completed',
    'data' => [
        'object' => [
            'id' => 'cs_test_' . time(),
            'payment_status' => 'paid',
            'payment_intent' => 'pi_3TQNihKBfhNEfJhc1GG0pQjo',
            'amount_total' => 5000, // 50.00 EUR en centavos
            'currency' => 'eur',
            'customer_email' => 'fempinyapp@gmail.com',
            'metadata' => (object)[
                'registration_ids' => '728'
            ],
            'created' => time()
        ]
    ]
];

$ch = curl_init('http://campus-org.test/stripe/webhook');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookPayload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Stripe-Signature: test_signature'
]);

// Deshabilitar verificación de firma para pruebas
$_ENV['STRIPE_WEBHOOK_SECRET'] = 'test';

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: " . $httpCode . "\n";
echo "Response: " . substr($response, 0, 500) . "...\n";

if ($httpCode === 200) {
    echo "✅ Webhook respondió OK\n";
} else {
    echo "❌ Webhook falló con código: " . $httpCode . "\n";
}

echo "\nRevisar logs en: storage/logs/laravel.log\n";
