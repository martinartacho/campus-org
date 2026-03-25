<?php

namespace App\Http\Controllers;

use App\Models\ReleaseNote;
use Illuminate\Http\Request;

class ReleaseController extends Controller
{
    public function index(Request $request)
    {
        $releases = ReleaseNote::published()
            ->latest()
            ->paginate(10);

        return view('releases.index', compact('releases'));
    }

    public function show($slug)
    {
        $release = ReleaseNote::where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Marcar como leído si el usuario está autenticado
        if (auth()->check()) {
            // Aquí podríamos implementar un sistema de lectura
        }

        return view('releases.show', compact('release'));
    }

    public function latest()
    {
        $release = ReleaseNote::published()
            ->latest()
            ->first();

        if (!$release) {
            abort(404, 'No hi ha releases publicats');
        }

        return redirect()->route('releases.show', $release->slug);
    }

    public function feed()
    {
        $releases = ReleaseNote::published()
            ->latest()
            ->take(20)
            ->get();

        return response()->view('releases.feed', compact('releases'))
            ->header('Content-Type', 'application/xml');
    }
}
