<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Columns in campus_seasons table:" . PHP_EOL;

$schema = Illuminate\Support\Facades\Schema::getColumnListing('campus_seasons');
foreach ($schema as $name => $column) {
    echo "- " . $name . PHP_EOL;
}
