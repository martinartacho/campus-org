<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\SimpleConsentPDFService;
use App\Models\CampusTeacher;
use App\Models\CampusSeason;
use App\Models\CampusCourse;
use App\Models\CampusTeacherPayment;

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Obtener datos reales
$teacher = CampusTeacher::find(14);
$season = CampusSeason::where('slug', 'curs-2025-26-1q')->first();
$course = CampusCourse::find(35);
$payment = CampusTeacherPayment::find(4);

echo "=== PRUEBA SIMPLE PDF SERVICE ===\n";
echo "Teacher: {$teacher->first_name} {$teacher->last_name}\n";
echo "Course: {$course->title}\n";
echo "Payment: {$payment->payment_option}\n\n";

// Probar nuevo servicio guardando en /tmp
$service = new SimpleConsentPDFService();

$html = $service->buildSimpleHTML(
    $teacher,
    $season,
    $course,
    $payment,
    true,  // autoritzacioDades
    false  // declaracioFiscal
);

// Guardar HTML para revisión
file_put_contents('/tmp/simple_consent_html.html', $html);
echo "HTML guardado en: /tmp/simple_consent_html.html\n";

// Generar PDF directamente en /tmp
$pdf = new TCPDF();
$pdf->SetCreator('Campus UPG');
$pdf->SetAuthor('Universitat Popular de Granollers');
$pdf->SetTitle('Document de Consentiment');
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');

$path = '/tmp/simple_consent_' . date('His') . '.pdf';
$pdf->Output($path, 'F');

echo "✅ PDF generado exitosamente\n";
echo "Ruta: $path\n";
echo "Existe: " . (file_exists($path) ? 'YES' : 'NO') . "\n";

if (file_exists($path)) {
    echo "Tamaño: " . filesize($path) . " bytes\n";
}

echo "\n=== PRUEBA COMPLETADA ===\n";
