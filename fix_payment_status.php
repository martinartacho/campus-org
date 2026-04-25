<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FIX PAYMENT STATUS ===\n";

$registration = \App\Models\CampusRegistration::find(727);

if (!$registration) {
    echo "Registration 727 not found!\n";
    exit;
}

echo "Before update:\n";
echo "- Status: " . $registration->status . "\n";
echo "- Payment Status: " . $registration->payment_status . "\n";

// Update to paid status
$registration->update([
    'status' => 'confirmed',
    'payment_status' => 'paid',
    'payment_completed_at' => now(),
    'user_id' => \App\Models\User::where('email', $registration->student->email)->first()?->id
]);

echo "\nAfter update:\n";
echo "- Status: " . $registration->fresh()->status . "\n";
echo "- Payment Status: " . $registration->fresh()->payment_status . "\n";
echo "- User ID: " . $registration->fresh()->user_id . "\n";

// Update CampusCourseStudent too
$courseStudent = \App\Models\CampusCourseStudent::where('student_id', $registration->student_id)
    ->where('course_id', $registration->course_id)
    ->first();

if ($courseStudent) {
    $courseStudent->update([
        'academic_status' => 'active'
    ]);
    echo "- CourseStudent Status: " . $courseStudent->fresh()->academic_status . "\n";
}

echo "\n✅ Payment status fixed!\n";
