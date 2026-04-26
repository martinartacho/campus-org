<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Cart;
use App\Models\User;

echo "=== VERIFICAR CARROS DEL USUARIO ===\n";

// Buscar usuario por email
$user = User::where('email', 'fempinyapp@gmail.com')->first();
if (!$user) {
    echo "❌ Usuario no encontrado\n";
    exit;
}

echo "✅ Usuario encontrado: ID {$user->id}\n";

// Buscar carros del usuario
$carts = Cart::where('user_id', $user->id)->get();
echo "Carros del usuario: " . $carts->count() . "\n";

foreach ($carts as $cart) {
    echo "- ID: {$cart->id}, Session: {$cart->session_id}, Status: {$cart->status}, Expires: {$cart->expires_at}\n";
}

// Buscar carros con session_id duplicado
$sessionId = 'mi57TxoJKYwGpl93tN2l1bF9dgwwTwimkJnH5Svq';
$duplicateCart = Cart::where('session_id', $sessionId)->first();
if ($duplicateCart) {
    echo "❌ Session ID duplicado encontrado:\n";
    echo "  - Cart ID: {$duplicateCart->id}\n";
    echo "  - User ID: {$duplicateCart->user_id}\n";
    echo "  - Status: {$duplicateCart->status}\n";
} else {
    echo "✅ Session ID no duplicado\n";
}
