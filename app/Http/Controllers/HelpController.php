<?php

namespace App\Http\Controllers;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HelpController extends Controller
{
    /**
     * Obtindre ajuda contextual segons la pàgina actual
     */
    public function contextual(Request $request): JsonResponse
    {
        $currentPath = $request->get('current_path', '');
        $area = $this->getAreaFromPath($currentPath);
        
        // Obtenir categories de l'àrea actual
        $categories = HelpCategory::active()
            ->byArea($area)
            ->ordered()
            ->get();
        
        // Obtenir articles rellevants per al context actual
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
     * Obtenir tots els articles d'una àrea
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
     * Buscar articles d'ajuda
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
     * Obtenir un article específic
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
     * Obtenir totes les àrees disponibles
     */
    public function areas(): JsonResponse
    {
        $areas = [
            'cursos' => [
                'name' => 'Cursos',
                'icon' => 'bi-book',
                'description' => 'Ajuda sobre gestió de cursos',
            ],
            'matricula' => [
                'name' => 'Matrícula',
                'icon' => 'bi-person-plus',
                'description' => 'Ajuda sobre matriculació',
            ],
            'materials' => [
                'name' => 'Materials',
                'icon' => 'bi-folder',
                'description' => 'Ajuda sobre materials didàctics',
            ],
            'configuracio' => [
                'name' => 'Configuració',
                'icon' => 'bi-gear',
                'description' => 'Ajuda sobre configuració del sistema',
            ],
            'super-admin' => [
                'name' => 'Super Admin',
                'icon' => 'bi-shield-fill',
                'description' => 'Ajuda sobre el rol Super Admin',
            ],
            'admin' => [
                'name' => 'Admin',
                'icon' => 'bi-person-badge-fill',
                'description' => 'Ajuda sobre el rol Admin',
            ],
            'director' => [
                'name' => 'Director',
                'icon' => 'bi-mortarboard-fill',
                'description' => 'Ajuda sobre el rol Director',
            ],
            'manager' => [
                'name' => 'Manager',
                'icon' => 'bi-people-fill',
                'description' => 'Ajuda sobre el rol Manager',
            ],
            'comunicacio' => [
                'name' => 'Comunicació',
                'icon' => 'bi-chat-dots-fill',
                'description' => 'Ajuda sobre el rol Comunicació',
            ],
            'coordinacio' => [
                'name' => 'Coordinació',
                'icon' => 'bi-diagram-3-fill',
                'description' => 'Ajuda sobre el rol Coordinació',
            ],
            'secretaria' => [
                'name' => 'Secretaria',
                'icon' => 'bi-building',
                'description' => 'Ajuda sobre el rol Secretaria',
            ],
            'gestio' => [
                'name' => 'Gestió',
                'icon' => 'bi-gear-fill',
                'description' => 'Ajuda sobre el rol Gestió',
            ],
            'treasury' => [
                'name' => 'Treasury',
                'icon' => 'bi-cash-stack',
                'description' => 'Ajuda sobre el rol Treasury',
            ],
            'editor' => [
                'name' => 'Editor',
                'icon' => 'bi-pencil-square',
                'description' => 'Ajuda sobre el rol Editor',
            ],
            'teacher' => [
                'name' => 'Professor/a',
                'icon' => 'bi-person-video3',
                'description' => 'Ajuda sobre el rol Professor/a',
            ],
            'student' => [
                'name' => 'Estudiant',
                'icon' => 'bi-mortarboard',
                'description' => 'Ajuda sobre el rol Estudiant',
            ],
            'user' => [
                'name' => 'Usuari',
                'icon' => 'bi-person',
                'description' => 'Ajuda sobre el rol Usuari',
            ],
        ];
        
        return response()->json(['areas' => $areas]);
    }
    
    /**
     * Determinar l'àrea a partir de la ruta actual
     */
    private function getAreaFromPath(string $path): string
    {
        $areaMapping = [
            'courses' => 'cursos',
            'cursos' => 'cursos',
            'registration' => 'matricula',
            'matricula' => 'matricula',
            'registrations' => 'matricula',
            'materials' => 'materials',
            'settings' => 'configuracio',
            'configuracio' => 'configuracio',
            'profile' => 'configuracio',
            'perfil' => 'configuracio',
            'roles' => 'super-admin',
            'super-admin' => 'super-admin',
            'superadmin' => 'super-admin',
            'admin' => 'admin',
            'administrator' => 'admin',
            'director' => 'director',
            'manager' => 'manager',
            'comunicacion' => 'comunicacio',
            'comunicacio' => 'comunicacio',
            'coordinacion' => 'coordinacio',
            'coordinacio' => 'coordinacio',
            'secretaria' => 'secretaria',
            'gestion' => 'gestio',
            'gestio' => 'gestio',
            'treasury' => 'treasury',
            'tesoreria' => 'treasury',
            'editor' => 'editor',
            'teacher' => 'teacher',
            'professor' => 'teacher',
            'student' => 'student',
            'estudiante' => 'student',
            'user' => 'user',
            'usuario' => 'user',
            'usuaris' => 'user',
        ];
        
        foreach ($areaMapping as $keyword => $area) {
            if (strpos($path, $keyword) !== false) {
                return $area;
            }
        }
        
        return 'cursos'; // Per defecte
    }
}
