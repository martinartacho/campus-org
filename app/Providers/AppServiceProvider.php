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
        if ($this->app->environment('local')) {
            // Desactivar realmente el envío de emails en local
            Mail::alwaysTo('preview@mailpit');
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
