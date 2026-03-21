<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use Illuminate\Http\Request;

class DocumentCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = DocumentCategory::with(['parent', 'children' => function($query) {
            $query->active()->orderBy('sort_order');
        }])
        ->withCount('documents')
        ->active()
        ->orderBy('sort_order')
        ->get();

        return view('documents.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = DocumentCategory::active()
            ->orderBy('sort_order')
            ->pluck('name', 'id');

        return view('documents.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:document_categories,id',
            'sort_order' => 'integer|min:0',
            'access_roles' => 'nullable|array',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);
        $validated['is_active'] = true;

        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $counter = 1;

        while (DocumentCategory::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        DocumentCategory::create($validated);

        return redirect()
            ->route('campus.documents.categories.index')
            ->with('success', 'Categoría creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentCategory $category)
    {
        $category->load(['parent', 'children' => function($query) {
            $query->active()->orderBy('sort_order');
        }]);

        return view('documents.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentCategory $category)
    {
        $categories = DocumentCategory::where('id', '!=', $category->id)
            ->active()
            ->orderBy('sort_order')
            ->pluck('name', 'id');

        return view('documents.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:document_categories,id',
            'sort_order' => 'integer|min:0',
            'access_roles' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        // Update slug if name changed
        if ($validated['name'] !== $category->name) {
            $slug = \Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;

            while (DocumentCategory::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $validated['slug'] = $slug;
        }

        // Prevent circular reference
        if ($validated['parent_id'] == $category->id) {
            unset($validated['parent_id']);
        }

        $category->update($validated);

        return redirect()
            ->route('campus.documents.categories.show', $category)
            ->with('success', 'Categoría actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentCategory $category)
    {
        // Check if category has documents
        if ($category->documents()->exists()) {
            return redirect()
                ->route('campus.documents.categories.index')
                ->with('error', 'No se puede eliminar una categoría que contiene documentos');
        }

        // Check if category has children
        if ($category->children()->exists()) {
            return redirect()
                ->route('campus.documents.categories.index')
                ->with('error', 'No se puede eliminar una categoría que tiene subcategorías');
        }

        $category->delete();

        return redirect()
            ->route('campus.documents.categories.index')
            ->with('success', 'Categoría eliminada correctamente');
    }

    /**
     * Toggle category active status.
     */
    public function toggle(DocumentCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        return redirect()
            ->route('campus.documents.categories.index')
            ->with('success', 'Estado de la categoría actualizado correctamente');
    }
}
