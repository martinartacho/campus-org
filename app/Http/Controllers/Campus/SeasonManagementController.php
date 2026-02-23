<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CampusSeason;

class SeasonManagementController extends Controller
{
    public function index()
    {
        $this->authorize('campus.admin');
        
        $seasons = CampusSeason::orderBy('academic_year', 'desc')
            ->orderBy('name', 'desc')
            ->get();
            
        return view('campus.seasons.index', compact('seasons'));
    }
    
    public function updateStatus(Request $request, CampusSeason $season)
    {
        $this->authorize('campus.admin');
        
        $request->validate([
            'status' => 'required|in:draft,planning,active,registration,in_progress,completed,archived',
            'make_current' => 'boolean'
        ]);
        
        // Actualizar status
        $season->update([
            'status' => $request->status
        ]);
        
        // Si se solicita hacerla actual
        if ($request->boolean('make_current')) {
            $season->setAsCurrent();
        }
        
        return redirect()->route('campus.seasons.index')
            ->with('success', 'Temporada actualizada correctamente');
    }
    
    public function setCurrent(CampusSeason $season)
    {
        $this->authorize('campus.admin');
        
        $season->setAsCurrent();
        
        return redirect()->route('campus.seasons.index')
            ->with('success', 'Temporada establecida como actual');
    }
}
