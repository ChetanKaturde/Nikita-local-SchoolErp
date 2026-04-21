<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Academic\AcademicYear;
use App\Models\Holiday;
use Carbon\Carbon;

echo "=== Test 1: List Academic Years ===\n";
$years = AcademicYear::all();
foreach ($years as $y) {
    echo "ID: {$y->id}, Session: {$y->session_name}, Start: {$y->start_date}, End: {$y->end_date}, Active: ".($y->is_active ? 'Yes' : 'No')."\n";
}

echo "\n=== Test 2: List Existing Holidays ===\n";
$holidays = Holiday::with('academicYear')->get();
foreach ($holidays as $h) {
    $ayName = $h->academicYear ? $h->academicYear->session_name : 'N/A';
    echo "ID: {$h->id}, Title: {$h->title}, Type: {$h->type}, Start: {$h->start_date}, End: {$h->end_date}, Active: ".($h->is_active ? 'Yes' : 'No').", AY: {$ayName}\n";
}

if ($holidays->isEmpty()) {
    echo "No holidays found.\n";
}

echo "\n=== Test 3: Create New Holiday ===\n";
$activeYear = AcademicYear::where('is_active', true)->first();
if ($activeYear) {
    echo "Creating holiday for academic year: {$activeYear->session_name} (ID: {$activeYear->id})\n";
    $holiday = Holiday::create([
        'title' => 'Test Holiday '.date('Y-m-d H:i:s'),
        'description' => 'Test holiday description',
        'start_date' => '2026-05-01',
        'end_date' => '2026-05-02',
        'type' => 'public_holiday',
        'is_recurring' => false,
        'academic_year_id' => $activeYear->id,
        'is_active' => true,
    ]);
    echo "Created Holiday ID: {$holiday->id}, Title: {$holiday->title}\n";
    $createdId = $holiday->id;
} else {
    echo "No active academic year found!\n";
    $createdId = null;
}

echo "\n=== Test 4: Read/Edit Holiday ===\n";
if ($createdId) {
    $holiday = Holiday::with('academicYear')->find($createdId);
    $ayName = $holiday->academicYear ? $holiday->academicYear->session_name : 'N/A';
    echo "Read Holiday - ID: {$holiday->id}, Title: {$holiday->title}, Academic Year: {$ayName}\n";

    echo "\n=== Test 5: Toggle Status ===\n";
    echo 'Status before toggle: '.($holiday->is_active ? 'Active' : 'Inactive')."\n";

    $holiday->update(['is_active' => ! $holiday->is_active]);
    $holiday->refresh();
    echo 'Status after toggle: '.($holiday->is_active ? 'Active' : 'Inactive')."\n";

    // Toggle back
    $holiday->update(['is_active' => ! $holiday->is_active]);
    $holiday->refresh();
    echo 'Status toggled back: '.($holiday->is_active ? 'Active' : 'Inactive')."\n";

    echo "\n=== Test 6: Update Holiday ===\n";
    $holiday->update(['title' => 'Updated Test Holiday']);
    $holiday->refresh();
    echo "Updated Title: {$holiday->title}\n";

    echo "\n=== Test 7: Delete Holiday ===\n";
    $deletedId = $holiday->id;
    $holiday->delete();
    echo "Deleted Holiday ID: {$deletedId}\n";

    // Verify deleted
    $deleted = Holiday::find($deletedId);
    echo 'Verified deleted: '.($deleted ? 'No' : 'Yes')."\n";
}

echo "\n=== Test 8: CheckDate AJAX Endpoint ===\n";
$testDate = Carbon::parse('2025-08-15');
if ($activeYear) {
    $isHoliday = Holiday::isDateHoliday($testDate, $activeYear->id);
    echo "Date {$testDate->toDateString()} is holiday: ".($isHoliday ? 'Yes' : 'No')."\n";

    if ($isHoliday) {
        $title = Holiday::getHolidayTitle($testDate);
        echo "Holiday title: {$title}\n";
    }

    // Test with a non-holiday date
    $testDate2 = Carbon::parse('2025-07-15');
    $isHoliday2 = Holiday::isDateHoliday($testDate2, $activeYear->id);
    echo "Date {$testDate2->toDateString()} is holiday: ".($isHoliday2 ? 'Yes' : 'No')."\n";
}

echo "\n=== All Tests Completed ===\n";
