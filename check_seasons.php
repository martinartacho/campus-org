<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check seasons with registration dates
$seasons = \App\Models\CampusSeason::where('is_active', true)->get();
echo "Active seasons with registration dates:" . PHP_EOL;

foreach ($seasons as $season) {
    echo "- " . $season->name . PHP_EOL;
    echo "  Registration start: " . ($season->registration_start ? $season->registration_start->format('Y-m-d') : 'NULL') . PHP_EOL;
    echo "  Registration end: " . ($season->registration_end ? $season->registration_end->format('Y-m-d') : 'NULL') . PHP_EOL;
    echo "  Registration open: " . ($season->isRegistrationOpen() ? 'YES' : 'NO') . PHP_EOL;
    echo PHP_EOL;
}
