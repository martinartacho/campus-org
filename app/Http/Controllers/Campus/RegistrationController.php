<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Models\CampusRegistration;
use App\Models\CampusStudent;
use App\Models\CampusCourse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:campus.registrations.view');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = CampusRegistration::with(['student', 'course'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('nif', 'like', "%{$search}%");
                })
                ->orWhereHas('course', function ($courseQuery) use ($search) {
                    $courseQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })
                ->orWhere('registration_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->get('payment_status'));
        }

        $registrations = $query->paginate(25);

        // Estadísticas
        $stats = [
            'total' => CampusRegistration::count(),
            'confirmed' => CampusRegistration::where('status', 'confirmed')->count(),
            'pending' => CampusRegistration::where('status', 'pending')->count(),
            'paid' => CampusRegistration::where('payment_status', 'paid')->count(),
            'total_amount' => CampusRegistration::sum('amount'),
        ];

        return view('campus.registrations.index', compact('registrations', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
