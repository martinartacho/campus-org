<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking seasons registration dates:" . PHP_EOL;

$seasons = \App\Models\CampusSeason::where('is_active', true)->get();

foreach ($seasons as $season) {
    echo "Season: " . $season->name . PHP_EOL;
    echo "  Registration Start: " . ($season->registration_start ? $season->registration_start->format('Y-m-d') : 'NULL') . PHP_EOL;
    echo "  Registration End: " . ($season->registration_end ? $season->registration_end->format('Y-m-d') : 'NULL') . PHP_EOL;
    echo "  Is Registration Open: " . ($season->isRegistrationOpen() ? 'YES' : 'NO') . PHP_EOL;
    echo "  Status: " . $season->status . PHP_EOL;
    echo PHP_EOL;
}
