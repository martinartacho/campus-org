<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function set(Request $request)
    {
        $request->validate([
            'locale' => 'required|in:en,es,ca',
        ]);

        Session::put('locale', $request->locale);

        return back();
    }

    public function resolveConflict(Request $request)
    {
        $user = $request->user();
        $action = $request->input('action');
        $conflict = session('language_conflict');

        if (!$conflict || !$user) {
            return redirect()->back();
        }

        switch ($action) {
            case 'use_user':
                // Usar preferencia del usuario
                session()->put('locale', $conflict['user_language']);
                App::setLocale($conflict['user_language']);
                break;

            case 'use_session':
                // Mantener idioma de sesión actual
                // No se necesita acción adicional
                break;

            case 'update_preference':
                // Actualizar preferencia del usuario al idioma de sesión
                $user->settings()->updateOrCreate(
                    ['key' => 'language'],
                    ['value' => $conflict['session_language']]
                );
                break;
        }

        // Eliminar el conflicto de la sesión
        session()->forget('language_conflict');

        return redirect()->back()->with('status', 'language-conflict-resolved');
    }
}
