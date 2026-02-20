<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Services\CampusImportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CampusImportController extends Controller
{
    public function __construct(
        private CampusImportService $importService
    ) {}

    /**
     * Mostrar formulario de importación
     */
    public function create()
    {
        $seasons = \App\Models\CampusSeason::orderBy('academic_year', 'desc')->get();
        return view('campus.courses.import', compact('seasons'));
    }

    /**
     * Procesar la importación del CSV
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
            'season_id' => 'nullable|exists:campus_seasons,id',
        ], [
            'csv_file.required' => 'Has de seleccionar un fitxer CSV',
            'csv_file.mimes' => 'El fitxer ha de ser de tipus CSV',
            'csv_file.max' => 'El fitxer no pot ser més gran de 10MB',
            'season_id.exists' => 'La temporada seleccionada no és vàlida',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validació',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('csv_file');
            $seasonId = $request->get('season_id');
            $confirmResponsibility = $request->get('confirm_responsibility', false);

            // Omitir validación previa y proceder directamente
            $result = $this->importService->importFromCSV($file, $seasonId, true);

            if ($result['success']) {
                $message = __('campus.import_success') . " " .
                         "{$result['resum']['teachers_creats']} " . __('campus.Professors creats') . ", " .
                         "{$result['resum']['courses_creats']} " . __('campus.Cursos creats') . ".";

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'resum' => $result['resum'],
                    'incidencies' => $result['incidencies']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('campus.import_error'),
                    'incidencies' => $result['incidencies']
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error durant la importació: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar archivo CSV
     */
    public function validateCSV(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ], [
            'csv_file.required' => 'Has de seleccionar un fitxer CSV',
            'csv_file.mimes' => 'El fitxer ha de ser de tipus CSV',
            'csv_file.max' => 'El fitxer no pot ser més gran de 10MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validació',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('csv_file');
            $validation = $this->importService->validateCSV($file);

            if ($validation['valid']) {
                $message = $validation['has_critical_issues'] 
                    ? __('campus.import_validation_warning') 
                    : __('campus.import_validate_success');

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'has_issues' => $validation['has_critical_issues'],
                    'warnings' => $validation['warnings']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $validation['message']
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error durant la validació: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descargar plantilla de importación
     */
    public function downloadTemplate()
    {
        $template = "category,code,title,slug,description,credits,hours,max_students,price,level,schedule_days,schedule_times,start_date,end_date,requirements,objectives,professor,location,calendar_dates,registration_price,format\n" .
                    "Salut i Infermeria,SAN101,PEDIATRIA,pediatria,\"Curs de pediatria per a professionals de la salut\",4,30,25,20.00,intermediate,Dilluns,10:00-11:30,2026-02-16,2026-03-16,\"Titol d'infermeria o medicina\",\"Actualització de coneixements en pediatria\",\"Anna Estapé\",\"CTUG. ROCA UMBERT\",\"16/2, 23/2, 2/3, 9/3, 16/3\",20.00,Presencial\n" .
                    "Tecnologia,TECH201,WEB DEVELOPMENT,web-development,\"Curs de desenvolupament web\",6,40,20,150.00,intermediate,Dimecres,18:00-20:00,2026-02-17,2026-04-21,\"Coneixements bàsics d'informàtica\",\"Aprenent a desenvolupar pàgines web\",\"Marc Garcia\",\"Aula Informàtica\",\"17/2, 24/2, 3/3, 10/3, 17/3, 24/3, 31/3, 7/4, 14/4, 21/4\",150.00,Presencial\n";

        $filename = "plantilla_importacio_cursos_" . date('Y-m-d') . ".csv";

        return response($template)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
