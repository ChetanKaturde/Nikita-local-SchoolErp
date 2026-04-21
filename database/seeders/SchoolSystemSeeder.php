<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\Program; // Will be renamed to Standard

/**
 * SEEDER FOR INDIAN SCHOOL SYSTEM (K-10)
 * 
 * Creates:
 * - Standards 1 to 10
 * - Divisions A, B, C for each standard
 * - Education stages (Primary, Middle, High School)
 * 
 * USAGE:
 * php artisan db:seed --class=SchoolSystemSeeder
 */
class SchoolSystemSeeder extends Seeder
{
    public function run(): void
    {
        // Define education stages
        $stages = [
            'primary' => ['name' => 'Primary School', 'standards' => [1, 2, 3, 4, 5]],
            'middle' => ['name' => 'Middle School', 'standards' => [6, 7, 8]],
            'high' => ['name' => 'High School', 'standards' => [9, 10]],
        ];

        // Create standards 1-10
        foreach ($stages as $stageKey => $stage) {
            foreach ($stage['standards'] as $standardNum) {
                $this->createStandard($standardNum, $stageKey);
            }
        }

        // Create divisions for each standard
        $divisions = ['A', 'B', 'C'];
        $standardIds = \DB::table('standards')->pluck('id')->toArray();

        foreach ($standardIds as $standardId) {
            foreach ($divisions as $division) {
                $this->createDivision($standardId, $division);
            }
        }
    }

    /**
     * Create a standard/class
     */
    private function createStandard(int $number, string $stage): void
    {
        $ordinal = $this->getOrdinal($number);
        
        \App\Models\Academic\Program::updateOrCreate(
            ['code' => 'STD' . str_pad($number, 2, '0', STR_PAD_LEFT)],
            [
                'name' => "Standard {$number}",
                'short_name' => "{$number}{$ordinal}",
                'standard_number' => $number,
                'education_stage' => $stage,
                'board_affiliation' => env('SCHOOL_BOARD', 'STATE_BOARD'),
                'medium' => env('SCHOOL_MEDIUM', 'English'),
                'is_active' => true,
            ]
        );
    }

    /**
     * Create a division within a standard
     */
    private function createDivision(int $standardId, string $divisionName): void
    {
        $academicYearId = \App\Models\Academic\AcademicYear::getCurrentAcademicYearId();
        
        if (!$academicYearId) {
            // Create current academic year if not exists
            $academicYear = \App\Models\Academic\AcademicYear::create([
                'name' => date('Y') . '-' . (date('Y') + 1),
                'start_date' => date('Y') . '-06-01',
                'end_date' => (date('Y') + 1) . '-05-31',
                'is_active' => true,
            ]);
            $academicYearId = $academicYear->id;
        }

        \App\Models\Academic\Division::updateOrCreate(
            [
                'academic_year_id' => $academicYearId,
                'division_name' => $divisionName,
                'standard_id' => $standardId,
            ],
            [
                'max_students' => 60,
                'current_strength' => 0,
                'classroom' => 'Room ' . $standardId . $divisionName,
                'shift' => 'morning',
                'is_active' => true,
            ]
        );
    }

    /**
     * Get ordinal suffix (1st, 2nd, 3rd, etc.)
     */
    private function getOrdinal(int $number): string
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if (($number % 100) >= 11 && ($number % 100) <= 13) {
            return 'th';
        }
        return $ends[$number % 10];
    }
}
