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
        
        // Obtener traducciones
        $campusName = __('campus.denominacio_UPG');
        $aiepInfo = __('campus.denominacio_AIEP');
        
        $html = '
        <style>
            body { font-family: helvetica, sans-serif; font-size: 12px; }
            h1 { font-size: 18px; color: #2c3e50; text-align: center; margin-bottom: 30px; }
            h2 { font-size: 14px; color: #34495e; border-bottom: 2px solid #3490dc; padding-bottom: 5px; margin-top: 20px; margin-bottom: 15px; }
            .header { text-align: center; margin-bottom: 30px; }
            .section { margin-bottom: 20px; }
            .info-row { margin-bottom: 8px; display: flex; }
            .label { font-weight: bold; color: #555; width: 150px; }
            .value { color: #333; flex: 1; }
            .declaration { margin: 10px 0; padding: 10px; background: #f8f9fa; border-left: 3px solid #3490dc; }
            .signature { margin-top: 30px; text-align: center; }
            .footer { font-size: 10px; color: #777; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; }
        </style>
        
        <div class="header">
            <h1>DOCUMENT DE CONSENTIMENT RGPD – Professorat</h1>
            <p><strong>' . htmlspecialchars($campusName) . '</strong></p>
        </div>
        
        <div class="section">
            <h2>1. DADES PROFESSOR/A</h2>
            <div class="info-row">
                <span class="label">Nom complet:</span>
                <span class="value">' . htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) . '</span>
            </div>
            <div class="info-row">
                <span class="label">DNI:</span>
                <span class="value">' . htmlspecialchars($teacher->dni ?? 'N/A') . '</span>
            </div>
            <div class="info-row">
                <span class="label">Email:</span>
                <span class="value">' . htmlspecialchars($teacher->email) . '</span>
            </div>
            <div class="info-row">
                <span class="label">Telèfon:</span>
                <span class="value">' . htmlspecialchars($teacher->phone ?? 'N/A') . '</span>
            </div>
        </div>
        
        <div class="section">
            <h2>2. DADES DEL CURS</h2>
            <div class="info-row">
                <span class="label">Títol:</span>
                <span class="value">' . htmlspecialchars($course->title) . '</span>
            </div>
            <div class="info-row">
                <span class="label">Codi:</span>
                <span class="value">' . htmlspecialchars($course->code) . '</span>
            </div>
            <div class="info-row">
                <span class="label">Temporada:</span>
                <span class="value">' . htmlspecialchars($season->name) . '</span>
            </div>
        </div>
        
        <div class="section">
            <h2>3. OPCIÓ DE PAGAMENT</h2>
            <div class="info-row">
                <span class="label">Opció seleccionada:</span>
                <span class="value">' . htmlspecialchars($paymentLabel) . '</span>
            </div>';
        
        if ($paymentOption === 'waived_fee') {
            $html .= '
            <div class="info-row">
                <span class="label">Nota:</span>
                <span class="value">Renuncia al cobrament per l\'activitat realitzada</span>
            </div>';
        } elseif ($paymentOption === 'ceded_fee') {
            $html .= '
            <div class="info-row">
                <span class="label">IBAN:</span>
                <span class="value">' . htmlspecialchars($payment->iban ?? 'N/A') . '</span>
            </div>
            <div class="info-row">
                <span class="label">Titular del compte:</span>
                <span class="value">' . htmlspecialchars($payment->bank_titular ?? 'N/A') . '</span>
            </div>';
        } elseif ($paymentOption === 'own_fee') {
            $html .= '
            <div class="info-row">
                <span class="label">IBAN:</span>
                <span class="value">' . htmlspecialchars($teacher->masked_iban ?? 'N/A') . '</span>
            </div>
            <div class="info-row">
                <span class="label">Titular del compte:</span>
                <span class="value">' . htmlspecialchars($payment->bank_titular ?? $teacher->first_name . ' ' . $teacher->last_name) . '</span>
            </div>';
        }
        
        $html .= '
        </div>
        
        <div class="section">
            <h2>4. DECLARACIONS I AUTORITZACIONS</h2>';
        
        if ($declaracioFiscal) {
            $html .= '
            <div class="declaration">
                <strong>✅ DECLARACIÓ FISCAL:</strong> Declara sota la meva responsabilitat que les dades facilitades són certes 
                i que es troba en alguna de les següents situacions fiscals:
                <ul style="margin-left: 20px; font-size: 11px;">
                    <li>Soc autònom i presento declaracions trimestrals d\'IVA</li>
                    <li>Soc pensionista i els meus ingressos estan exempts d\'IRPF</li>
                    <li>Soc aturat i no tinc ingressos subjectes a retenció</li>
                    <li>Altres situacions exentes o amb retencions específiques</li>
                </ul>
            </div>';
        } else {
            $html .= '
            <div class="declaration">
                <strong>❌ No procedeix declaració fiscal</strong>
            </div>';
        }
        
        if ($autoritzacioDades) {
            $html .= '
            <div class="declaration">
                <strong>✅ AUTORITZACIÓ TRACTAMENT DE DADES:</strong> Autoritzo el tractament de les meves dades personals 
                amb finalitats fiscals i administratives, d\'acord amb la normativa vigent de protecció de dades.
            </div>';
        } else {
            $html .= '
            <div class="declaration">
                <strong>❌ No s\'ha registrat l\'autorització de dades</strong>
            </div>';
        }
        
        $html .= '
        </div>
        
        <div class="signature">
            <p>_____________________________</p>
            <p><strong>' . htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) . '</strong></p>
            <p>DNI: ' . htmlspecialchars($teacher->dni ?? 'N/A') . '</p>
        </div>
        
        <div class="footer">
            <p><strong>PROTECCIÓ DE DADES - RGPD</strong></p>
            <p>A la UPG tractem la informació que ens faciliteu exclusivament per oferir el servei sol·licitat. Les dades proporcionades es conservaran mentre es mantingui la relació formativa, o durant els anys necessaris per complir amb les obligacions legals. Les dades no se cediran a tercers excepte en els casos d\'obligació legal.</p>
            <p>Teniu dret a obtenir confirmació i accés quant al tractament de les vostres dades personals per part de l\'Associació per a l\'Impuls d\'Estudis Populars (AIEP). Podeu rectificar les vostres dades o sol·licitar la seva supressió quan aquestes no siguin necessàries.</p>
            <p><strong>' . htmlspecialchars($aiepInfo) . '</strong></p>
            <p><small>Document generat electrònicament el ' . date('d/m/Y H:i:s') . '</small></p>
        </div>';
        
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
