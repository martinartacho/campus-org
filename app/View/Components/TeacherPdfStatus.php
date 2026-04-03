<?php

namespace App\View\Components;

use App\Models\CampusTeacher;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Log;

class TeacherPdfStatus extends Component
{
    /**
     * The teacher instance.
     *
     * @var CampusTeacher
     */
    public $teacher;

    /**
     * Whether the teacher has IBAN.
     *
     * @var bool
     */
    public $hasIban;

    /**
     * Whether the PDF is updated.
     *
     * @var bool
     */
    public $isPdfUpdated;

    /**
     * Create a new component instance.
     *
     * @param CampusTeacher $teacher
     */
    public function __construct(CampusTeacher $teacher)
    {
        $this->teacher = $teacher;
        $this->hasIban = false;
        $this->isPdfUpdated = false;

        $this->checkPdfStatus();
    }

    /**
     * Check the PDF status for the teacher.
     */
    private function checkPdfStatus(): void
    {
        try {
            $this->hasIban = $this->teacher && !empty($this->teacher->iban);
            
            // Verificar si el PDF està actualitzat segons la data límit
            if ($this->hasIban) {
                $deadline = \App\Models\Setting::get('pdf_update_deadline', '2026-03-15');
                $deadlineDate = \Carbon\Carbon::parse($deadline);
                
                // Obtenir l'últim PDF del professor
                $directory = storage_path('app/consents/teachers/' . $this->teacher->id);
                if (is_dir($directory)) {
                    $files = glob($directory . '/consent_dades_teacher_*.pdf');
                    if (!empty($files)) {
                        usort($files, function($a, $b) {
                            return filemtime($b) - filemtime($a);
                        });
                        $latestFile = $files[0];
                        $pdfModifiedTime = filemtime($latestFile);
                        $pdfDate = \Carbon\Carbon::createFromTimestamp($pdfModifiedTime);
                        $this->isPdfUpdated = $pdfDate->greaterThan($deadlineDate);
                    }
                }
            }
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            $this->hasIban = false;
            Log::warning('Corrupted IBAN detected for teacher: ' . $this->teacher->id . ' - ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->hasIban = false;
            Log::error('Error checking IBAN for teacher: ' . $this->teacher->id . ' - ' . $e->getMessage());
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.teacher-pdf-status');
    }
}
