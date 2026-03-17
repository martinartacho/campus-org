<?php

namespace App\Services;

use TCPDF;
use App\Models\CampusTeacher;
use App\Models\User;
use App\Models\CampusSeason;
use App\Models\CampusCourse;
use App\Models\CampusTeacherPayment;

class ConsentPDFService
{
    public function generateTeacherConsentPDF(
        CampusTeacher $teacher,
        User $user,
        CampusSeason $season,
        CampusCourse $course,
        CampusTeacherPayment $payment,
        string $checksum,
        \DateTime $acceptedAt,
        bool $autoritzacioDades = false,
        bool $declaracioFiscal = false
    ): string {
        // Crear instancia TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Configurar metadata
        $pdf->SetCreator('Campus UPG');
        $pdf->SetAuthor('Campus UPG');
        $pdf->SetTitle('Document de Consentiment - ' . $teacher->first_name . ' ' . $teacher->last_name);
        $pdf->SetSubject('Consentiment de Pagament');
        
        // Configurar márgenes
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        
        // Añadir página
        $pdf->AddPage();
        
        // Construir HTML
        $html = $this->buildConsentHTML(
            $teacher,
            $user,
            $season,
            $course,
            $payment,
            $checksum,
            $acceptedAt,
            $autoritzacioDades,
            $declaracioFiscal
        );
        
        // Escribir HTML
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Generar ruta del archivo
        $seasonSlug = $season->slug ?? $season->id;
        $courseId = $course->code ?? 'unknown';
        $filename = "final_consent_{$seasonSlug}_{$courseId}.pdf";
        $path = "consents/teachers/{$teacher->id}/{$filename}";
        
        // Asegurar que el directorio exista
        $directory = storage_path("app/consents/teachers/{$teacher->id}");
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
        
        // Guardar PDF
        $pdf->Output(storage_path("app/{$path}"), 'F');
        
        return $path;
    }
    
    private function buildConsentHTML(
        CampusTeacher $teacher,
        User $user,
        CampusSeason $season,
        CampusCourse $course,
        CampusTeacherPayment $payment,
        string $checksum,
        \DateTime $acceptedAt,
        bool $autoritzacioDades,
        bool $declaracioFiscal
    ): string {
        // DEBUG SIMPLE
        \Log::info('ConsentPDF: buildConsentHTML llamado para teacher ' . $teacher->id . ' y course ' . $course->id);
        
        // DEBUG: Log valores de las variables
        \Log::info('ConsentPDF DEBUG - Variables:', [
            'teacher_id' => $teacher->id,
            'course_id' => $course->id,
            'payment_option' => $payment->payment_option ?? 'null',
            'declaracioFiscal' => $declaracioFiscal,
            'autoritzacioDades' => $autoritzacioDades,
            'payment_exists' => !is_null($payment),
            'fiscal_id' => $payment->fiscal_id ?? 'null',
            'iban' => $payment->iban ?? 'null'
        ]);
        
        $paymentOptionLabel = $this->getPaymentOptionLabel($payment->payment_option);
        
        return '
        <style>
            body { font-family: helvetica, sans-serif; font-size: 12px; }
            h1 { font-size: 18px; color: #2c3e50; text-align: center; }
            h2 { font-size: 14px; color: #34495e; border-bottom: 2px solid #3490dc; padding-bottom: 5px; }
            .section { margin-bottom: 20px; }
            .info-row { margin-bottom: 8px; }
            .label { font-weight: bold; color: #555; }
            .value { color: #333; }
            .declaration { margin: 10px 0; padding: 10px; background: #f8f9fa; border-left: 3px solid #3490dc; }
            .signature { margin-top: 30px; text-align: center; }
            .footer { font-size: 10px; color: #777; margin-top: 20px; }
        </style>
        
        <h1>DOCUMENT DE CONSENTIMENT I PAGAMENT</h1>
        
        <div class="section">
            <h2>1. INFORMACIÓ PROFESSOR/A</h2>
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
                <span class="value">' . htmlspecialchars($user->email) . '</span>
            </div>
            <div class="info-row">
                <span class="label">Telèfon:</span>
                <span class="value">' . htmlspecialchars($teacher->phone ?? 'N/A') . '</span>
            </div>
        </div>
        
        <div class="section">
            <h2>2. ACTIVITAT FORMATIVA</h2>
            <div class="info-row">
                <span class="label">Curs:</span>
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
                <span class="value">' . htmlspecialchars($paymentOptionLabel) . '</span>
            </div>
        </div>
        
        <div class="section">
            <h2>4. DADES FISCALS I BANCÀRIES</h2>';
        
        // Determinar si mostrem dades del professor o del beneficiari
        if ($payment && $payment->payment_option === 'ceded_fee') {
            // Mostrar dades del beneficiari si existen, sino del professor
            $html .= '
            <div class="info-row">
                <span class="label">Identificació fiscal:</span>
                <span class="value">' . htmlspecialchars($payment->fiscal_id ?? $teacher->dni ?? 'N/A') . '</span>
            </div>
            <div class="info-row">
                <span class="label">IBAN:</span>
                <span class="value">' . htmlspecialchars($payment->iban ?? $teacher->masked_iban ?? 'N/A') . '</span>
            </div>
            <div class="info-row">
                <span class="label">Titular del compte:</span>
                <span class="value">' . htmlspecialchars($payment->bank_titular ?? $teacher->first_name . ' ' . $teacher->last_name) . '</span>
            </div>';
        } elseif ($payment && $payment->payment_option === 'own_fee') {
            // Mostrar dades del professor
            $html .= '
            <div class="info-row">
                <span class="label">Identificació fiscal:</span>
                <span class="value">' . htmlspecialchars($payment->fiscal_id ?? $teacher->dni ?? 'N/A') . '</span>
            </div>
            <div class="info-row">
                <span class="label">IBAN:</span>
                <span class="value">' . htmlspecialchars($teacher->masked_iban ?? 'N/A') . '</span>
            </div>
            <div class="info-row">
                <span class="label">Titular del compte:</span>
                <span class="value">' . htmlspecialchars($payment->bank_titular ?? $teacher->first_name . ' ' . $teacher->last_name) . '</span>
            </div>';
        } else {
            // waived_fee o sin datos de pago - mostrar mensaje
            $html .= '
            <div class="info-row">
                <span class="label">Nota:</span>
                <span class="value">No aplica - Renuncia al cobrament o datos no disponibles</span>
            </div>';
        }
        
        $html .= '
        </div>
        
        <div style="page-break-before: always;"></div>
        
        <div class="section">
            <h2>5. DECLARACIONS I AUTORITZACIONS</h2>';
        
        // Mostrar siempre la declaración fiscal (temporal para pruebas)
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
        
        // Mostrar siempre la autorización de datos (temporal para pruebas)
        $html .= '
            <div class="declaration">
                <strong>✅ AUTORITZACIÓ TRACTAMENT DE DADES:</strong> Autoritzo el tractament de les meves dades personals 
                amb finalitats fiscals i administratives, d\'acord amb la normativa vigent de protecció de dades.
            </div>';
        
        $html .= '
        </div>
        
        <div class="section">
            <h2>6. VALIDACIÓ DEL DOCUMENT</h2>
            <div class="info-row">
                <span class="label">Data d\'acceptació:</span>
                <span class="value">' . $acceptedAt->format('d/m/Y H:i:s') . '</span>
            </div>
            <div class="info-row">
                <span class="label">Checksum de validació:</span>
                <span class="value">' . htmlspecialchars($checksum) . '</span>
            </div>
        </div>
        
        <div class="signature">
            <p>_____________________________</p>
            <p><strong>' . htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) . '</strong></p>
            <p>Firma digital acceptada</p>
        </div>
        
        <div class="footer">
            <p>Document generat electrònicament el ' . $acceptedAt->format('d/m/Y H:i:s') . '</p>
            <p>Campus UPG - Sistema de Gestió Acadèmica</p>
        </div>';
    }
    
    private function getPaymentOptionLabel(string $option): string
    {
        $labels = [
            'own_fee' => '✅ Accepto el cobrament',
            'ceded_fee' => '✅ Cedo el cobrament a tercer',
            'waived_fee' => '✅ Renuncio al cobrament',
        ];
        
        return $labels[$option] ?? $option;
    }
}
