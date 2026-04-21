<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\User\Student;
use App\Models\Academic\Division;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n=== Seeding Student Data ===\n";

        $divisions = Division::where('is_active', true)->get();
        
        if ($divisions->isEmpty()) {
            echo "Warning: No divisions found. Skipping student seeding.\n";
            return;
        }

        $firstNames = [
            'Aarav', 'Vivaan', 'Aditya', 'Vihaan', 'Arjun', 'Sai', 'Ayaan', 'Krishna',
            'Ishita', 'Diya', 'Ananya', 'Saanvi', 'Aarohi', 'Pari', 'Myra', 'Aditi',
            'Rohan', 'Karan', 'Arnav', 'Reyansh', 'Aarav', 'Ishaan', 'Shaurya', 'Atharva',
            'Priya', 'Neha', 'Pooja', 'Ritu', 'Kavya', 'Sneha', 'Riya', 'Nisha'
        ];
        
        $lastNames = [
            'Sharma', 'Verma', 'Gupta', 'Singh', 'Kumar', 'Patel', 'Reddy', 'Nair',
            'Desai', 'Joshi', 'Malhotra', 'Kapoor', 'Mehta', 'Shah', 'Rao', 'Iyer'
        ];

        $created = 0;
        $divisionIndex = 0;

        // Create 20 students per division
        foreach ($divisions as $division) {
            for ($i = 0; $i < 20; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $middleName = Str::random(1);
                
                $email = strtolower($firstName . '.' . $lastName . $i . '@student.schoolerp.com');
                
                // Check if student already exists
                $exists = Student::whereHas('user', function($q) use ($email) {
                    $q->where('email', $email);
                })->exists();
                
                if (!$exists) {
                    // Create user
                    $user = User::create([
                        'name' => $firstName . ' ' . $lastName,
                        'email' => $email,
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                    ]);
                    
                    // Assign student role
                    $user->assignRole('student');
                    
                    // Create student profile
                    Student::create([
                        'user_id' => $user->id,
                        'admission_number' => 'ADM' . str_pad(($division->id * 1000 + $i + 1), 6, '0', STR_PAD_LEFT),
                        'roll_number' => 'ROLL' . str_pad(($division->id * 100 + $i + 1), 3, '0', STR_PAD_LEFT),
                        'first_name' => $firstName,
                        'middle_name' => $middleName,
                        'last_name' => $lastName,
                        'date_of_birth' => now()->subYears(rand(17, 20))->subDays(rand(1, 365)),
                        'gender' => rand(0, 1) ? 'male' : 'female',
                        'blood_group' => ['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-'][rand(0, 7)],
                        'mobile_number' => '9' . rand(100000000, 999999999),
                        'email' => $email,
                        'current_address' => rand(100, 999) . ' Sample Street, Test City, Hyderabad',
                        'permanent_address' => rand(100, 999) . ' Sample Street, Test City, Hyderabad',
                        'program_id' => $division->program_id ?? 1,
                        'academic_year' => '2025-2026',
                        'division_id' => $division->id,
                        'academic_session_id' => $division->academic_session_id ?? 1,
                        'student_status' => 'active',
                        'admission_date' => now()->subYears(rand(1, 3)),
                    ]);
                    
                    $created++;
                }
            }
            
            $divisionIndex++;
        }

        echo "✓ Created {$created} new student records\n";
        echo "=== Student Seeding Complete ===\n\n";
    }
}
