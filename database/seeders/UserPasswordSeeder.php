<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\User\Student;
use App\Helpers\PasswordHelper;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n=== Seeding User Passwords ===\n";

        $created = 0;
        $updated = 0;

        // Get all students and create user accounts with passwords
        $students = Student::with('user')->get();
        
        foreach ($students as $student) {
            if (!$student->user) {
                // Create user account for student
                $generatedPassword = PasswordHelper::generate(10);
                $studentEmail = $student->email ?? strtolower($student->first_name . '.' . $student->last_name . $student->id . '@student.schoolerp.com');
                
                $user = User::create([
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'email' => $studentEmail,
                    'password' => Hash::make($generatedPassword),
                    'temp_password' => $generatedPassword,
                    'password_generated_at' => now(),
                    'email_verified_at' => now(),
                ]);
                
                $user->assignRole('student');
                
                // Link student to user
                $student->update(['user_id' => $user->id]);
                
                $created++;
                echo "✓ Created user for student: {$student->first_name} {$student->last_name} (Password: {$generatedPassword})\n";
            } elseif (empty($student->user->temp_password)) {
                // Update existing user with password
                $generatedPassword = PasswordHelper::generate(10);
                
                $student->user->update([
                    'password' => Hash::make($generatedPassword),
                    'temp_password' => $generatedPassword,
                    'password_generated_at' => now(),
                ]);
                
                $updated++;
                echo "✓ Updated password for student: {$student->first_name} {$student->last_name} (Password: {$generatedPassword})\n";
            }
        }

        // Get all teachers and ensure they have passwords
        $teachers = User::role('teacher')->get();
        
        foreach ($teachers as $teacher) {
            if (empty($teacher->temp_password)) {
                $generatedPassword = PasswordHelper::generate(10);
                
                $teacher->update([
                    'password' => Hash::make($generatedPassword),
                    'temp_password' => $generatedPassword,
                    'password_generated_at' => now(),
                ]);
                
                $updated++;
                echo "✓ Updated password for teacher: {$teacher->name} (Password: {$generatedPassword})\n";
            }
        }

        echo "\n=== Password Seeding Complete ===\n";
        echo "✓ Created: {$created} new user accounts\n";
        echo "✓ Updated: {$updated} existing user passwords\n";
        echo "=====================================\n\n";
    }
}
