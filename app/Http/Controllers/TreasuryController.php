<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CampusTeacher;
use App\Models\CampusTeacherPayment;
use App\Models\ConsentHistory;
use App\Models\CampusCourseTeacher;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TreasuryController extends Controller
{
    use AuthorizesRequests;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|treasury');
    }

    /**
     * Display all consents with filtering and pagination.
     */
    public function consents(Request $request)
    {
        $query = ConsentHistory::with(['teacher.user', 'course', 'season']);
        
        // Aplicar filtros
        if ($request->filled('season')) {
            $query->where('season', $request->season);
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('document_path');
            } elseif ($request->status === 'pending') {
                $query->whereNull('document_path');
            }
        }
        
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('teacher.user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('name', 'like', "%{$searchTerm}%")
                          ->orWhere('email', 'like', "%{$searchTerm}%");
                })
                ->orWhereHas('course', function ($courseQuery) use ($searchTerm) {
                    $courseQuery->where('title', 'like', "%{$searchTerm}%")
                              ->orWhere('code', 'like', "%{$searchTerm}%");
                });
            });
        }
        
        $consentments = $query->latest('accepted_at')->paginate(20);
        
        return view('treasury.consents', compact('consentments'));
    }
    
    /**
     * Display consent details.
     */
    public function showConsent(ConsentHistory $consent)
    {
        $consent->load(['teacher.user', 'course', 'season']);
        
        return view('treasury.consents.show', compact('consent'));
    }
    
    /**
     * Export consents to Excel.
     */
    public function exportConsents()
    {
        $consentments = ConsentHistory::with(['teacher.user', 'course', 'season'])
            ->latest('accepted_at')
            ->get();
        
        // Aquí iría la lógica de exportación a Excel
        // Por ahora, redirigimos con un mensaje
        return back()->with('success', 'Función de exportación no implementada aún');
    }

    /**
     * Display payments listing.
     */
    public function payments()
    {
        $payments = CampusTeacherPayment::with(['teacher', 'course', 'season'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('treasury.payments.index', compact('payments'));
    }

    /**
     * Display teachers listing for treasury.
     */
    public function teachers()
    {
        $teachers = CampusTeacher::with(['user', 'courses'])
            ->orderBy('last_name')
            ->paginate(20);
            
        return view('treasury.teachers.index', compact('teachers'));
    }

    /**
     * Display financial reports.
     */
    public function reports()
    {
        return view('treasury.reports.index');
    }
}
