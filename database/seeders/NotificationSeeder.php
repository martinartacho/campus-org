<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // 🔒 Desactivar restricciones de claves foráneas
/*
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Vaciar tablas relacionadas
        DB::table('notification_user')->truncate();
        DB::table('notifications')->truncate();

        // 🔒 Reactivar restricciones
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
*/
        // Obtener usuarios con roles específicos
        $admin = User::role('admin')->first();
        $gestor = User::role('gestio')->first(); // Cambiado de 'manager' a 'gestio'
        $treasury = User::role('treasury')->first();

        if (!$admin) {
            $this->command->error('No se encontró usuario con rol admin');
            return;
        }

        if (!$gestor) {
            $this->command->error('No se encontró usuario con rol gestio');
            return;
        }

        if (!$treasury) {
            $this->command->error('No se encontró usuario con rol treasury');
            return;
        }

        $editors = User::role('editor')->take(2)->get();
        if ($editors->count() < 1) { // Cambiado de 2 a 1
            $this->command->error('Se necesitan al menos 1 usuario con rol editor');
            return;
        }

        $regularUsers = User::role('user')->take(2)->get();
        if ($regularUsers->count() < 1) { // Cambiado de 2 a 1
            $this->command->error('Se necesitan al menos 1 usuario con rol user');
            return;
        }

        // 1. Notificación welcome (id = 1)
/*        Notification::create([
            'title' => '¡Bienvenido a la app!',
            'content' => 'Gracias por unirte. Esperamos que disfrutes de todas las funcionalidades.',
            'sender_id' => $admin->id,
            'recipient_type' => 'specific',
            'recipient_ids' => 'all', // json_encode([$regularUsers[0]->id]),
            'type' => 'welcome',
            'is_published' => true,
            'published_at' => now(),
            'web_sent' => false
        ]);
*/
        // 2. Notificación pública
        Notification::create([
            'title' => 'Manteniment programat',
            'content' => 'El sistema estarà inactiu el proper dissabte',
            'sender_id' => $admin->id,
            'recipient_type' => 'all',
            'is_published' => true,
            'published_at' => now(),
            'web_sent' => true
        ]);

        // 3. Por rol
        Notification::create([
            'title' => 'Noves directrius editorials',
            'content' => 'Si us plau, reviseu les noves normes',
            'sender_id' => $gestor->id,
            'recipient_type' => 'role',
            'recipient_role' => 'editor',
            'is_published' => true,
            'published_at' => now()->subDay(),
            'web_sent' => true
        ]);

        // 4. Específica
        $specificNotification = Notification::create([
            'title' => 'El teu article ha estat aprovat',
            'content' => 'Felicitacions per la teva publicació',
            'sender_id' => $editors[0]->id,
            'recipient_type' => 'specific',
            'recipient_ids' => [$regularUsers[0]->id], // Solo un usuario
            'is_published' => true,
            'published_at' => now()->subHours(3),
            'web_sent' => true
        ]);
        $specificNotification->recipients()->attach([$regularUsers[0]->id]); // Solo un usuario

        // 5. Borrador
        Notification::create([
            'title' => 'Esborrany: Canvis en polítiques',
            'content' => 'Aquesta notificació està pendent de revisió',
            'sender_id' => $editors[0]->id, // Usar el primer editor disponible
            'recipient_type' => 'all',
            'is_published' => false,
            'published_at' => null
        ]);

        $this->command->info('Notificacions de prova creades correctament');
    }
}
