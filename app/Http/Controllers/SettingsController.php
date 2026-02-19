<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function edit()
    {
        $logFiles = File::files(storage_path('logs'));
        // Últimos 5 logs push
        $logFiles = collect(File::files(storage_path('logs')))
            ->filter(fn ($file) => str_contains($file->getFilename(), 'push-'))
                ->sortByDesc(fn ($file) => $file->getCTime())
                ->take(5);
        $language = Setting::where('key', 'language')->value('value') ?? config('app.locale');

        $settings = [
                'logo' => \App\Models\Setting::get('logo', 'logos/default.png'),
                'language' => $language,
                'pushLogs' => $logFiles,
            ];
        return view('settings.edit', compact('settings'));
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,JPG,jpeg,svg', 'max:2048'],
        ]);

        $logoPath = $request->file('logo')->store('logos', 'public');

        Setting::set('logo', $logoPath);

        return redirect()->route('settings.edit')->with('success', 'Logo actualizado correctamente.');
    }

    public function updateLanguage(Request $request)
    {
        $request->validate([
            'language' => ['required', 'in:en,es,ca'],
        ]);

        Setting::updateOrCreate(
            ['key' => 'language'],
            ['value' => $request->language]
        );

        // cache()->forget('global_language');
        // Actualizar la caché inmediatamente
        // cache(['global_language' => $request->language], now()->addDay());

        // Actualizar caché inmediatamente
        Cache::put('global_language', $request->language, now()->addDay());

        // Actualizar el locale en tiempo real para esta sesión
        app()->setLocale($request->language);
        session()->put('locale', $request->language);

        return redirect()->route('settings.edit')->with('success', __('Idioma actualizado correctamente.'));
    }

}
