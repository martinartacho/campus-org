<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Http\Request;

class HelpDashboardController extends Controller
{
    /**
     * Display the help dashboard.
     */
    public function index(Request $request)
    {
        // Estadístiques generals
        $stats = [
            'total_articles' => HelpArticle::count(),
            'published_articles' => HelpArticle::byStatus('validated')->count(),
            'draft_articles' => HelpArticle::byStatus('draft')->count(),
            'obsolete_articles' => HelpArticle::byStatus('obsolete')->count(),
            'total_categories' => HelpCategory::count(),
            'active_categories' => HelpCategory::active()->count(),
        ];
        
        // Articles recents
        $recentArticles = HelpArticle::with(['createdBy', 'category'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
        
        // Articles per àrea
        $articlesByArea = [
            'cursos' => HelpArticle::byArea('cursos')->count(),
            'matricula' => HelpArticle::byArea('matricula')->count(),
            'materiales' => HelpArticle::byArea('materiales')->count(),
            'configuracion' => HelpArticle::byArea('configuracion')->count(),
        ];
        
        // Categories amb més articles
        $categoriesWithArticles = HelpCategory::withCount(['articles' => function($query) {
                $query->where('status', 'validated');
            }])
            ->orderBy('articles_count', 'desc')
            ->limit(5)
            ->get();
        
        // Activitat recent (últims 7 dies)
        $recentActivity = HelpArticle::where('updated_at', '>=', now()->subDays(7))
            ->with(['createdBy', 'updatedBy'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.help.dashboard', compact(
            'stats', 
            'recentArticles', 
            'articlesByArea', 
            'categoriesWithArticles',
            'recentActivity'
        ));
    }
}
