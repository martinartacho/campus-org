<?php

namespace App\Services;

use App\Models\CampusTeacher;
use App\Models\User;
use App\Models\CampusSeason;
use App\Models\CampusCourse;
use App\Models\CampusTeacherPayment;
use TCPDF;

class SimpleConsentPDFService
{
    public function generateSimplePDF(
        CampusTeacher $teacher,
        CampusSeason $season,
        CampusCourse $course,
        ?CampusTeacherPayment $payment,
        bool $autoritzacioDades = false,
        bool $declaracioFiscal = false
    ): string {
        // Crear instancia TCPDF
        $pdf = new TCPDF();
        $pdf->SetCreator('Campus UPG');
        $pdf->SetAuthor('Universitat Popular de Granollers');
        $pdf->SetTitle('Document de Consentiment');
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();
        
        // Construir HTML simple
        $html = $this->buildSimpleHTML($teacher, $season, $course, $payment, $autoritzacioDades, $declaracioFiscal);
        
        // Escribir HTML
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Generar ruta con timestamp para evitar sobreescribir
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "simple_consent_{$season->slug}_{$course->id}_{$timestamp}.pdf";
        $path = "consents/teachers/{$teacher->id}/{$filename}";
        
        // Asegurar directorio
        $directory = storage_path("app/consents/teachers/{$teacher->id}");
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
        
        // Eliminar PDF anterior si existe (mantener solo el más reciente)
        $existingFiles = glob(storage_path("app/consents/teachers/{$teacher->id}/simple_consent_{$season->slug}_{$course->id}_*.pdf"));
        foreach ($existingFiles as $existingFile) {
            if (is_file($existingFile)) {
                unlink($existingFile);
                \Log::info('PDF anterior eliminado: ' . basename($existingFile));
            }
        }
        
        // Guardar PDF
        $pdf->Output(storage_path("app/{$path}"), 'F');
        
        \Log::info('PDF generado: ' . $path);
        
        return $path;
    }
    
    public function buildSimpleHTML(
        CampusTeacher $teacher,
        CampusSeason $season,
        CampusCourse $course,
        ?CampusTeacherPayment $payment,
        bool $autoritzacioDades,
        bool $declaracioFiscal
    ): string {
        $paymentOption = $payment->payment_option ?? 'unknown';
        $paymentLabel = $this->getPaymentLabel($paymentOption);
        
        $html = '
        <h1>DOCUMENT DE CONSENTIMENT</h1>
        
        <h2>1. DADES PROFESSOR/A</h2>
        <p><strong>Nom:</strong> ' . htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) . '</p>
        <p><strong>DNI:</strong> ' . htmlspecialchars($teacher->dni ?? 'N/A') . '</p>
        <p><strong>Email:</strong> ' . htmlspecialchars($teacher->email) . '</p>
        
        <h2>2. DADES DEL CURS</h2>
        <p><strong>Títol:</strong> ' . htmlspecialchars($course->title) . '</p>
        <p><strong>Codi:</strong> ' . htmlspecialchars($course->code) . '</p>
        <p><strong>Temporada:</strong> ' . htmlspecialchars($season->name) . '</p>
        
        <h2>3. OPCIÓ DE PAGAMENT</h2>
        <p><strong>Opció:</strong> ' . htmlspecialchars($paymentLabel) . '</p>
        
        <h2>4. DADES BANCÀRIES</h2>';
        
        if ($paymentOption === 'waived_fee') {
            $html .= '<p><strong>Nota:</strong> Renuncia al cobrament per l\'activitat realitzada</p>';
        } else {
            $html .= '<p><strong>IBAN:</strong> ' . htmlspecialchars($teacher->masked_iban ?? 'N/A') . '</p>';
        }
        
        $html .= '
        <h2>5. DECLARACIONS</h2>';
        
        if ($declaracioFiscal) {
            $html .= '<p>✅ Declaració fiscal acceptada</p>';
        } else {
            $html .= '<p>❌ No procedeix declaració fiscal</p>';
        }
        
        if ($autoritzacioDades) {
            $html .= '<p>✅ Autorització de dades acceptada</p>';
        } else {
            $html .= '<p>❌ No s\'ha registrat l\'autorització de dades</p>';
        }
        
        $html .= '
        <h2>6. SIGNatura</h2>
        <p>_____________________________</p>
        <p>' . htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) . '</p>
        <p>DNI: ' . htmlspecialchars($teacher->dni ?? 'N/A') . '</p>
        
        <p><small>Document generat el ' . date('d/m/Y H:i:s') . '</small></p>';
        
        return $html;
    }
    
    private function getPaymentLabel(string $option): string
    {
        $labels = [
            'own_fee' => '✅ Accepto el cobrament',
            'ceded_fee' => '✅ Cedo el cobrament a tercer',
            'waived_fee' => '✅ Renuncio al cobrament',
        ];
        
        return $labels[$option] ?? $option;
    }
}
