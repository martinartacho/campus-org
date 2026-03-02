<?php

namespace App\Http\Controllers;

use App\Models\HelpArticle;
use Illuminate\Http\Request;

class WebHelpController extends Controller
{
    /**
     * Mostrar un artículo de ayuda específico
     */
    public function show(string $slug)
    {
        $article = HelpArticle::validated()
            ->where('slug', $slug)
            ->firstOrFail();
        
        // Obtener artículos relacionados del mismo área
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
     * Página principal de ayuda
     */
    public function index(Request $request)
    {
        $areas = [
            'cursos' => [
                'name' => 'Cursos',
                'icon' => 'bi-book',
                'description' => 'Ayuda sobre gestión de cursos',
                'articles' => HelpArticle::validated()->byArea('cursos')->orderBy('order')->orderBy('title')->get()
            ],
            'matricula' => [
                'name' => 'Matrícula',
                'icon' => 'bi-person-plus',
                'description' => 'Ayuda sobre matriculación',
                'articles' => HelpArticle::validated()->byArea('matricula')->orderBy('order')->orderBy('title')->get()
            ],
            'materiales' => [
                'name' => 'Materiales',
                'icon' => 'bi-folder',
                'description' => 'Ayuda sobre materiales didácticos',
                'articles' => HelpArticle::validated()->byArea('materiales')->orderBy('order')->orderBy('title')->get()
            ],
            'configuracion' => [
                'name' => 'Configuración',
                'icon' => 'bi-gear',
                'description' => 'Ayuda sobre configuración del sistema',
                'articles' => HelpArticle::validated()->byArea('configuracion')->orderBy('order')->orderBy('title')->get()
            ],
        ];
        
        // Filtrar artículos según los parámetros GET
        $filteredArticles = HelpArticle::validated()
            ->when($request->get('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($request->get('area'), function ($query, $area) {
                $query->byArea($area);
            })
            ->orderBy('order')
            ->orderBy('title')
            ->get();
        
        return view('help.index', compact('areas', 'filteredArticles'));
    }
    
    /**
     * Guardar feedback de artículo
     */
    public function feedback(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:helpful,not_helpful,issue',
            'text' => 'nullable|string|max:1000',
            'article' => 'required|integer|exists:help_articles,id'
        ]);
        
        // Guardar en base de datos
        \App\Models\Feedback::create([
            'user_id' => auth()->id(),
            'email' => auth()->check() ? null : request()->ip(), // O null si no hay email
            'type' => 'help_' . $validated['type'], // helpful, not_helpful, issue
            'message' => $validated['text'] ?? 'Feedback sin comentario',
            'status' => 'pending'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Feedback recibido correctamente'
        ]);
    }
}
