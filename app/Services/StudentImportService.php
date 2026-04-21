<?php

namespace App\Services;

use App\Models\User\Student;
use App\Models\User;
use App\Repositories\StudentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Student Import Service
 * 
 * Handles bulk import of students from Excel/CSV files
 * Features:
 * - Validation before import
 * - Batch processing for performance
 * - Error reporting
 * - Rollback on failure
 * - Duplicate detection
 */
class StudentImportService
{
    private array $errors = [];
    private array $warnings = [];
    private int $successCount = 0;
    private int $failureCount = 0;

    public function __construct(
        private StudentRepository $repository,
        private RollNumberService $rollNumberService
    ) {}

    /**
     * Import students from Excel/CSV file
     * 
     * @param string $filePath
     * @return array Import results
     */
    public function importFromFile(string $filePath): array
    {
        $this->resetCounters();
        
        try {
            // Read file
            $data = Excel::toArray([], $filePath)[0];
            
            // Remove header row
            $headers = array_shift($data);
            
            // Validate headers
            if (!$this->validateHeaders($headers)) {
                throw new \Exception('Invalid file format. Please use the provided template.');
            }
            
            // Process in batches for better performance
            $batches = array_chunk($data, 100);
            
            foreach ($batches as $batchIndex => $batch) {
                $this->processBatch($batch, $headers, $batchIndex);
            }
            
            return $this->getImportResults();
            
        } catch (\Exception $e) {
            $this->errors[] = [
                'row' => 'General',
                'error' => $e->getMessage()
            ];
            
            return $this->getImportResults();
        }
    }

    /**
     * Process a batch of student records
     * 
     * @param array $batch
     * @param array $headers
     * @param int $batchIndex
     * @return void
     */
    private function processBatch(array $batch, array $headers, int $batchIndex): void
    {
        DB::beginTransaction();
        
        try {
            foreach ($batch as $index => $row) {
                $rowNumber = ($batchIndex * 100) + $index + 2; // +2 for header and 0-index
                
                // Convert row to associative array
                $data = array_combine($headers, $row);
                
                // Validate row
                $validator = $this->validateRow($data, $rowNumber);
                
                if ($validator->fails()) {
                    $this->failureCount++;
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'data' => $data,
                        'errors' => $validator->errors()->all()
                    ];
                    continue;
                }
                
                // Check for duplicates
                if ($this->isDuplicate($data)) {
                    $this->failureCount++;
                    $this->warnings[] = [
                        'row' => $rowNumber,
                        'message' => 'Duplicate student detected (email or mobile already exists)'
                    ];
                    continue;
                }
                
                // Create student
                try {
                    $this->createStudent($data);
                    $this->successCount++;
                } catch (\Exception $e) {
                    $this->failureCount++;
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate file headers
     * 
     * @param array $headers
     * @return bool
     */
    private function validateHeaders(array $headers): bool
    {
        $requiredHeaders = [
            'first_name',
            'last_name',
            'date_of_birth',
            'gender',
            'program_id',
            'division_id',
            'academic_session_id',
            'academic_year',
            'admission_date',
        ];
        
        foreach ($requiredHeaders as $required) {
            if (!in_array($required, $headers)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Validate a single row
     * 
     * @param array $data
     * @param int $rowNumber
     * @return \Illuminate\Validation\Validator
     */
    private function validateRow(array $data, int $rowNumber): \Illuminate\Validation\Validator
    {
        return Validator::make($data, [
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'category' => 'required|in:general,obc,sc,st,vjnt,nt,ews,sbc',
            'mobile_number' => 'nullable|regex:/^[6-9]\d{9}$/',
            'email' => 'nullable|email',
            'program_id' => 'required|exists:standards,id',
            'division_id' => 'required|exists:divisions,id',
            'academic_session_id' => 'required|exists:academic_sessions,id,is_active,1',
            'academic_year' => 'required|string|max:20',
            'admission_date' => 'required|date|before_or_equal:today',
        ]);
    }

    /**
     * Check if student is duplicate
     * 
     * @param array $data
     * @return bool
     */
    private function isDuplicate(array $data): bool
    {
        $query = Student::query();
        
        if (!empty($data['email'])) {
            $query->orWhere('email', $data['email']);
        }
        
        if (!empty($data['mobile_number'])) {
            $query->orWhere('mobile_number', $data['mobile_number']);
        }
        
        if (!empty($data['aadhar_number'])) {
            $query->orWhere('aadhar_number', $data['aadhar_number']);
        }
        
        return $query->exists();
    }

    /**
     * Create student from import data
     * 
     * @param array $data
     * @return Student
     */
    private function createStudent(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            // Create user account
            $user = User::create([
                'name' => trim($data['first_name'] . ' ' . $data['last_name']),
                'email' => $data['email'] ?: $data['first_name'] . '.' . $data['last_name'] . '@student.local',
                'password' => Hash::make('student123'),
            ]);
            
            $user->assignRole('student');
            
            // Generate admission and roll numbers
            $admissionNumber = 'ADM' . date('Y') . str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT);
            
            $rollNumber = $this->rollNumberService->generate(
                $data['program_id'],
                $data['academic_year'],
                $data['division_name'] ?? 'A'
            );
            
            // Create student
            return $this->repository->create(array_merge($data, [
                'user_id' => $user->id,
                'admission_number' => $admissionNumber,
                'roll_number' => $rollNumber,
                'student_status' => 'active',
            ]));
        });
    }

    /**
     * Get import results
     * 
     * @return array
     */
    public function getImportResults(): array
    {
        return [
            'success' => $this->successCount,
            'failed' => $this->failureCount,
            'total' => $this->successCount + $this->failureCount,
            'errors' => $this->errors,
            'warnings' => $this->warnings,
        ];
    }

    /**
     * Reset counters
     * 
     * @return void
     */
    private function resetCounters(): void
    {
        $this->errors = [];
        $this->warnings = [];
        $this->successCount = 0;
        $this->failureCount = 0;
    }

    /**
     * Validate import file before processing
     * 
     * @param string $filePath
     * @return array Validation results
     */
    public function validateImportFile(string $filePath): array
    {
        try {
            $data = Excel::toArray([], $filePath)[0];
            $headers = array_shift($data);
            
            if (!$this->validateHeaders($headers)) {
                return [
                    'valid' => false,
                    'message' => 'Invalid file format',
                    'errors' => ['Headers do not match template']
                ];
            }
            
            $errors = [];
            $rowCount = 0;
            
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2;
                $rowData = array_combine($headers, $row);
                
                $validator = $this->validateRow($rowData, $rowNumber);
                
                if ($validator->fails()) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'errors' => $validator->errors()->all()
                    ];
                }
                
                $rowCount++;
                
                // Limit validation to first 100 rows for performance
                if ($rowCount >= 100) {
                    break;
                }
            }
            
            return [
                'valid' => empty($errors),
                'message' => empty($errors) ? 'File is valid' : 'File contains errors',
                'total_rows' => count($data),
                'validated_rows' => $rowCount,
                'errors' => $errors
            ];
            
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => 'Error reading file',
                'errors' => [$e->getMessage()]
            ];
        }
    }
}
