<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HelpCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = HelpCategory::with(['articles' => function($query) {
            $query->validated();
        }])->orderBy('order')->paginate(15);
        
        return view('campus.help.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $areas = [
            'cursos' => 'Cursos',
            'matricula' => 'Matrícula',
            'materiales' => 'Materiales',
            'configuracion' => 'Configuración'
        ];
        
        return view('campus.help.categories.create', compact('areas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'area' => ['required', Rule::in(['cursos', 'matricula', 'materiales', 'configuracion'])],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer|min:0',
            'active' => 'boolean'
        ]);

        $validated['active'] = $request->has('active');
        
        HelpCategory::create($validated);
        
        return redirect()
            ->route('campus.help.categories.index')
            ->with('success', 'Categoria d\'ajuda creada correctament.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HelpCategory $helpCategory)
    {
        $areas = [
            'cursos' => 'Cursos',
            'matricula' => 'Matrícula',
            'materiales' => 'Materiales',
            'configuracion' => 'Configuración'
        ];
        
        return view('campus.help.categories.edit', compact('helpCategory', 'areas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HelpCategory $helpCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'area' => ['required', Rule::in(['cursos', 'matricula', 'materiales', 'configuracion'])],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer|min:0',
            'active' => 'boolean'
        ]);

        $validated['active'] = $request->has('active');
        
        $helpCategory->update($validated);
        
        return redirect()
            ->route('campus.help.categories.index')
            ->with('success', 'Categoria d\'ajuda actualitzada correctament.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HelpCategory $helpCategory)
    {
        $helpCategory->delete();
        
        return redirect()
            ->route('campus.help.categories.index')
            ->with('success', 'Categoria d\'ajuda eliminada correctament.');
    }

    /**
     * Toggle active status of category.
     */
    public function toggleActive(HelpCategory $helpCategory)
    {
        $helpCategory->update(['active' => !$helpCategory->active]);
        
        return redirect()
            ->route('campus.help.categories.index')
            ->with('success', 'Estat de la categoria actualitzat correctament.');
    }
}
