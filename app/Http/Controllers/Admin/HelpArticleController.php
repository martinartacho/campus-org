<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HelpArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = HelpArticle::with(['createdBy', 'updatedBy']);
        
        // Filtre per estat
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        
        // Filtre per àrea
        if ($request->filled('area')) {
            $query->byArea($request->area);
        }
        
        // Cerca
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        $articles = $query->orderBy('order')->orderBy('title')->paginate(15);
        
        return view('campus.help.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = HelpCategory::active()->orderBy('order')->get();
        $areas = [
            'cursos' => 'Cursos',
            'matricula' => 'Matrícula',
            'materials' => 'Materials',
            'configuracio' => 'Configuració',
            'super-admin' => 'Super Admin',
            'admin' => 'Admin',
            'director' => 'Director',
            'manager' => 'Manager',
            'comunicacio' => 'Comunicació',
            'coordinacio' => 'Coordinació',
            'secretaria' => 'Secretaria',
            'gestio' => 'Gestió',
            'treasury' => 'Treasury',
            'editor' => 'Editor',
            'teacher' => 'Professor/a',
            'student' => 'Estudiant',
            'user' => 'Usuari',
        ];
        $statuses = [
            'draft' => 'Borrador',
            'validated' => 'Validado',
            'obsolete' => 'Obsoleto'
        ];
        
        return view('campus.help.articles.create', compact('categories', 'areas', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'area' => ['required', Rule::in(['cursos', 'matricula', 'materials', 'configuracio', 'super-admin', 'admin', 'director', 'manager', 'comunicacio', 'coordinacio', 'secretaria', 'gestio', 'treasury', 'editor', 'teacher', 'student', 'user'])],
            'context' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:100',
            'status' => ['required', Rule::in(['draft', 'validated', 'obsolete'])],
            'order' => 'nullable|integer|min:0',
            'help_category_id' => 'nullable|exists:help_categories,id'
        ]);
        
        // Asignar valores por defecto si son nulos
        $validated['type'] = $validated['type'] ?? null;
        $validated['order'] = $validated['order'] ?? 0;
        $validated['help_category_id'] = $validated['help_category_id'] ?? null;
        
        $validated['slug'] = Str::slug($validated['title']);
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();
        
        // Assegurar slug únic
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (HelpArticle::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        $article = HelpArticle::create($validated);
        
        return redirect()
            ->route('campus.help.articles.index')
            ->with('success', 'Article d\'ajuda creat correctament.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HelpArticle $helpArticle)
    {
        $helpArticle->load(['createdBy', 'updatedBy', 'category']);
        return view('campus.help.articles.show', compact('helpArticle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HelpArticle $helpArticle)
    {
        $categories = HelpCategory::active()->orderBy('order')->get();
        $areas = [
            'cursos' => 'Cursos',
            'matricula' => 'Matrícula',
            'materials' => 'Materials',
            'configuracio' => 'Configuració',
            'super-admin' => 'Super Admin',
            'admin' => 'Admin',
            'director' => 'Director',
            'manager' => 'Manager',
            'comunicacio' => 'Comunicació',
            'coordinacio' => 'Coordinació',
            'secretaria' => 'Secretaria',
            'gestio' => 'Gestió',
            'treasury' => 'Treasury',
            'editor' => 'Editor',
            'teacher' => 'Professor/a',
            'student' => 'Estudiant',
            'user' => 'Usuari',
        ];
        $statuses = [
            'draft' => 'Borrador',
            'validated' => 'Validado',
            'obsolete' => 'Obsoleto'
        ];
        
        return view('campus.help.articles.edit', compact('helpArticle', 'categories', 'areas', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HelpArticle $helpArticle)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'area' => ['required', Rule::in(['cursos', 'matricula', 'materials', 'configuracio', 'super-admin', 'admin', 'director', 'manager', 'comunicacio', 'coordinacio', 'secretaria', 'gestio', 'treasury', 'editor', 'teacher', 'student', 'user'])],
            'context' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:100',
            'status' => ['required', Rule::in(['draft', 'validated', 'obsolete'])],
            'order' => 'nullable|integer|min:0',
            'help_category_id' => 'nullable|exists:help_categories,id'
        ]);
        
        $validated['slug'] = Str::slug($validated['title']);
        $validated['updated_by'] = auth()->id();
        
        // Assegurar slug únic (excepte l'actual)
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (HelpArticle::where('slug', $validated['slug'])->where('id', '!=', $helpArticle->id)->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        $helpArticle->update($validated);
        
        return redirect()
            ->route('campus.help.articles.index')
            ->with('success', 'Article d\'ajuda actualitzat correctament.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HelpArticle $helpArticle)
    {
        $helpArticle->delete();
        
        return redirect()
            ->route('campus.help.articles.index')
            ->with('success', 'Article d\'ajuda eliminat correctament.');
    }
    
    /**
     * Toggle article status
     */
    public function toggleStatus(HelpArticle $helpArticle)
    {
        $newStatus = $helpArticle->status === 'validated' ? 'draft' : 'validated';
        $helpArticle->update([
            'status' => $newStatus,
            'updated_by' => auth()->id()
        ]);
        
        return back()->with('success', 'Estat de l\'article actualitzat correctament.');
    }
}
