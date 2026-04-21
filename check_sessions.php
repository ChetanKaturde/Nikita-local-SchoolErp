<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ACADEMIC SESSIONS LIST ===\n\n";

$sessions = DB::table('academic_sessions')
    ->select('id', 'session_name', 'start_date', 'end_date', 'is_active', 'created_at')
    ->orderBy('start_date', 'desc')
    ->get();

if ($sessions->count() > 0) {
    echo sprintf("%-4s | %-15s | %-12s | %-12s | %-8s\n", 'ID', 'Session Name', 'Start', 'End', 'Active');
    echo str_repeat('-', 60) . "\n";
    
    foreach ($sessions as $s) {
        echo sprintf("%-4s | %-15s | %-12s | %-12s | %-8s\n", 
            $s->id, 
            $s->session_name, 
            $s->start_date, 
            $s->end_date,
            $s->is_active ? 'YES ✓' : 'NO'
        );
    }
    
    echo "\n\n=== CURRENT ACTIVE SESSION ===\n";
    $active = DB::table('academic_sessions')->where('is_active', 1)->first();
    if ($active) {
        echo "Session: {$active->session_name}\n";
        echo "Dates:   {$active->start_date} to {$active->end_date}\n";
        echo "ID:      {$active->id}\n";
    } else {
        echo "NO ACTIVE SESSION FOUND\n";
    }
} else {
    echo "No academic sessions found in database.\n";
    echo "Create one at: /academic/sessions/create\n";
}

echo "\n=== FUNCTIONS USING ACTIVE SESSION ===\n\n";
$functions = [
    'Fee Structures' => 'Filters by active session by default',
    'Timetable (Grid/Table)' => 'Defaults to getCurrentAcademicYearId()',
    'Timetable (AJAX)' => 'Uses getCurrentAcademicYearId() for holiday checks',
    'Attendance (index)' => 'Uses getCurrentAcademicYearId() for today checks',
    'Academic Sessions List' => 'Set Active button deactivates all others',
];

foreach ($functions as $name => $desc) {
    echo "✓ {$name}: {$desc}\n";
}

echo "\n";
