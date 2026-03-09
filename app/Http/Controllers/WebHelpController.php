<?php

namespace App\Http\Controllers;

use App\Models\HelpArticle;
use Illuminate\Http\Request;

class WebHelpController extends Controller
{
    /**
     * Mostrar un article d'ajuda específic
     */
    public function show(string $slug)
    {
        $article = HelpArticle::validated()
            ->where('slug', $slug)
            ->firstOrFail();
        
        // Obtenir articles relacionats de la mateixa àrea
        $relatedArticles = HelpArticle::validated()
            ->byArea($article->area)
            ->where('id', '!=', $article->id)
            ->orderBy('order')
            ->orderBy('title')
            ->limit(4)
            ->get();
        
        return view('help.article', compact('article', 'relatedArticles'));
    }
    
    /**
     * Pàgina principal d'ajuda
     */
    public function index(Request $request)
    {
        $areas = [
            'cursos' => [
                'name' => 'Cursos',
                'icon' => 'bi-book',
                'description' => 'Ajuda sobre gestió de cursos',
                'articles' => HelpArticle::validated()->byArea('cursos')->orderBy('order')->orderBy('title')->get()
            ],
            'matricula' => [
                'name' => 'Matrícula',
                'icon' => 'bi-person-plus',
                'description' => 'Ajuda sobre matriculació',
                'articles' => HelpArticle::validated()->byArea('matricula')->orderBy('order')->orderBy('title')->get()
            ],
            'materials' => [
                'name' => 'Materials',
                'icon' => 'bi-folder',
                'description' => 'Ajuda sobre materials didàctics',
                'articles' => HelpArticle::validated()->byArea('materials')->orderBy('order')->orderBy('title')->get()
            ],
            'configuracio' => [
                'name' => 'Configuració',
                'icon' => 'bi-gear',
                'description' => 'Ajuda sobre configuració del sistema',
                'articles' => HelpArticle::validated()->byArea('configuracio')->orderBy('order')->orderBy('title')->get()
            ],
            'super-admin' => [
                'name' => 'Super Admin',
                'icon' => 'bi-shield-fill',
                'description' => 'Ajuda sobre el rol Super Admin',
                'articles' => HelpArticle::validated()->byArea('super-admin')->orderBy('order')->orderBy('title')->get()
            ],
            'admin' => [
                'name' => 'Admin',
                'icon' => 'bi-person-badge-fill',
                'description' => 'Ajuda sobre el rol Admin',
                'articles' => HelpArticle::validated()->byArea('admin')->orderBy('order')->orderBy('title')->get()
            ],
            'director' => [
                'name' => 'Director',
                'icon' => 'bi-mortarboard-fill',
                'description' => 'Ajuda sobre el rol Director',
                'articles' => HelpArticle::validated()->byArea('director')->orderBy('order')->orderBy('title')->get()
            ],
            'manager' => [
                'name' => 'Manager',
                'icon' => 'bi-people-fill',
                'description' => 'Ajuda sobre el rol Manager',
                'articles' => HelpArticle::validated()->byArea('manager')->orderBy('order')->orderBy('title')->get()
            ],
            'comunicacio' => [
                'name' => 'Comunicació',
                'icon' => 'bi-chat-dots-fill',
                'description' => 'Ajuda sobre el rol Comunicació',
                'articles' => HelpArticle::validated()->byArea('comunicacio')->orderBy('order')->orderBy('title')->get()
            ],
            'coordinacio' => [
                'name' => 'Coordinació',
                'icon' => 'bi-diagram-3-fill',
                'description' => 'Ajuda sobre el rol Coordinació',
                'articles' => HelpArticle::validated()->byArea('coordinacio')->orderBy('order')->orderBy('title')->get()
            ],
            'secretaria' => [
                'name' => 'Secretaria',
                'icon' => 'bi-building',
                'description' => 'Ajuda sobre el rol Secretaria',
                'articles' => HelpArticle::validated()->byArea('secretaria')->orderBy('order')->orderBy('title')->get()
            ],
            'gestio' => [
                'name' => 'Gestió',
                'icon' => 'bi-gear-fill',
                'description' => 'Ajuda sobre el rol Gestió',
                'articles' => HelpArticle::validated()->byArea('gestio')->orderBy('order')->orderBy('title')->get()
            ],
            'treasury' => [
                'name' => 'Treasury',
                'icon' => 'bi-cash-stack',
                'description' => 'Ajuda sobre el rol Treasury',
                'articles' => HelpArticle::validated()->byArea('treasury')->orderBy('order')->orderBy('title')->get()
            ],
            'editor' => [
                'name' => 'Editor',
                'icon' => 'bi-pencil-square',
                'description' => 'Ajuda sobre el rol Editor',
                'articles' => HelpArticle::validated()->byArea('editor')->orderBy('order')->orderBy('title')->get()
            ],
            'teacher' => [
                'name' => 'Professor/a',
                'icon' => 'bi-person-video3',
                'description' => 'Ajuda sobre el rol Professor/a',
                'articles' => HelpArticle::validated()->byArea('teacher')->orderBy('order')->orderBy('title')->get()
            ],
            'student' => [
                'name' => 'Estudiant',
                'icon' => 'bi-mortarboard',
                'description' => 'Ajuda sobre el rol Estudiant',
                'articles' => HelpArticle::validated()->byArea('student')->orderBy('order')->orderBy('title')->get()
            ],
            'user' => [
                'name' => 'Usuari',
                'icon' => 'bi-person',
                'description' => 'Ajuda sobre el rol Usuari',
                'articles' => HelpArticle::validated()->byArea('user')->orderBy('order')->orderBy('title')->get()
            ],
        ];
        
        // Filtrar articles segons els paràmetres GET
        $filteredArticles = HelpArticle::validated()
            ->when($request->get('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($request->get('area'), function ($query, $area) {
                // Suport per a ambdós idiomes: castellà i català
                $areaMapping = [
                    'configuracion' => 'configuracio',
                    'materiales' => 'materials',
                    'cursos' => 'cursos',
                    'matricula' => 'matricula',
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
                $mappedArea = $areaMapping[$area] ?? $area;
                $query->byArea($mappedArea);
            })
            ->orderBy('order')
            ->orderBy('title')
            ->get();
        
        return view('help.index', compact('areas', 'filteredArticles'));
    }
    
    /**
     * Guardar feedback d'article
     */
    public function feedback(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:helpful,not_helpful,issue',
            'text' => 'nullable|string|max:1000',
            'article' => 'required|integer|exists:help_articles,id'
        ]);
        
        // Guardar a base de dades
        \App\Models\Feedback::create([
            'user_id' => auth()->id(),
            'email' => auth()->check() ? null : request()->ip(), // O null si no hi ha email
            'type' => 'help_' . $validated['type'], // helpful, not_helpful, issue
            'message' => $validated['text'] ?? 'Feedback sense comentari',
            'status' => 'pending'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Feedback rebut correctament'
        ]);
    }
}
