<?php

namespace App\Console\Commands;

use App\Models\ReleaseNote;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReleasePublishCommand extends Command
{
    protected $signature = 'release:publish {--release-version= : Versió específica a publicar} {--auto-notify : Enviar notificacions automàticament} {--dry-run : Simular sense publicar}';

    protected $description = 'Publicar Release Note i enviar notificacions';

    public function handle()
    {
        $this->info('🚀 Publicant Release Note...');

        $version = $this->option('release-version');
        $autoNotify = $this->option('auto-notify');
        $dryRun = $this->option('dry-run');

        // Obtener release a publicar
        $release = $this->getReleaseToPublish($version);

        if (!$release) {
            $this->error('❌ No s\'ha trobat el Release Note per publicar');
            return 1;
        }

        $this->info("📝 Release: {$release->title} (v{$release->version})");

        // Validar release
        if (!$this->validateRelease($release)) {
            $this->error('❌ El Release Note no és vàlid per publicar');
            return 1;
        }

        if ($dryRun) {
            $this->info('👁️ Mode simulació - No es publicarà');
            $this->showReleaseSummary($release);
            return 0;
        }

        // Confirmar publicación
        if (!$this->confirm("📢 Vols publicar aquest Release Note?")) {
            $this->info('❌ Operació cancel·lada');
            return 0;
        }

        // Publicar release
        $this->publishRelease($release);

        // Enviar notificaciones
        if ($autoNotify) {
            $this->sendNotifications($release);
        }

        $this->info("✅ Release Note publicat correctament");
        return 0;
    }

    private function getReleaseToPublish(?string $version): ?ReleaseNote
    {
        $query = ReleaseNote::where('status', 'draft');

        if ($version) {
            $query->where('version', $version);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->first();
    }

    private function validateRelease(ReleaseNote $release): bool
    {
        $errors = [];

        if (empty($release->title)) {
            $errors[] = 'El títol és obligatori';
        }

        if (empty($release->version)) {
            $errors[] = 'La versió és obligatòria';
        }

        if (empty($release->content)) {
            $errors[] = 'El contingut és obligatori';
        }

        // Verificar que no exista la misma versión
        $existing = ReleaseNote::where('version', $release->version)
            ->where('status', 'published')
            ->where('id', '!=', $release->id)
            ->first();

        if ($existing) {
            $errors[] = "Ja existeix un release publicat amb la versió {$release->version}";
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->error("❌ {$error}");
            }
            return false;
        }

        return true;
    }

    private function showReleaseSummary(ReleaseNote $release): void
    {
        $this->info("\n📋 RESUM DEL RELEASE:");
        $this->info("================================");
        $this->info("Títol: {$release->title}");
        $this->info("Versió: {$release->version}");
        $this->info("Tipus: {$release->type}");
        $this->info("Summary: {$release->summary}");
        $this->info("Mòduls afectats: " . implode(', ', $release->affected_modules ?? []));
        $this->info("Features: " . count($release->features ?? []));
        $this->info("Fixes: " . count($release->fixes ?? []));
        $this->info("Commits: " . count($release->commits ?? []));

        if (!empty($release->affected_modules)) {
            $this->info("\n👥 Usuaris a notificar:");
            foreach ($release->affected_modules as $module) {
                $count = $this->getAffectedUsersCount($module);
                $this->info("  - {$module}: {$count} usuaris");
            }
        }
    }

    private function publishRelease(ReleaseNote $release): void
    {
        $release->status = 'published';
        $release->published_at = now();
        $release->published_by = auth()->id() ?? 1;
        $release->save();

        $this->info("📢 Release publicat: {$release->title}");
    }

    private function sendNotifications(ReleaseNote $release): void
    {
        $this->info('📧 Enviant notificacions...');

        $affectedUsers = $this->getAffectedUsers($release);
        $totalUsers = $affectedUsers->count();

        if ($totalUsers === 0) {
            $this->info('ℹ️ No hi ha usuaris afectats per notificar');
            return;
        }

        // Crear notificación
        $notification = Notification::create([
            'title' => "🚀 Nou Release: {$release->title}",
            'content' => $this->generateNotificationContent($release),
            'type' => 'release',
            'sender_id' => auth()->id() ?? 1,
            'is_published' => true,
            'published_at' => now(),
        ]);

        // Asociar usuarios
        $notification->recipients()->attach($affectedUsers->pluck('id'), [
            'read' => false,
            'email_sent' => false,
            'web_sent' => false,
            'push_sent' => false,
        ]);

        $this->info("📧 Notificació creada per {$totalUsers} usuaris");

        // Programar envío de emails
        $this->scheduleEmailNotifications($notification);
    }

    private function getAffectedUsers(ReleaseNote $release): \Illuminate\Support\Collection
    {
        $users = collect();

        if (empty($release->affected_modules)) {
            // Si no hay módulos específicos, notificar a todos los usuarios activos
            return User::where('email_verified_at', '!=', null)->get();
        }

        // Notificar según módulos afectados
        foreach ($release->affected_modules as $module) {
            $moduleUsers = $this->getUsersByModule($module);
            $users = $users->merge($moduleUsers);
        }

        return $users->unique('id');
    }

    private function getUsersByModule(string $module): \Illuminate\Support\Collection
    {
        switch ($module) {
            case 'admin':
                return User::role('admin')->get();
            case 'campus':
            case 'teacher':
                return User::role('teacher')->get();
            case 'dashboard':
                return User::whereHas('roles', function($query) {
                    $query->whereIn('name', ['admin', 'teacher', 'student']);
                })->get();
            case 'help':
                return User::where('email_verified_at', '!=', null)->get();
            default:
                return collect();
        }
    }

    private function getAffectedUsersCount(string $module): int
    {
        return $this->getUsersByModule($module)->count();
    }

    private function generateNotificationContent(ReleaseNote $release): string
    {
        $content = "S'ha publicat un nou release amb les següents novetats:\n\n";
        $content .= "**{$release->title}**\n";
        $content .= "{$release->summary}\n\n";

        if (!empty($release->features)) {
            $content .= "🆕 **Novetats:**\n";
            foreach (array_slice($release->features, 0, 3) as $feature) {
                $content .= "- {$feature['title']}\n";
            }
            if (count($release->features) > 3) {
                $content .= "- I " . (count($release->features) - 3) . " més...\n";
            }
            $content .= "\n";
        }

        if ($release->hasBreakingChanges()) {
            $content .= "⚠️ **Important:** Aquest release conté canvis disruptius. Revisa la documentació.\n\n";
        }

        $content .= "📖 [Veure detalls complet]({url('/releases/' . $release->slug)})";

        return $content;
    }

    private function scheduleEmailNotifications(Notification $notification): void
    {
        // Programar envío en 5 minutos para dar tiempo a revisión
        $notification->published_at = now()->addMinutes(5);
        $notification->save();

        $this->info('⏰ Notificacions programades per enviar en 5 minuts');
    }
}
