<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\Timetable;
use App\Models\Academic\Division;
use App\Models\Academic\Subject;
use App\Models\User;
use Carbon\Carbon;

class TimetableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n=== Seeding Timetable Data ===\n";

        // Get active divisions
        $divisions = Division::where('is_active', true)->get();
        
        // Get all subjects
        $subjects = Subject::all();
        
        // Get all teachers
        $teachers = User::role('teacher')->get();
        
        if ($divisions->isEmpty() || $subjects->isEmpty() || $teachers->isEmpty()) {
            echo "Warning: Missing divisions, subjects, or teachers. Skipping timetable seeding.\n";
            return;
        }

        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $timeSlots = [
            ['start' => '09:00:00', 'end' => '09:50:00', 'period' => 'Period 1'],
            ['start' => '09:50:00', 'end' => '10:40:00', 'period' => 'Period 2'],
            ['start' => '10:40:00', 'end' => '11:30:00', 'period' => 'Period 3'],
            ['start' => '11:30:00', 'end' => '12:20:00', 'period' => 'Period 4'],
            ['start' => '12:20:00', 'end' => '13:10:00', 'period' => 'Period 5'],
            ['start' => '14:00:00', 'end' => '14:50:00', 'period' => 'Period 6'],
            ['start' => '14:50:00', 'end' => '15:40:00', 'period' => 'Period 7'],
        ];

        $rooms = ['A-101', 'A-102', 'B-101', 'B-102', 'LAB-S1', 'LAB-S2', 'LAB-S3', 'C-201'];
        
        $count = 0;
        $created = 0;

        // Create weekly timetables for each division
        foreach ($divisions as $division) {
            // Assign 5-6 subjects per division
            $divisionSubjects = $subjects->random(min(6, $subjects->count()));
            
            foreach ($daysOfWeek as $dayIndex => $day) {
                // Skip Sunday (not in array) and maybe Saturday half-day
                if ($day === 'saturday') {
                    // Only 3 periods on Saturday
                    $daySlots = array_slice($timeSlots, 0, 3);
                } else {
                    // Full day - 6 periods
                    $daySlots = array_slice($timeSlots, 0, 6);
                }
                
                foreach ($daySlots as $slotIndex => $slot) {
                    $subject = $divisionSubjects->random();
                    $teacher = $teachers->random();
                    $room = $rooms[array_rand($rooms)];
                    
                    $existing = Timetable::where('division_id', $division->id)
                        ->where('day_of_week', $day)
                        ->where('start_time', $slot['start'])
                        ->exists();
                    
                    if (!$existing) {
                        Timetable::create([
                            'division_id' => $division->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacher->id,
                            'day_of_week' => $day,
                            'start_time' => $slot['start'],
                            'end_time' => $slot['end'],
                            'period_name' => $slot['period'],
                            'room_number' => $room,
                            'academic_year_id' => $division->academic_year_id ?? 1,
                            'is_break_time' => false,
                            'is_active' => true,
                            'status' => 'active',
                        ]);
                        $created++;
                    }
                    $count++;
                }
            }
        }

        echo "✓ Created {$created} new timetable entries\n";
        echo "✓ Total timetable entries processed: {$count}\n";
        echo "=== Timetable Seeding Complete ===\n\n";
    }
}
