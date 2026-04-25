<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUG REGISTRATION 727 ===\n";

$reg = \App\Models\CampusRegistration::find(727);

if (!$reg) {
    echo "Registration 727 not found!\n";
    exit;
}

echo "✅ Registration found!\n";
echo "User ID: " . $reg->user_id . "\n";
echo "Student ID: " . $reg->student_id . "\n";
echo "Payment Status: " . $reg->payment_status . "\n";
echo "Status: " . $reg->status . "\n";
echo "Created: " . $reg->created_at . "\n";

// Check if user exists
$user = \App\Models\User::find($reg->user_id);
echo "User exists: " . ($user ? 'YES' : 'NO') . "\n";
if ($user) {
    echo "User email: " . $user->email . "\n";
    echo "User name: " . $user->first_name . " " . $user->last_name . "\n";
}

// Check student
$student = \App\Models\CampusStudent::find($reg->student_id);
echo "Student exists: " . ($student ? 'YES' : 'NO') . "\n";
if ($student) {
    echo "Student email: " . $student->email . "\n";
    echo "Student name: " . $student->first_name . " " . $student->last_name . "\n";
}

// Check campus_course_student sync
$courseStudent = \App\Models\CampusCourseStudent::where('student_id', $reg->student_id)
    ->where('course_id', $reg->course_id)
    ->first();

echo "CourseStudent sync: " . ($courseStudent ? 'YES' : 'NO') . "\n";
if ($courseStudent) {
    echo "Academic Status: " . $courseStudent->academic_status . "\n";
}

echo "\n=== DEBUG CURRENT USER ===\n";
if (auth()->check()) {
    echo "Current User ID: " . auth()->id() . "\n";
    echo "Current User Email: " . auth()->user()->email . "\n";
    echo "Can access invoice: " . ((auth()->id() === $reg->user_id) ? 'YES' : 'NO') . "\n";
} else {
    echo "User not authenticated\n";
}
