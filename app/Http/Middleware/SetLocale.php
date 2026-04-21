<?php

namespace App\Http\Middleware;



class SetLocale
{
    public function handle($request, Closure $next)
    {

        // Prioridad 1: Idioma de la sesión (si existe)
/*         if (session()->has('locale')) {
            $locale = session('locale');
            App::setLocale($locale);
            return $next($request);
        } 
*/

        // Evitar procesar para rutas de resolución de conflicto
        if ($request->routeIs('language.resolve-conflict')) {
            return $next($request);
        }
        
        // Prioridad 1: Idioma de la sesión (si existe)
        if (session()->has('locale')) {
            $locale = session('locale');
            App::setLocale($locale);
            
            // Verificar conflicto con preferencia de usuario solo si está autenticado
            if (Auth::check() && !session()->has('language_conflict')) {
                try {
                    $userLang = Auth::user()->getLanguage();
                    
                    if ($userLang && $userLang !== $locale) {
                        session()->flash('language_conflict', [
                            'session_language' => $locale,
                            'user_language' => $userLang
                        ]);
                    }
                } catch (\Exception $e) {
                    // Log error but continue
                    \Log::error('Error getting user language: ' . $e->getMessage());
                }
            }
            
            return $next($request);
        }

        // Prioridad 2: Idioma del usuario (si está autenticado)
        if (Auth::check()) {
            try {
                $userLang = Auth::user()->getLanguage();
                
                if ($userLang) {
                    App::setLocale($userLang);
                    session()->put('locale', $userLang);
                    return $next($request);
                }
            } catch (\Exception $e) {
                // Log error but continue
                \Log::error('Error getting user language: ' . $e->getMessage());
            }
        }
        
        // Prioridad 3: Idioma global del sitio
        $lang = Cache::remember('global_language', now()->addDay(), function () {
            return Setting::where('key', 'language')->value('value') ?? config('app.locale');
        });

        App::setLocale($lang);
        session()->put('locale', $lang);

        return $next($request);
    }

}
