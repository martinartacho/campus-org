<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Notification;
use App\Models\User;
use EventQuestionTemplate;
use App\Policies\EventPolicy;
use App\Policies\EventTypePolicy;
use App\Policies\EventQuestionTemplatePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Event::class => EventPolicy::class,
        EventType::class => EventTypePolicy::class,
        EventQuestionTemplate::class => EventQuestionTemplatePolicy::class,

        // ... otras políticas
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        
        // Gates para notificaciones
        Gate::define('view-notification', function (User $user, Notification $notification) {
            return $user->hasRole(['admin', 'gestor','treasury' ]) || 
                ($user->hasRole('editor') && $notification->sender_id == $user->id) ||
                $notification->recipients->contains($user->id);
        });
        
        Gate::define('edit-notification', function (User $user, Notification $notification) {
            return $user->hasRole(['admin', 'gestor', 'treasury' ]) || 
                ($user->hasRole('editor') && $notification->sender_id == $user->id);
        });

        Gate::define('update-notification', function (User $user, Notification $notification) {
            return $user->hasRole(['admin', 'gestor', 'treasury']) || 
                ($user->hasRole('editor') && $notification->sender_id == $user->id);
        });

        Gate::define('delete-notification', function (User $user, Notification $notification) {
            return $user->hasRole(['admin', 'gestor', 'treasury']) || 
                ($user->hasRole('editor') && $notification->sender_id == $user->id);
        });

        // Gates para acciones generales (sin necesidad de modelo)
        Gate::define('list-notifications', function (User $user) {
            return $user->hasAnyRole(['admin', 'gestor', 'treasury', 'editor', 'user']);
        });

        Gate::define('create-notification', function (User $user) {
            return $user->hasAnyRole(['admin', 'gestor', 'treasury', 'editor']);
        });

        Gate::define('publish-notification', function (User $user) {
            return $user->hasAnyRole(['admin', 'gestor','treasury']);
        });

        // Gates para eventos (mantenemos estos gates para compatibilidad)
        Gate::define('view-calendar', function (User $user) {
            return true; // Todos los usuarios autenticados pueden ver el calendario
        });

        Gate::define('create-events', function (User $user) {
            return $user->hasRole(['admin', 'gestor', 'treasury', 'editor']);
        });

        Gate::define('edit-events', function (User $user) {
            return $user->hasRole(['admin', 'gestor', 'treasury', 'editor']);
        });

        Gate::define('delete-events', function (User $user) {
            return $user->hasRole(['admin', 'gestor','treasury']);
        });
    }
}