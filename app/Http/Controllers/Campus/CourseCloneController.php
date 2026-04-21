<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Models\CampusCourse;
use App\Models\CampusSeason;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CourseCloneController extends Controller
{
    /**
     * Mostrar formulario de clonación de cursos
     */
    public function create(Request $request)
    {
        $seasons = CampusSeason::orderBy('season_start', 'desc')->get();
        $currentSeason = CampusSeason::where('is_current', true)->first();
        
        return view('campus.courses.clone', compact('seasons', 'currentSeason'));
    }
    
    /**
     * Obtener cursos disponibles para clonar
     */
    public function getCourses(Request $request)
    {
        $seasonId = $request->get('season_id');
        
        if (!$seasonId) {
            return response()->json(['success' => false, 'message' => 'Temporada no especificada']);
        }
        
        // Obtener cursos padres (sin parent_id) de la temporada seleccionada
        $courses = CampusCourse::where('season_id', $seasonId)
            ->whereNull('parent_id')  // Solo cursos padres
            ->where('is_active', true)
            ->with(['category', 'teachers'])
            ->get(['id', 'title', 'code', 'slug', 'price', 'max_students', 'hours', 'level', 'category_id']);
        
        return response()->json([
            'success' => true,
            'courses' => $courses->map(function($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'code' => $course->code,
                    'price' => $course->price,
                    'max_students' => $course->max_students,
                    'hours' => $course->hours,
                    'level' => $course->level,
                    'category' => $course->category ? $course->category->name : 'Sin categoría',
                    'teachers' => $course->teachers->pluck('full_name')->implode(', '),
                ];
            })
        ]);
    }
    
    /**
     * Clonar cursos seleccionados a nueva temporada
     */
    public function clone(Request $request)
    {
        $validated = $request->validate([
            'source_season_id' => 'required|exists:campus_seasons,id',
            'target_season_id' => 'required|exists:campus_seasons,id|different:source_season_id',
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'exists:campus_courses,id',
            'activate_cloned' => 'boolean',
            'make_public' => 'boolean',
        ]);
        
        $sourceSeasonId = $validated['source_season_id'];
        $targetSeasonId = $validated['target_season_id'];
        $courseIds = $validated['course_ids'];
        $activateCloned = $validated['activate_cloned'] ?? false;
        $makePublic = $validated['make_public'] ?? false;
        
        $targetSeason = CampusSeason::find($targetSeasonId);
        $clonedCourses = [];
        $errors = [];
        
        DB::beginTransaction();
        
        try {
            foreach ($courseIds as $courseId) {
                $originalCourse = CampusCourse::find($courseId);
                
                if (!$originalCourse) {
                    $errors[] = "Curso ID {$courseId} no encontrado";
                    continue;
                }
                
                // Verificar si ya existe un curso con el mismo slug en la temporada destino
                $existingSlug = $this->generateUniqueSlug($originalCourse->title, $targetSeasonId);
                $existingCourse = CampusCourse::where('slug', $existingSlug)
                    ->where('season_id', $targetSeasonId)
                    ->first();
                
                if ($existingCourse) {
                    $errors[] = "El curso '{$originalCourse->title}' ya existe en la temporada destino";
                    continue;
                }
                
                try {
                    // Clonar el curso
                    $clonedCourse = $this->cloneCourse($originalCourse, $targetSeason, $activateCloned, $makePublic);
                    $clonedCourses[] = $clonedCourse;
                    
                    // Clonar relaciones (profesores)
                    $this->cloneTeacherRelations($originalCourse, $clonedCourse);
                    
                } catch (\Exception $e) {
                    $errors[] = "Error clonando curso '{$originalCourse->title}': " . $e->getMessage();
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => count($errors) === 0,
                'message' => count($clonedCourses) . ' cursos clonados correctamente',
                'cloned_count' => count($clonedCourses),
                'error_count' => count($errors),
                'errors' => $errors,
                'cloned_courses' => $clonedCourses
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error durante la clonación: ' . $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
    
    /**
     * Generar slug único por temporada
     */
    private function generateUniqueSlug($title, $seasonId)
    {
        $baseSlug = Str::slug($title);
        $season = CampusSeason::find($seasonId);
        $seasonYear = $season ? substr($season->season_start->format('Y'), -2) : date('y');
        $uniqueSlug = "{$baseSlug}-{$seasonYear}";
        
        // Si existe, añadir sufijo numérico
        $counter = 1;
        while (CampusCourse::where('slug', $uniqueSlug)->where('season_id', $seasonId)->exists()) {
            $uniqueSlug = "{$baseSlug}-{$seasonYear}-{$counter}";
            $counter++;
        }
        
        return $uniqueSlug;
    }
    
    /**
     * Generar código único por temporada
     */
    private function generateUniqueCode($title, $seasonId)
    {
        // 1. Normalizar texto
        $normalized = Str::ascii($title);
        $normalized = strtoupper($normalized);
        $normalized = preg_replace('/[^A-Z\s]/', '', $normalized);
        
        // 2. Separar palabras
        $words = array_values(array_filter(explode(' ', $normalized)));
        $count = count($words);
        
        $base = '';
        
        if ($count == 1) {
            $base = substr($words[0], 0, 6);
        } elseif ($count == 2) {
            $base = substr($words[0], 0, 3) . substr($words[1], 0, 3);
        } elseif ($count == 3) {
            foreach ($words as $w) {
                $base .= substr($w, 0, 2);
            }
        } elseif ($count == 4) {
            $base = substr($words[0], 0, 3) . substr($words[1], 0, 1) . substr($words[2], 0, 1) . substr($words[3], 0, 1);
        } else {
            foreach ($words as $w) {
                $base .= substr($w, 0, 1);
                if (strlen($base) >= 6) break;
            }
        }
        
        // Asegurar 6 caracteres
        $base = substr(str_pad($base, 6, 'X'), 0, 6);
        
        // 3. Generar número incremental por temporada
        $counter = 1;
        do {
            $code = $base . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $exists = CampusCourse::where('code', $code)->where('season_id', $seasonId)->exists();
            $counter++;
        } while ($exists);
        
        return $code;
    }
    
    /**
     * Clonar un curso específico
     */
    private function cloneCourse($originalCourse, $targetSeason, $activate = false, $makePublic = false)
    {
        $clonedCourse = $originalCourse->replicate([
            // Excluir campos que no se deben replicar
            'id', 'slug', 'code', 'parent_id', 'created_at', 'updated_at'
        ]);
        
        // Actualizar campos para el curso clonado
        $clonedCourse->season_id = $targetSeason->id;
        $clonedCourse->parent_id = $originalCourse->id; // Referencia al curso original
        $clonedCourse->slug = $this->generateUniqueSlug($originalCourse->title, $targetSeason->id);
        $clonedCourse->code = $this->generateUniqueCode($originalCourse->title, $targetSeason->id);
        $clonedCourse->is_active = $activate;
        $clonedCourse->is_public = $makePublic;
        $clonedCourse->status = 'draft'; // Estado inicial para revisión
        
        // Ajustar fechas a la temporada destino
        $clonedCourse->start_date = $targetSeason->season_start;
        $clonedCourse->end_date = $targetSeason->season_end;
        
        // Añadir metadata sobre clonación
        $metadata = $originalCourse->metadata ?? [];
        $metadata['cloned_from'] = [
            'original_course_id' => $originalCourse->id,
            'original_season_id' => $originalCourse->season_id,
            'cloned_at' => now()->toISOString(),
            'cloned_by' => auth()->user()->id ?? null
        ];
        $clonedCourse->metadata = $metadata;
        
        $clonedCourse->save();
        
        return $clonedCourse;
    }
    
    /**
     * Clonar relaciones profesor-curso
     */
    private function cloneTeacherRelations($originalCourse, $clonedCourse)
    {
        $teacherRelations = $originalCourse->teachers()->get();
        
        foreach ($teacherRelations as $teacher) {
            $clonedCourse->teachers()->attach($teacher->id, [
                'hours_assigned' => $teacher->pivot->hours_assigned ?? null,
                'role' => $teacher->pivot->role ?? 'teacher',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
