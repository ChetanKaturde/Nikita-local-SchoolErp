<?php

namespace App\Services;

use App\Models\Academic\Admission;
use App\Models\User\Student;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentService
{
    public function enrollFromAdmission(int $admissionId): Student
    {
        return DB::transaction(function () use ($admissionId) {
            $admission = Admission::findOrFail($admissionId);
            
            if (!$admission->canBeEnrolled()) {
                throw new \Exception('Admission must be verified before enrollment');
            }

            if ($admission->student_id) {
                throw new \Exception('Student already enrolled from this admission');
            }

            // Create user account
            $user = $this->createUserAccount($admission);
            
            // Generate roll number
            $rollNumber = RollNumberService::generate(
                $admission->program_id,
                now()->year,
                $admission->division->division_name
            );

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'admission_number' => $admission->application_no,
                'roll_number' => $rollNumber,
                'first_name' => $admission->first_name,
                'middle_name' => $admission->middle_name,
                'last_name' => $admission->last_name,
                'date_of_birth' => $admission->date_of_birth,
                'gender' => $admission->gender,
                'blood_group' => $admission->blood_group,
                'religion' => $admission->religion,
                // 'caste' field removed, category used instead
                'category' => $admission->category,
                'aadhar_number' => $admission->aadhar_number,
                'mobile_number' => $admission->mobile_number,
                'email' => $admission->email,
                'current_address' => $admission->current_address,
                'permanent_address' => $admission->permanent_address,
                'program_id' => $admission->program_id,
                'academic_year' => $admission->academic_year,
                'division_id' => $admission->division_id,
                'academic_session_id' => $admission->academic_session_id,
                'student_status' => 'active',
                'admission_date' => now()
            ]);

            // Link admission to student
            $admission->update([
                'status' => 'enrolled',
                'student_id' => $student->id
            ]);

            // Log enrollment
            AuditLog::logEvent(
                $student,
                'enrolled',
                null,
                array_merge($student->toArray(), [
                    'admission_id' => $admission->id,
                    'roll_number' => $rollNumber
                ])
            );

            AuditLog::logEvent(
                $admission,
                'enrolled',
                ['status' => 'verified'],
                ['status' => 'enrolled', 'student_id' => $student->id]
            );

            return $student;
        });
    }

    public function updateEditableFields(Student $student, array $data): Student
    {
        return DB::transaction(function () use ($student, $data) {
            $oldValues = $student->toArray();
            
            // Only allow editing of specific fields after enrollment
            $editableFields = [
                'mobile_number',
                'email',
                'current_address',
                'blood_group'
            ];

            $updateData = array_intersect_key($data, array_flip($editableFields));
            
            $student->update($updateData);

            // Log the update
            AuditLog::logEvent(
                $student,
                'profile_updated',
                $oldValues,
                $student->fresh()->toArray()
            );

            return $student;
        });
    }

    private function createUserAccount(Admission $admission): User
    {
        // Generate username from admission number
        $username = strtolower($admission->application_no);

        // Generate temporary password
        $tempPassword = Str::random(8);

        $user = User::create([
            'name' => $admission->full_name,
            'email' => $admission->email,
            'password' => Hash::make($tempPassword),
            'temp_password' => $tempPassword, // Plain text for admin viewing
            'password_generated_at' => now(), // Track when generated
            'email_verified_at' => now()
        ]);

        // Assign student role
        $user->assignRole('student');

        // Log user creation
        AuditLog::logEvent(
            $user,
            'created',
            null,
            [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'student',
                'admission_id' => $admission->id,
                'temp_password' => $tempPassword // This should be sent via email in production
            ]
        );

        return $user;
    }

    public function getStudentProfile(int $studentId): array
    {
        $student = Student::with([
            'user',
            'program',
            'division',
            'academicSession',
            'guardians',
            'fees'
        ])->findOrFail($studentId);

        return [
            'student' => $student,
            'admission' => Admission::where('student_id', $studentId)->first(),
            'documents' => $student->admission?->documents ?? [],
            'audit_logs' => AuditLog::forModel($student)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
        ];
    }
}