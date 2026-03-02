<?php

namespace App\Http\Controllers;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HelpController extends Controller
{
    /**
     * Obtener ayuda contextual según la página actual
     */
    public function contextual(Request $request): JsonResponse
    {
        $currentPath = $request->get('current_path', '');
        $area = $this->getAreaFromPath($currentPath);
        
        // Obtener categorías del área actual
        $categories = HelpCategory::active()
            ->byArea($area)
            ->ordered()
            ->get();
        
        // Obtener artículos relevantes para el contexto actual
        $articles = HelpArticle::validated()
            ->byArea($area)
            ->when($currentPath, function ($query) use ($currentPath) {
                $query->where('context', $currentPath);
            })
            ->ordered()
            ->get();
        
        return response()->json([
            'current_area' => $area,
            'current_path' => $currentPath,
            'categories' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->getDisplayName(),
                    'icon' => $category->getIconClass(),
                    'order' => $category->order,
                ];
            }),
            'contextual_articles' => $articles->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => $article->getFormattedContent(),
                    'order' => $article->order,
                ];
            }),
        ]);
    }
    
    /**
     * Obtener todos los artículos de un área
     */
    public function byArea(Request $request, string $area): JsonResponse
    {
        $articles = HelpArticle::validated()
            ->byArea($area)
            ->ordered()
            ->get();
        
        return response()->json([
            'area' => $area,
            'articles' => $articles->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => $article->getFormattedContent(),
                    'context' => $article->context,
                    'order' => $article->order,
                ];
            }),
        ]);
    }
    
    /**
     * Buscar artículos de ayuda
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $area = $request->get('area', '');
        
        $articles = HelpArticle::validated()
            ->when($area, function ($query) use ($area) {
                $query->byArea($area);
            })
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->ordered()
            ->limit(20)
            ->get();
        
        return response()->json([
            'query' => $query,
            'area' => $area,
            'articles' => $articles->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => substr($article->content, 0, 150) . '...',
                    'area' => $article->area,
                    'context' => $article->context,
                ];
            }),
        ]);
    }
    
    /**
     * Obtener un artículo específico
     */
    public function show(string $slug): JsonResponse
    {
        $article = HelpArticle::validated()
            ->where('slug', $slug)
            ->firstOrFail();
        
        return response()->json([
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'content' => $article->getFormattedContent(),
                'area' => $article->area,
                'context' => $article->context,
                'type' => $article->type,
                'order' => $article->order,
                'created_at' => $article->created_at->format('d/m/Y H:i'),
            ],
        ]);
    }
    
    /**
     * Obtener todas las áreas disponibles
     */
    public function areas(): JsonResponse
    {
        $areas = [
            'cursos' => [
                'name' => 'Cursos',
                'icon' => 'bi-book',
                'description' => 'Ayuda sobre gestión de cursos',
            ],
            'matricula' => [
                'name' => 'Matrícula',
                'icon' => 'bi-person-plus',
                'description' => 'Ayuda sobre matriculación',
            ],
            'materiales' => [
                'name' => 'Materiales',
                'icon' => 'bi-folder',
                'description' => 'Ayuda sobre materiales didácticos',
            ],
            'configuracion' => [
                'name' => 'Configuración',
                'icon' => 'bi-gear',
                'description' => 'Ayuda sobre configuración del sistema',
            ],
        ];
        
        return response()->json(['areas' => $areas]);
    }
    
    /**
     * Determinar el área a partir de la ruta actual
     */
    private function getAreaFromPath(string $path): string
    {
        $areaMapping = [
            'courses' => 'cursos',
            'cursos' => 'cursos',
            'registration' => 'matricula',
            'matricula' => 'matricula',
            'registrations' => 'matricula',
            'materials' => 'materiales',
            'materiales' => 'materiales',
            'settings' => 'configuracion',
            'configuracion' => 'configuracion',
            'profile' => 'configuracion',
            'perfil' => 'configuracion',
        ];
        
        foreach ($areaMapping as $keyword => $area) {
            if (strpos($path, $keyword) !== false) {
                return $area;
            }
        }
        
        return 'cursos'; // Por defecto
    }
}
