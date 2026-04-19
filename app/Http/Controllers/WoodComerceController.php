<?php

namespace App\Http\Controllers;

use App\Services\WoodComerceETLService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WoodComerceController extends Controller
{
    private $etlService;
    
    public function __construct(WoodComerceETLService $etlService)
    {
        $this->etlService = $etlService;
    }
    
    /**
     * Mostrar interfaz de exportación
     */
    public function index()
    {
        return view('campus.courses.woodcomerce');
    }
    
    /**
     * Generar y descargar CSV completo
     */
    public function export(Request $request)
    {
        try {
            Log::info('WoodComerce: Iniciando exportación completa');
            
            // Generar CSV
            $csv = $this->etlService->process();
            
            // Crear nombre de archivo
            $filename = 'wc-export-' . now()->format('Y-m-d-H-i-s') . '.csv';
            
            Log::info('WoodComerce: CSV generado', ['filename' => $filename]);
            
            // Descargar archivo
            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            Log::error('WoodComerce: Error en exportación', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()->with('error', 'Error al generar CSV: ' . $e->getMessage());
        }
    }
}
