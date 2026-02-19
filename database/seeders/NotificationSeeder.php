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
        $admin = User::role('admin')->firstOrFail();
        $gestor = User::role('gestor')->firstOrFail();
        $treasury = User::role('treasury')->firstOrFail();

        $editors = User::role('editor')->take(2)->get();
        if ($editors->count() < 2) {
            $this->command->error('Se necesitan al menos 2 usuarios con rol editor');
            return;
        }

        $regularUsers = User::role('user')->take(2)->get();
        if ($regularUsers->count() < 2) {
            $this->command->error('Se necesitan al menos 2 usuarios con rol user');
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
            'title' => 'Mantenimiento programado',
            'content' => 'El sistema estará inactivo el próximo sábado',
            'sender_id' => $admin->id,
            'recipient_type' => 'all',
            'is_published' => true,
            'published_at' => now(),
            'web_sent' => true
        ]);

        // 3. Por rol
        Notification::create([
            'title' => 'Nuevas directrices editoriales',
            'content' => 'Por favor revisen las nuevas normas',
            'sender_id' => $gestor->id,
            'recipient_type' => 'role',
            'recipient_role' => 'editor',
            'is_published' => true,
            'published_at' => now()->subDay(),
            'web_sent' => true
        ]);

        // 4. Específica
        $specificNotification = Notification::create([
            'title' => 'Tu artículo ha sido aprobado',
            'content' => 'Felicitaciones por tu publicación',
            'sender_id' => $editors[0]->id,
            'recipient_type' => 'specific',
            'recipient_ids' => [$regularUsers[0]->id, $regularUsers[1]->id],
            'is_published' => true,
            'published_at' => now()->subHours(3),
            'web_sent' => true
        ]);
        $specificNotification->recipients()->attach([$regularUsers[0]->id, $regularUsers[1]->id]);

        // 5. Borrador
        Notification::create([
            'title' => 'Borrador: Cambios en políticas',
            'content' => 'Esta notificación está pendiente de revisión',
            'sender_id' => $editors[1]->id,
            'recipient_type' => 'all',
            'is_published' => false,
            'published_at' => null
        ]);

        $this->command->info('Notificaciones de prueba creadas exitosamente');
    }
}
