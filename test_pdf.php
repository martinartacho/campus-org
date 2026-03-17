<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\ConsentPDFService;
use App\Models\CampusTeacher;
use App\Models\User;
use App\Models\CampusSeason;
use App\Models\CampusCourse;
use App\Models\CampusTeacherPayment;

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Obtener datos reales
$teacher = CampusTeacher::find(14);
$user = User::find($teacher->user_id);
$season = CampusSeason::where('slug', 'curs-2025-26-1q')->first();
$course = CampusCourse::find(35);
$payment = CampusTeacherPayment::find(4);

echo "Datos:\n";
echo "Teacher: {$teacher->first_name} {$teacher->last_name}\n";
echo "Course: {$course->title}\n";
echo "Season: {$season->name}\n";
echo "Payment: {$payment->payment_option}\n\n";

// Generar PDF en /tmp para evitar problemas de permisos
$html = $pdfService->buildConsentHTML(
    $teacher,
    $user,
    $season,
    $course,
    $payment,
    'test-checksum-' . date('His'),
    new DateTime(),
    true,  // autoritzacioDades
    false  // declaracioFiscal
);

// Guardar HTML para revisión
file_put_contents('/tmp/consent_html.html', $html);
echo "HTML guardado en: /tmp/consent_html.html\n";

// Generar PDF con TCPDF directamente en /tmp
$pdf = new TCPDF();
$pdf->SetCreator('Campus UPG');
$pdf->SetAuthor('Universitat Popular de Granollers');
$pdf->SetTitle('Document de Consentiment');
$pdf->SetSubject('Consentiment i Pagament');
$pdf->SetKeywords('TCPDF, PDF, consentiment, pagament');
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');

$path = '/tmp/test_consent_' . date('His') . '.pdf';
$pdf->Output($path, 'F');

echo "PDF generado en: $path\n";
echo "Archivo existe: " . (file_exists($path) ? 'YES' : 'NO') . "\n";
echo "Tamaño: " . (file_exists($path) ? filesize($path) . ' bytes' : 'N/A') . "\n";

// Revisar logs
$logPath = storage_path('logs/laravel.log');
echo "\nÚltimas líneas del log:\n";
$lines = file($logPath);
$lastLines = array_slice($lines, -10);
foreach ($lastLines as $line) {
    echo $line;
}
