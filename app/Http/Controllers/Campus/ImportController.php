<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Imports\RegistrationsImport;
use App\Models\CampusRegistration;
use App\Models\CampusCourse;
use App\Models\CampusStudent;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function index()
    {
        $registrations = CampusRegistration::with(['student', 'course'])
            ->orderBy('registration_date', 'desc')
            ->paginate(50);

        $stats = [
            'total' => CampusRegistration::count(),
            'confirmed' => CampusRegistration::where('status', 'confirmed')->count(),
            'pending' => CampusRegistration::where('status', 'pending')->count(),
            'paid' => CampusRegistration::where('payment_status', 'paid')->count(),
            'total_amount' => CampusRegistration::sum('amount')
        ];

        return view('campus.registrations.index', compact('registrations', 'stats'));
    }

    public function showImportForm()
    {
        return view('campus.registrations.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Store file temporarily
            $path = $file->storeAs('imports', $fileName, 'local');

            // Import data
            $import = new RegistrationsImport();
            Excel::import($import, $path);

            // Clean up temporary file
            Storage::disk('local')->delete($path);

            return redirect()->route('campus.registrations.index')
                ->with('success', 'Importación completada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error durante la importación: ' . $e->getMessage());
        }
    }

    public function destroy(CampusRegistration $registration)
    {
        $registration->delete();
        
        return redirect()->route('campus.registrations.index')
            ->with('success', 'Registro eliminado correctamente.');
    }

    public function export()
    {
        $registrations = CampusRegistration::with(['student', 'course'])
            ->get()
            ->map(function ($registration) {
                return [
                    'ID' => $registration->id,
                    'Código Registro' => $registration->registration_code,
                    'NIF Alumno' => $registration->student->nif,
                    'Nombre Alumno' => $registration->student->first_name,
                    'Email Alumno' => $registration->student->email,
                    'Código Curso' => $registration->course->code,
                    'Nombre Curso' => $registration->course->title,
                    'Fecha Registro' => $registration->registration_date,
                    'Estado' => $registration->status,
                    'Importe' => $registration->amount,
                    'Estado Pago' => $registration->payment_status,
                    'Método Pago' => $registration->payment_method
                ];
            });

        return response()->json($registrations);
    }
}
