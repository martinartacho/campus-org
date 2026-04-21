<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check seasons
$seasons = \App\Models\CampusSeason::where('is_active', true)->get();
echo "Active seasons: " . $seasons->count() . PHP_EOL;

foreach ($seasons as $season) {
    echo "- " . $season->name . " (ID: " . $season->id . ")" . PHP_EOL;
}

// Check courses
$courses = \App\Models\CampusCourse::where('is_public', true)->where('is_active', true)->limit(5)->get();
echo PHP_EOL . "Public active courses: " . $courses->count() . PHP_EOL;

foreach ($courses as $course) {
    echo "- " . $course->code . " - " . $course->title . " - " . $course->price . " EUR" . PHP_EOL;
}
