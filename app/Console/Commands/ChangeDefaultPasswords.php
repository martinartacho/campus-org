<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChangeDefaultPasswords extends Command
{

    protected $signature = 'users:change-all-passwords';
    protected $description = 'Cambia la contraseña de todos los usuarios existentes con una nueva contraseña segura';

    public function handle()
    {
        $this->info("🔐 Cambio masivo de contraseñas de usuarios");


        // Solicitar nueva contraseña
        $password = $this->secret('Introduce la nueva contraseña:');
        $confirm  = $this->secret('Confirma la nueva contraseña:');

        // Verificar que coincidan
        if ($password !== $confirm) {
            $this->error("❌ Las contraseñas no coinciden.");
            return 1;
        }

        // Validar seguridad mínima
        if (!$this->isValidPassword($password)) {
            $this->error("❌ La contraseña no cumple los requisitos mínimos:");
            $this->line("- Al menos 8 caracteres");
            $this->line("- Al menos una mayúscula, una minúscula, un número y un símbolo");
            return 1;
        }

        // Cambiar la contraseña a todos los usuarios
        $users = User::all();
        foreach ($users as $user) {
            $user->password = Hash::make($password);
            $user->save();
            $this->info("✅ Contraseña cambiada para: {$user->email}");
        }

        $this->info("🎉 Todas las contraseñas han sido actualizadas correctamente.");
        return 0;
    }

    protected function isValidPassword(string $password): bool
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
    }
}

