<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SYNC REGISTRATION 727 ===\n";

$registration = \App\Models\CampusRegistration::find(727);

if (!$registration) {
    echo "Registration 727 not found!\n";
    exit;
}

echo "✅ Registration found!\n";
echo "Student ID: " . $registration->student_id . "\n";
echo "Course ID: " . $registration->course_id . "\n";
echo "Season ID: " . $registration->season_id . "\n";
echo "Status: " . $registration->status . "\n";
echo "Payment Status: " . $registration->payment_status . "\n";

// Check if already synced
$existing = \App\Models\CampusCourseStudent::where('student_id', $registration->student_id)
    ->where('course_id', $registration->course_id)
    ->first();

if ($existing) {
    echo "⚠️  Already synced with CampusCourseStudent ID: " . $existing->id . "\n";
    echo "   Academic Status: " . $existing->academic_status . "\n";
} else {
    echo "🔄 Creating CampusCourseStudent record...\n";
    
    $courseStudent = \App\Models\CampusCourseStudent::create([
        'student_id' => $registration->student_id,
        'course_id' => $registration->course_id,
        'season_id' => $registration->season_id,
        'enrollment_date' => $registration->registration_date ?? now()->format('Y-m-d'),
        'academic_status' => $registration->payment_status === 'paid' ? 'active' : 'enrolled',
        'start_date' => $registration->course->start_date ?? now()->format('Y-m-d'),
        'end_date' => $registration->course->end_date,
        'metadata' => array_merge($registration->metadata ?? [], [
            'registration_id' => $registration->id,
            'registration_code' => $registration->registration_code,
            'sync_date' => now()->format('Y-m-d H:i:s'),
            'payment_status' => $registration->payment_status,
        ])
    ]);
    
    echo "✅ Created CampusCourseStudent ID: " . $courseStudent->id . "\n";
    echo "   Academic Status: " . $courseStudent->academic_status . "\n";
}

// Update registration payment status if needed
if ($registration->payment_status === 'pending') {
    echo "⚠️  Registration still has 'pending' payment status\n";
    echo "   Consider updating to 'paid' if payment was completed\n";
    
    // Uncomment to update:
    // $registration->update(['payment_status' => 'paid', 'status' => 'confirmed']);
    // echo "✅ Updated registration to paid status\n";
}

echo "\n=== VERIFICATION ===\n";
$studentCourses = \App\Models\CampusCourseStudent::where('student_id', $registration->student_id)->get();
echo "Student courses count: " . $studentCourses->count() . "\n";

foreach ($studentCourses as $course) {
    echo "- Course: " . $course->course->title . " (Status: " . $course->academic_status . ")\n";
}
