<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Enable query logging
use Illuminate\Support\Facades\DB;
DB::listen(function ($query) {
    if (strpos($query->sql, 'programs') !== false) {
        echo "QUERY TO 'programs': " . $query->sql . "\n";
    }
});

$request = Illuminate\Http\Request::create('/academic/timetable', 'GET');

echo "Testing /academic/timetable route with query logging...\n";

try {
    $response = $kernel->handle($request);
    echo "SUCCESS! Status: " . $response->getStatusCode() . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}