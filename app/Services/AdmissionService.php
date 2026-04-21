<?php

namespace App\Services;

use App\Models\Academic\Admission;
use App\Models\Academic\StudentDocument;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\User\Student;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdmissionService
{
    /**
     * Create a student directly from admission form data
     */
    public function createStudentFromAdmission(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            // Check if user already exists with this email
            $existingUser = User::where('email', $data['email'])->first();
            if ($existingUser) {
                throw new \Exception('A user with this email already exists. Please use a different email.');
            }
            
            // Generate random temporary password (8 characters)
            $tempPassword = bin2hex(random_bytes(4)); // 8 character random string
            
            // Create user first
            $user = User::create([
                'name' => $data['first_name'] . ' ' . ($data['middle_name'] ?? '') . ' ' . $data['last_name'],
                'email' => $data['email'],
                'password' => bcrypt($tempPassword),
                'temp_password' => $tempPassword,
                'password_generated_at' => now(),
                'role' => 'student',
            ]);
            
            // Map fields to students table
            // roll_number = admission_number (they are the same)
            $studentData = [
                'user_id' => $user->id,
                'admission_number' => $data['admission_number'],
                'roll_number' => $data['admission_number'], // Same as admission number
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'blood_group' => $data['blood_group'] ?? null,
                'religion' => $data['religion'] ?? null,
                'category' => $data['category'],
                'aadhar_number' => $data['aadhar_number'] ?? null,
                'mobile_number' => $data['mobile_number'],
                'email' => $data['email'],
                'current_address' => $data['current_address'],
                'permanent_address' => $data['permanent_address'] ?? $data['current_address'],
                'program_id' => $data['program_id'],
                'division_id' => $data['division_id'],
                'academic_session_id' => $data['academic_session_id'],
                'academic_year' => $data['academic_year'],
                'admission_date' => $data['admission_date'],
                'student_status' => $data['student_status'] ?? 'active',
                'photo_path' => $data['photo_path'] ?? null,
                'signature_path' => $data['signature_path'] ?? null,
                'marksheet_path' => $data['marksheet_path'] ?? null,
                'cast_certificate_path' => $data['cast_certificate_path'] ?? null,
            ];
            
            $student = Student::create($studentData);
            
            // Log the admission
            AuditLog::logEvent(
                $student,
                'admission_created',
                null,
                $student->toArray()
            );
            
            return $student;
        });
    }

    public function apply(array $data): Admission
    {
        return DB::transaction(function () use ($data) {
            // Generate application number
            $applicationNo = $this->generateApplicationNumber();
            
            // Filter out file path fields if they're null
            $admissionData = array_merge($data, [
                'application_no' => $applicationNo,
                'status' => 'applied'
            ]);
            
            // Only include file paths if they have values
            $fileFields = ['photo_path', 'signature_path', 'twelfth_marksheet_path', 'cast_certificate_path'];
            foreach ($fileFields as $field) {
                if (!isset($admissionData[$field]) || is_null($admissionData[$field])) {
                    unset($admissionData[$field]);
                }
            }
            
            $admission = Admission::create($admissionData);

            // Log the admission application
            AuditLog::logEvent(
                $admission,
                'applied',
                null,
                $admission->toArray()
            );

            return $admission;
        });
    }

    public function uploadDocument(
        Admission $admission,
        string $documentType,
        UploadedFile $file
    ): StudentDocument {
        return DB::transaction(function () use ($admission, $documentType, $file) {
            // Store file
            $path = $file->store("admissions/{$admission->id}/documents", 'public');
            
            // Create document record
            $document = StudentDocument::create([
                'admission_id' => $admission->id,
                'document_type' => $documentType,
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize()
            ]);

            // Log document upload
            AuditLog::logEvent(
                $document,
                'uploaded',
                null,
                $document->toArray()
            );

            return $document;
        });
    }

    public function verifyDocument(
        StudentDocument $document,
        bool $isVerified,
        string $notes = null
    ): StudentDocument {
        return DB::transaction(function () use ($document, $isVerified, $notes) {
            $oldValues = $document->toArray();
            
            $document->update([
                'is_verified' => $isVerified,
                'verified_by' => auth()->id(),
                'verified_at' => $isVerified ? now() : null,
                'verification_notes' => $notes
            ]);

            // Log document verification
            AuditLog::logEvent(
                $document,
                $isVerified ? 'verified' : 'rejected',
                $oldValues,
                $document->fresh()->toArray()
            );

            // Check if all documents are verified and update admission status
            $this->checkAndUpdateAdmissionStatus($document->admission);

            return $document;
        });
    }

    public function verifyAdmission(Admission $admission, string $notes = null): Admission
    {
        return DB::transaction(function () use ($admission, $notes) {
            if (!$admission->canBeVerified()) {
                throw new \Exception('Admission cannot be verified. Check application fee payment and document status.');
            }

            if (!$admission->hasAllDocumentsVerified()) {
                throw new \Exception('All required documents must be verified before admission verification.');
            }

            $oldValues = $admission->toArray();
            
            $admission->update([
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);

            // Log admission verification
            AuditLog::logEvent(
                $admission,
                'verified',
                $oldValues,
                $admission->fresh()->toArray()
            );

            return $admission;
        });
    }

    public function rejectAdmission(Admission $admission, string $reason): Admission
    {
        return DB::transaction(function () use ($admission, $reason) {
            $oldValues = $admission->toArray();
            
            $admission->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);

            // Log admission rejection
            AuditLog::logEvent(
                $admission,
                'rejected',
                $oldValues,
                $admission->fresh()->toArray()
            );

            return $admission;
        });
    }

    private function generateApplicationNumber(): string
    {
        $year = Carbon::now()->year;
        $prefix = "APP{$year}";
        
        // Get the last application number for this year
        $lastAdmission = Admission::where('application_no', 'like', $prefix . '%')
            ->orderBy('application_no', 'desc')
            ->first();

        if ($lastAdmission) {
            $lastNumber = (int) substr($lastAdmission->application_no, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    private function checkAndUpdateAdmissionStatus(Admission $admission): void
    {
        if ($admission->isApplied() && 
            $admission->application_fee_paid && 
            $admission->hasAllDocumentsVerified()) {
            
            // Auto-verify if all conditions are met
            $this->verifyAdmission($admission, 'Auto-verified: All documents verified and fee paid');
        }
    }

    public function getAdmissionStats(array $filters = []): array
    {
        $query = Admission::query();

        if (isset($filters['academic_session_id'])) {
            $query->where('academic_session_id', $filters['academic_session_id']);
        }

        if (isset($filters['program_id'])) {
            $query->where('program_id', $filters['program_id']);
        }

        return [
            'total' => $query->count(),
            'applied' => $query->clone()->where('status', 'applied')->count(),
            'verified' => $query->clone()->where('status', 'verified')->count(),
            'rejected' => $query->clone()->where('status', 'rejected')->count(),
            'enrolled' => $query->clone()->where('status', 'enrolled')->count(),
        ];
    }

    /**
     * Enroll a verified admission as a student
     */
    public function enrollStudent(Admission $admission): \App\Models\User\Student
    {
        return DB::transaction(function () use ($admission) {
            // Check if admission is verified
            if (!$admission->isVerified()) {
                throw new \Exception('Only verified admissions can be enrolled.');
            }

            // Check if already enrolled
            if ($admission->student_id) {
                throw new \Exception('This admission has already been enrolled.');
            }

            // Get user for this admission
            $user = \App\Models\User::where('email', $admission->email)->first();

            if (!$user) {
                // Create user account
                $user = $this->createUserFromAdmission($admission);
            }

            // Generate roll number
            $rollNumber = \App\Services\RollNumberService::generate(
                $admission->program_id,
                now()->year,
                $admission->division->division_name ?? null
            );

            // Copy document paths from admission
            $photoPath = null;
            $signaturePath = null;
            $marksheetPath = null;
            $castCertPath = null;
            
            // Get documents from admission
            $documents = $admission->documents()->get();
            foreach ($documents as $doc) {
                switch ($doc->document_type) {
                    case 'photo':
                        $photoPath = $doc->file_path;
                        break;
                    case 'signature':
                        $signaturePath = $doc->file_path;
                        break;
                    case 'twelfth_marksheet':
                        $marksheetPath = $doc->file_path;
                        break;
                    case 'cast_certificate':
                        $castCertPath = $doc->file_path;
                        break;
                }
            }

            // Create student record
            $student = \App\Models\User\Student::create([
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
                'admission_date' => now(),
                'photo_path' => $photoPath,
                'signature_path' => $signaturePath,
                'marksheet_path' => $marksheetPath,
                'cast_certificate_path' => $castCertPath,
            ]);

            // Update admission status
            $admission->update([
                'status' => 'enrolled',
                'student_id' => $student->id
            ]);

            // Log the enrollment
            AuditLog::logEvent(
                $student,
                'enrolled',
                null,
                $student->toArray()
            );

            return $student;
        });
    }

    /**
     * Create user account from admission
     */
    private function createUserFromAdmission(Admission $admission): \App\Models\User
    {
        $username = strtolower($admission->application_no);
        $tempPassword = \Illuminate\Support\Str::random(8);

        $user = \App\Models\User::create([
            'name' => $admission->first_name . ' ' . $admission->last_name,
            'email' => $admission->email,
            'password' => \Illuminate\Support\Facades\Hash::make($tempPassword),
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
                'temp_password' => $tempPassword
            ]
        );

        return $user;
    }
}