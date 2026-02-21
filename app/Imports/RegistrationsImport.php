<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Validators\Failure;
use App\Models\CampusRegistration;
use App\Models\CampusCourse;
use App\Models\CampusStudent;

class RegistrationsImport implements WithMultipleSheets, SkipsOnFailure, SkipsEmptyRows
{
    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        // Log failures if needed
    }

    public function sheets(): array
    {
        return [
            new OrdersSheetImport(),
            new TDAHSheetImport(),
            new INTELIGENCIAEMOCIONALSheetImport(),
            new EXCELSheetImport(),
            new LEANMANAGEMENTSheetImport(),
            new ANGLESSheetImport(),
            new FOTOGRAFIADIGITALSheetImport(),
            new BIOETICASheetImport(),
            new PILATESSheetImport(),
            new PROTECCIONDEDATOSSheetImport(),
            new NUTRICIONCLINICASheetImport(),
            new MINDFULNESSSheetImport(),
        ];
    }
}

class OrdersSheetImport implements ToModel, WithHeadingRow, SkipsOnFailure, SkipsEmptyRows
{
    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        // Log failures if needed
        foreach ($failures as $failure) {
            \Log::error('Import failure: ' . $failure->toString());
        }
    }

    public function model(array $row)
    {
        try {
            // Skip empty rows
            if (empty($row['nif']) && empty($row['nombre'])) {
                return null;
            }

            // Find or create student using correct field names
            $student = CampusStudent::firstOrCreate(
                ['dni' => $row['nif']], // Use 'dni' field instead of 'nif'
                [
                    'first_name' => $row['nombre'] ?? '',
                    'last_name' => '',
                    'email' => $row['email'] ?? '',
                    'phone' => $row['telefono'] ?? '',
                    'address' => '',
                    'city' => '',
                    'postal_code' => '',
                    'birth_date' => null,
                    'status' => 'active'
                ]
            );

            // Find course by code
            $courseCode = $this->getCourseCodeFromRow($row);
            if (!$courseCode) {
                return null;
            }

            $course = CampusCourse::where('code', $courseCode)->first();
            if (!$course) {
                return null;
            }

            // Create registration with proper date format
            return new CampusRegistration([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'registration_code' => uniqid('REG_', true),
                'registration_date' => now()->format('Y-m-d'), // Proper date format
                'status' => 'confirmed',
                'amount' => $row['precio'] ?? 0,
                'payment_status' => 'paid',
                'payment_method' => 'import',
                'notes' => 'Importado desde Excel'
            ]);

        } catch (\Exception $e) {
            // Log error but continue processing
            \Log::error('Import error: ' . $e->getMessage(), [
                'row' => $row,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    private function getCourseCodeFromRow($row)
    {
        // Map course names to codes based on the sheet structure
        $courseMap = [
            'TDAH' => 'EDU201',
            'INTEL·LIGÈNCIA EMOCIONAL' => 'SOC301',
            'EXCEL AVANZAT' => 'TEC401',
            'LEAN MANAGEMENT' => 'GES501',
            'ANGLÈS B2' => 'IDI601',
            'FOTOGRAFIA DIGITAL' => 'ART701',
            'BIOÈTICA' => 'CIE801',
            'PILATES' => 'ESP901',
            'PROTECCIÓ DE DADES' => 'DER1001',
            'NUTRICIÓ CLÍNICA' => 'SAN1102',
            'MINDFULNESS A L\'AULA' => 'EDU1203'
        ];

        // Check if any course name exists in the row
        foreach ($courseMap as $name => $code) {
            foreach ($row as $key => $value) {
                if (stripos($key, $name) !== false || stripos($value, $name) !== false) {
                    return $code;
                }
            }
        }

        return null;
    }
}

// Generic sheet import class for other sheets (they might have different structures)
class TDAHSheetImport extends OrdersSheetImport {}
class INTELIGENCIAEMOCIONALSheetImport extends OrdersSheetImport {}
class EXCELSheetImport extends OrdersSheetImport {}
class LEANMANAGEMENTSheetImport extends OrdersSheetImport {}
class ANGLESSheetImport extends OrdersSheetImport {}
class FOTOGRAFIADIGITALSheetImport extends OrdersSheetImport {}
class BIOETICASheetImport extends OrdersSheetImport {}
class PILATESSheetImport extends OrdersSheetImport {}
class PROTECCIONDEDATOSSheetImport extends OrdersSheetImport {}
class NUTRICIONCLINICASheetImport extends OrdersSheetImport {}
class MINDFULNESSSheetImport extends OrdersSheetImport {}
