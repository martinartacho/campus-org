<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Models\CampusSeason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeasonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener temporadas con conteo de cursos
        $seasons = CampusSeason::withCount('courses')
            ->orderBy('season_start', 'desc')
            ->paginate(10);
        
        return view('campus.seasons.index', compact('seasons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('campus.seasons.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'season_start' => 'required|date',
            'season_end' => 'required|date|after_or_equal:season_start',
            'is_active' => 'boolean',
            'is_current' => 'boolean',
        ]);

        // Si se marca como actual, desmarcar las demás
        if ($request->has('is_current') && $request->is_current) {
            CampusSeason::where('is_current', true)->update(['is_current' => false]);
        }

        // Si se marca como activa, pero no hay fecha de inicio futura
        if ($request->has('is_active') && $request->is_active) {
            // Validar que no se solapen fechas con otras temporadas activas
            $overlapping = CampusSeason::where('is_active', true)
                ->where(function($query) use ($request) {
                    $query->whereBetween('season_start', [$request->season_start, $request->season_end])
                          ->orWhereBetween('season_end', [$request->season_start, $request->season_end])
                          ->orWhere(function($q) use ($request) {
                              $q->where('season_start', '<=', $request->season_start)
                                ->where('season_end', '>=', $request->season_end);
                          });
                })
                ->exists();
            
            if ($overlapping) {
                return back()->withErrors([
                    'season_start' => 'Ja existeix una temporada activa en aquestes dates.',
                    'season_end' => 'Les dates es solapen amb una altra temporada activa.'
                ])->withInput();
            }
        }

        CampusSeason::create($validated);

        return redirect()->route('campus.seasons.index')
            ->with('success', 'Temporada creada correctament.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CampusSeason $season)
    {
        // Cargar relaciones para la vista show
        $season->load(['courses' => function($query) {
            $query->withCount('registrations')
                  ->with('category')
                  ->orderBy('title');
        }, 'courses.teachers']);
        
        // Estadísticas básicas
        $stats = [
            'total_courses' => $season->courses_count,
            'active_courses' => $season->courses()->where('is_active', true)->count(),
            'total_registrations' => $season->courses->sum('registrations_count'),
            'unique_students' => DB::table('campus_registrations')
                ->join('campus_courses', 'campus_registrations.course_id', '=', 'campus_courses.id')
                ->where('campus_courses.season_id', $season->id)
                ->distinct('campus_registrations.student_id')
                ->count('campus_registrations.student_id'),
        ];
        
        return view('campus.seasons.show', compact('season', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CampusSeason $season)
    {
        return view('campus.seasons.edit', compact('season'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CampusSeason $season)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'season_start' => 'required|date',
            'season_end' => 'required|date|after_or_equal:season_start',
            'is_active' => 'boolean',
            'is_current' => 'boolean',
        ]);

        // Si se marca como actual, desmarcar las demás
        if ($request->has('is_current') && $request->is_current) {
            CampusSeason::where('id', '!=', $season->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);
        }

        // Si se marca como activa, validar solapamiento (excluyendo esta temporada)
        if ($request->has('is_active') && $request->is_active) {
            $overlapping = CampusSeason::where('is_active', true)
                ->where('id', '!=', $season->id)
                ->where(function($query) use ($request) {
                    $query->whereBetween('season_start', [$request->season_start, $request->season_end])
                          ->orWhereBetween('season_end', [$request->season_start, $request->season_end])
                          ->orWhere(function($q) use ($request) {
                              $q->where('season_start', '<=', $request->season_start)
                                ->where('season_end', '>=', $request->season_end);
                          });
                })
                ->exists();
            
            if ($overlapping) {
                return back()->withErrors([
                    'season_start' => 'Ja existeix una altra temporada activa en aquestes dates.',
                    'season_end' => 'Les dates es solapen amb una altra temporada activa.'
                ])->withInput();
            }
        }

        $season->update($validated);

        return redirect()->route('campus.seasons.index')
            ->with('success', 'Temporada actualitzada correctament.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CampusSeason $season)
    {
        // Verificar si hay cursos asociados
        if ($season->courses()->exists()) {
            return redirect()->route('campus.seasons.index')
                ->with('error', 'No es pot eliminar la temporada perquè té cursos associats.');
        }

        // Verificar si es la temporada actual
        if ($season->is_current) {
            return redirect()->route('campus.seasons.index')
                ->with('error', 'No es pot eliminar la temporada actual. Marca una altra temporada com a actual primer.');
        }

        $season->delete();

        return redirect()->route('campus.seasons.index')
            ->with('success', 'Temporada eliminada correctament.');
    }
    
    /**
     * Marcar como temporada actual.
     */
    public function setAsCurrent(CampusSeason $season)
    {
        // Desmarcar todas las temporadas como actuales
        CampusSeason::where('is_current', true)->update(['is_current' => false]);
        
        // Marcar esta temporada como actual
        $season->update(['is_current' => true]);
        
        return redirect()->route('campus.seasons.index')
            ->with('success', "Temporada '{$season->name}' marcada com a actual.");
    }
    
    /**
     * Activar/desactivar temporada.
     */
    public function toggleActive(CampusSeason $season)
    {
        // Si se va a activar, verificar solapamiento
        if (!$season->is_active) {
            $overlapping = CampusSeason::where('is_active', true)
                ->where('id', '!=', $season->id)
                ->where(function($query) use ($season) {
                    $query->whereBetween('season_start', [$season->season_start, $season->season_end])
                          ->orWhereBetween('season_end', [$season->season_start, $season->season_end])
                          ->orWhere(function($q) use ($season) {
                              $q->where('season_start', '<=', $season->season_start)
                                ->where('season_end', '>=', $season->season_end);
                          });
                })
                ->exists();
            
            if ($overlapping) {
                return redirect()->route('campus.seasons.index')
                    ->with('error', 'No es pot activar la temporada perquè es solapa amb una altra temporada activa.');
            }
        }
        
        $season->update(['is_active' => !$season->is_active]);
        
        $status = $season->is_active ? 'activada' : 'desactivada';
        return redirect()->route('campus.seasons.index')
            ->with('success', "Temporada {$status} correctament.");
    }
}