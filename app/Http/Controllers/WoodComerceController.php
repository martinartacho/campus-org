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
          Log::info('WoodComerce: index');
        // dd('Hola mundo');
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
    
    /**
     * Exportar cursos seleccionados
     */
    public function exportSelected(Request $request)
    {
        try {
            Log::info('WoodComerce: Iniciando exportación seleccionada', ['course_ids' => $request->input('course_ids')]);
            
            $courseIds = $request->input('course_ids', []);
            
            if (empty($courseIds)) {
                return response()->json([
                    'success' => false,
                    'error' => 'No se seleccionaron cursos'
                ], 400);
            }
            
            // Procesar cursos específicos usando el método processSpecific del ETL
            $csvData = $this->etlService->processSpecific($courseIds);
            
            // Generar CSV
            $filename = 'wc-selected-export-' . date('Y-m-d-H-i-s') . '.csv';
            $csv = $this->generateCSV($csvData);
            
            Log::info('WoodComerce: Exportación seleccionada completada', [
                'filename' => $filename,
                'count' => count($csvData)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Exportación completada con ' . count($csvData) . ' productos',
                'file_url' => '/campus/courses/woodcomerce/download/' . $filename,
                'filename' => $filename
            ]);
            
        } catch (\Exception $e) {
            Log::error('WoodComerce: Error en exportación seleccionada', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Descargar archivo CSV
     */
    public function download($filename)
    {
        try {
            $filepath = storage_path('app/exports/' . $filename);
            
            if (!file_exists($filepath)) {
                return response()->json(['error' => 'Archivo no encontrado'], 404);
            }
            
            return response()->download($filepath, $filename, [
                'Content-Type' => 'text/csv'
            ]);
            
        } catch (\Exception $e) {
            Log::error('WoodComerce: Error en descarga', [
                'error' => $e->getMessage(),
                'filename' => $filename
            ]);
            
            return response()->json(['error' => 'Error al descargar archivo'], 500);
        }
    }
    
    /**
     * Generar CSV a partir de datos
     */
    private function generateCSV($data): string
    {
        $csv = '';
        
        // Cabecera CSV
        $headers = array_keys($data[0] ?? []);
        $csv .= implode(',', array_map([$this, 'escapeCSV'], $headers)) . "\n";
        
        // Datos
        foreach ($data as $row) {
            $csv .= implode(',', array_map([$this, 'escapeCSV'], $row)) . "\n";
        }
        
        return $csv;
    }
    
    /**
     * Escapar valor para CSV
     */
    private function escapeCSV($value): string
    {
        if (is_string($value) && (strpos($value, ',') !== false || strpos($value, '"') !== false || strpos($value, "\n") !== false)) {
            return '"' . str_replace('"', '""', $value) . '"';
        }
        return (string) $value;
    }
}
