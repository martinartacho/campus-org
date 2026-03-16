<?php

namespace App\Providers;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Support\Facades\Mail;
use App\Services\ExportService;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Channels\DatabaseChannel;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ExportService::class, function () {
            return new ExportService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configurar emails en entorno local (evitar duplicados con archivo estático)
        if ($this->app->environment('local') && !config('email-security.configured', false)) {
            Mail::alwaysTo('preview@mailpit');
            config(['mail.default' => 'log']);
            app('config')->set('mail.default', 'log'); // Forzar configuración
            
            Log::info('🔒 Emails redirigidos a preview@mailpit (entorno local)');
            Log::info('🔒 Mailer cambiado a "log" para desarrollo');
            
            // Marcar como configurado en archivo estático
            file_put_contents(config_path('email-security.php'), "<?php\n\nreturn [\n    'configured' => true,\n];");
        }
        
        // En producción, no redirigir emails
        if ($this->app->environment('production')) {
            // Configuración de producción - emails reales
            // Mail::alwaysTo(null); // Desactiva el redireccionamiento
        }
        // Middleware para manejar el idioma
        $this->app->router->group([
            'namespace' => 'App\Http\Controllers',
        ], function ($router) {
            require base_path('routes/web.php');
        });

        // Establecer idioma según sesión o configuración
      //   App::setLocale(Session::get('locale', config('app.locale')));
	    Notification::extend('fcm', function ($app) {
        return new FcmChannel($app->make(\App\Services\FCMService::class));
    });

        \App\Models\User::observe(\App\Observers\UserObserver::class);  
    }
}
