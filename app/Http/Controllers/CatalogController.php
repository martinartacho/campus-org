<?php

namespace App\Http\Controllers;

use App\Models\CampusCourse;
use App\Models\CampusSeason;
use App\Models\CampusCategory;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class CatalogController extends Controller
{
    /**
     * Display the public course catalog
     */
    public function index(Request $request)
    {
        // Get current or next season for registration
        $season = $this->getRegistrationSeason();
        
        if (!$season) {
            return view('catalog.no-courses')
                ->with('message', 'No hay temporadas disponibles para matriculación en este momento.');
        }

        // Build query for available courses
        $query = CampusCourse::with(['season', 'category', 'teachers'])
            ->where('season_id', $season->id)
            ->where('is_public', true)
            ->where('is_active', true)
            ->whereIn('status', ['planning', 'active', 'registration', 'in_progress']);

        // Apply filters
        $this->applyFilters($query, $request);

        // Get courses with pagination
        $courses = $query->orderBy('start_date', 'asc')
                        ->paginate(12)
                        ->withQueryString();

        // Get filter options
        $categories = CampusCategory::whereHas('courses', function($q) use ($season) {
                $q->where('season_id', $season->id)
                  ->where('is_public', true)
                  ->where('is_active', true);
            })
            ->orderBy('name')
            ->get();

        $levels = ['none', 'beginner', 'intermediate', 'advanced', 'expert'];
        $formats = ['presencial', 'online', 'hybrid'];

        // Get current cart
        $cart = Cart::getCurrent();
        $cartItems = $cart ? $cart->items->pluck('course_id')->toArray() : [];

        return view('catalog.index', compact(
            'courses',
            'season',
            'categories',
            'levels',
            'formats',
            'cartItems'
        ));
    }

    /**
     * Display course details
     */
    public function show(CampusCourse $course, Request $request)
    {
        // Validate course is accessible
        $this->validateCourseAccess($course);

        // Load relationships
        $course->load(['season', 'category', 'teachers' => function($query) {
            $query->wherePivotNull('finished_at')
                  ->select('campus_teachers.id', 'first_name', 'last_name', 'teacher_code')
                  ->withPivot('role');
        }]);

        // Get related courses from same season and category
        $relatedCourses = CampusCourse::where('season_id', $course->season_id)
            ->where('category_id', $course->category_id)
            ->where('id', '!=', $course->id)
            ->where('is_public', true)
            ->where('is_active', true)
            ->whereIn('status', ['planning', 'active', 'registration', 'in_progress'])
            ->take(4)
            ->get();

        // Check if course is in cart
        $cart = Cart::getCurrent();
        $isInCart = $cart && $cart->items()->where('course_id', $course->id)->exists();

        return view('catalog.show', compact(
            'course',
            'relatedCourses',
            'isInCart'
        ));
    }

    /**
     * Get season available for registration
     */
    private function getRegistrationSeason(): ?CampusSeason
    {
        // Priority order:
        // 1. Current season with open registration
        // 2. Planning season (future)
        // 3. Active season (current courses)
        
        $seasons = CampusSeason::where('is_active', true)
            ->orderBy('season_start', 'asc')
            ->get();

        // First, try to find a season with open registration
        foreach ($seasons as $season) {
            if ($season->isRegistrationOpen()) {
                return $season;
            }
        }

        // Then try to find a planning or active season
        foreach ($seasons as $season) {
            if (in_array($season->status, ['planning', 'active'])) {
                return $season;
            }
        }

        // If no season found, try to get current season
        return CampusSeason::getCurrent();
    }

    /**
     * Apply filters to course query
     */
    private function applyFilters(Builder $query, Request $request): void
    {
        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Level filter
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Format filter
        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }

        // Price range filter
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Hours range filter
        if ($request->filled('hours_min')) {
            $query->where('hours', '>=', $request->hours_min);
        }

        if ($request->filled('hours_max')) {
            $query->where('hours', '<=', $request->hours_max);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'start_date');
        $sortOrder = $request->get('order', 'asc');

        $allowedSorts = [
            'title' => 'title',
            'price' => 'price',
            'start_date' => 'start_date',
            'hours' => 'hours'
        ];

        if (isset($allowedSorts[$sortBy])) {
            $query->orderBy($allowedSorts[$sortBy], $sortOrder);
        }
    }

    /**
     * Validate course access for public catalog
     */
    private function validateCourseAccess(CampusCourse $course): void
    {
        // Check if course is public and active
        if (!$course->is_public || !$course->is_active) {
            abort(404, 'Curso no encontrado');
        }

        // Check if course has valid status
        $validStatuses = ['planning', 'active', 'registration', 'in_progress'];
        if (!in_array($course->status, $validStatuses)) {
            abort(404, 'Curso no disponible');
        }

        // Check if season is accessible
        if (!$course->season || !$course->season->is_active) {
            abort(404, 'Temporada no disponible');
        }

        // Registration period check - allow viewing but show status
        // Don't throw exception, just indicate status in the view
    }

    /**
     * API endpoint to check course availability
     */
    public function checkAvailability(CampusCourse $course)
    {
        $this->validateCourseAccess($course);

        $availability = [
            'available' => $course->hasAvailableSpots(),
            'spots_left' => $course->available_spots,
            'max_students' => $course->max_students,
            'registration_open' => $course->season?->isRegistrationOpen() ?? false,
            'can_enroll' => $course->hasAvailableSpots() && 
                          ($course->season?->isRegistrationOpen() ?? false),
            'price' => $course->price,
            'level_label' => $course->level ? CampusCourse::LEVELS[$course->level] : null,
            'format' => $course->format,
            'location' => $course->location,
            'start_date' => $course->start_date?->format('d/m/Y'),
            'end_date' => $course->end_date?->format('d/m/Y'),
            'hours' => $course->hours,
            'sessions' => $course->sessions,
        ];

        return response()->json($availability);
    }

    /**
     * Search courses via AJAX
     */
    public function search(Request $request)
    {
        $season = $this->getRegistrationSeason();
        
        if (!$season) {
            return response()->json(['courses' => [], 'message' => 'No season available']);
        }

        $query = CampusCourse::where('season_id', $season->id)
            ->where('is_public', true)
            ->where('is_active', true)
            ->whereIn('status', ['planning', 'active', 'registration', 'in_progress']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $courses = $query->limit(10)->get(['id', 'title', 'code', 'price', 'slug']);

        return response()->json(['courses' => $courses]);
    }
}
