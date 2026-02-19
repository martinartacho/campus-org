<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class PushLogController extends Controller
{
    public function index()
    {
        $logFiles = File::files(storage_path('logs'));

        $pushLogs = collect($logFiles)
            ->filter(fn ($file) => str_contains($file->getFilename(), 'push-'))
            ->sortByDesc(fn ($file) => $file->getCTime());

        return view('admin.push-logs', ['logs' => $pushLogs]);
    }

    public function download($filename)
    {
        $path = storage_path("logs/{$filename}");

        if (!File::exists($path)) {
            abort(404);
        }

        return Response::download($path);
    }

    public function delete($filename)
    {
        $path = storage_path("logs/{$filename}");

        if (!File::exists($path)) {
            return redirect()->back()->withErrors('Archivo no encontrado.');
        }

        File::delete($path);

        return redirect()->back()->with('success', "Archivo '{$filename}' eliminado correctamente.");
    }
}
