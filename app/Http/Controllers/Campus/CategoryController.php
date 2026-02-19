<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Models\CampusCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Verificar permisos usando el gate de Laravel
        $this->authorize('viewAny', CampusCategory::class);

        // Obtener categorías con conteo de cursos
        $categories = CampusCategory::withCount('courses')
            ->with('parent')
            ->orderBy('order')
            ->orderBy('name')
            ->paginate(20);

        return view('campus.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', CampusCategory::class);

        // Obtener categorías padre
        $parentCategories = CampusCategory::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('campus.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', CampusCategory::class);

        // Validación
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:campus_categories,name',
            'description' => 'nullable|string',
            'color' => 'required|in:blue,green,red,yellow,purple,pink,indigo,gray,orange,teal',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'parent_id' => 'nullable|exists:campus_categories,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        // Generar slug
        $validated['slug'] = Str::slug($validated['name']);
        
        // Asegurar valores booleanos
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        
        // Valor por defecto para order
        if (empty($validated['order'])) {
            $validated['order'] = CampusCategory::max('order') + 1;
        }

        // Crear categoría
        $category = CampusCategory::create($validated);

        return redirect()->route('campus.categories.show', $category)
            ->with('success', __('Categoria creada correctament.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(CampusCategory $category)
    {
        $this->authorize('view', $category);

        // Cargar categoría con relaciones
        $category->load([
            'parent',
            'children' => function ($query) {
                $query->withCount('courses')->orderBy('order')->orderBy('name');
            },
            'courses' => function ($query) {
                $query->with(['season', 'teachers.user'])
                      ->withCount('registrations')
                      ->orderBy('created_at', 'desc');
            }
        ])->loadCount(['courses', 'children']);

        // Calcular estadísticas
        $stats = [
            'total_courses' => $category->courses_count,
            'active_courses' => $category->courses()->where('is_active', true)->count(),
            'subcategories' => $category->children_count,
        ];

        return view('campus.categories.show', compact('category', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CampusCategory $category)
    {
        $this->authorize('update', $category);

        // Obtener categorías padre (excluyendo la actual y sus hijos)
        $parentCategories = CampusCategory::where('id', '!=', $category->id)
            ->where(function ($query) use ($category) {
                $query->whereNull('parent_id')
                      ->orWhere('parent_id', '!=', $category->id);
            })
            ->orderBy('name')
            ->get();

        return view('campus.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CampusCategory $category)
    {
        $this->authorize('update', $category);

        // Validación
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:campus_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'color' => 'required|in:blue,green,red,yellow,purple,pink,indigo,gray,orange,teal',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'parent_id' => 'nullable|exists:campus_categories,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        // Verificar que no se asigne como padre a sí misma o a un hijo
        if ($validated['parent_id'] == $category->id) {
            return back()->withErrors(['parent_id' => __('No pots assignar la categoria com a pare d\'ella mateixa.')]);
        }

        // Verificar referencias cíclicas
        if ($this->hasCyclicReference($category->id, $validated['parent_id'])) {
            return back()->withErrors(['parent_id' => __('No pots crear una referència cíclica.')]);
        }

        // Asegurar valores booleanos
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        // Actualizar slug si cambió el nombre
        if ($category->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Actualizar categoría
        $category->update($validated);

        return redirect()->route('campus.categories.show', $category)
            ->with('success', __('Categoria actualitzada correctament.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CampusCategory $category)
    {
        $this->authorize('delete', $category);

        // Verificar si tiene cursos asociados
        if ($category->courses()->exists()) {
            return back()->with('error', __('No es pot eliminar una categoria amb cursos associats.'));
        }

        // Mover hijos a categoría padre (si existe) o hacerlos root
        if ($category->children()->exists()) {
            $children = $category->children;
            foreach ($children as $child) {
                $child->update(['parent_id' => $category->parent_id]);
            }
        }

        // Eliminar categoría
        $category->delete();

        return redirect()->route('campus.categories.index')
            ->with('success', __('Categoria eliminada correctament.'));
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(CampusCategory $category)
    {
        $this->authorize('update', $category);
        
        $category->update(['is_active' => !$category->is_active]);

        $message = $category->is_active 
            ? __('Categoria activada correctament.')
            : __('Categoria desactivada correctament.');

        return back()->with('success', $message);
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(CampusCategory $category)
    {
        $this->authorize('update', $category);
        
        $category->update(['is_featured' => !$category->is_featured]);

        $message = $category->is_featured 
            ? __('Categoria marcada com a destacada.')
            : __('Categoria desmarcada com a destacada.');

        return back()->with('success', $message);
    }

    /**
     * Check for cyclic references in category hierarchy.
     */
    private function hasCyclicReference($categoryId, $parentId)
    {
        if (!$parentId) {
            return false;
        }

        // Si el padre es la misma categoría
        if ($parentId == $categoryId) {
            return true;
        }

        // Verificar si el padre propuesto es un descendiente de esta categoría
        $parentCategory = CampusCategory::find($parentId);
        while ($parentCategory) {
            if ($parentCategory->id == $categoryId) {
                return true;
            }
            $parentCategory = $parentCategory->parent;
        }

        return false;
    }
}