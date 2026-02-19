<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\FcmToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CreateDemoUser extends Command
{
    protected $signature = 'demo:create-user';
    protected $description = 'Crea un usuario de prueba con FCM token (simulado)';

    public function handle()
    {
        $email = 'demo_user_' . Str::random(5) . '@example.com';

        $user = User::create([
            'name' => 'Usuario de prueba',
            'email' => $email,
            'password' => Hash::make('password'),
            'locale' => 'es', // o 'ca' si quieres probar catalán
        ]);

        // Asumimos que el modelo FcmToken tiene: token, user_id, etc.
        FcmToken::create([
            'user_id' => $user->id,
            'token' => 'SIMULATED_FCM_TOKEN_' . Str::random(10),
            'device' => 'DemoDevice',
            'platform' => 'android',
        ]);

        $this->info("✅ Usuario creado: $email");
        $this->info("📲 Se ha simulado token FCM y debería recibir la notificación de bienvenida");
    }
}
