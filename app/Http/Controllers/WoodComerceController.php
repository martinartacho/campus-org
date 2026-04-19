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
    
    /**
     * Vista previa de primeros productos
     */
    public function preview(Request $request)
    {
        try {
            Log::info('WoodComerce: Generando vista previa');
            
            // Obtener primeros cursos para preview
            $courses = \App\Models\CampusCourse::where('is_active', 1)
                ->where('is_public', 1)
                ->with(['category', 'season'])
                ->orderBy('code')
                ->limit(10)
                ->get();
            
            $previewData = [];
            
            foreach ($courses as $course) {
                if ($course->parent_id) {
                    // Es variación
                    $parent = \App\Models\CampusCourse::find($course->parent_id);
                    if ($parent) {
                        $previewData[] = $this->etlService->createVariation($parent, $course);
                    }
                } else {
                    // Es parent
                    $children = \App\Models\CampusCourse::where('parent_id', $course->id)->get();
                    
                    if ($children->count() > 0) {
                        $previewData[] = $this->etlService->createVariableProduct($course, $children);
                        
                        // Agregar una variación como ejemplo
                        $firstChild = $children->first();
                        $previewData[] = $this->etlService->createVariation($course, $firstChild);
                    } else {
                        $previewData[] = $this->etlService->createSimpleProduct($course);
                    }
                }
            }
            
            Log::info('WoodComerce: Vista previa generada', ['count' => count($previewData)]);
            
            return response()->json([
                'success' => true,
                'data' => $previewData,
                'message' => 'Vista previa generada correctamente'
            ]);
            
        } catch (\Exception $e) {
            Log::error('WoodComerce: Error en vista previa', [
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
     * Testing con cursos seleccionados
     */
    public function test(Request $request)
    {
        try {
            $courseIds = $request->input('course_ids', []);
            
            Log::info('WoodComerce: Iniciando test específico', ['course_ids' => $courseIds]);
            
            if (empty($courseIds)) {
                return response()->json([
                    'success' => false,
                    'error' => 'No se seleccionaron cursos para testing'
                ], 400);
            }
            
            // Procesar cursos específicos
            $testData = $this->etlService->processSpecific($courseIds);
            
            Log::info('WoodComerce: Test completado', ['count' => count($testData)]);
            
            return response()->json([
                'success' => true,
                'data' => $testData,
                'message' => 'Test completado correctamente'
            ]);
            
        } catch (\Exception $e) {
            Log::error('WoodComerce: Error en test', [
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
}
