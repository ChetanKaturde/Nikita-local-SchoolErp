<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PromotionService;
use App\Models\Academic\AcademicSession;
use App\Models\Academic\Program;
use App\Models\Academic\Division;
use App\Models\Academic\PromotionLog;
use App\Models\Academic\StudentAcademicRecord;
use App\Models\User\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Promotion Controller for Web Interface
 * 
 * Handles student promotion operations including:
 * - Displaying eligible students for promotion
 * - Preview promotion results before execution
 * - Bulk and individual promotion
 * - Promotion history and rollback
 */
class PromotionController extends Controller
{
    protected PromotionService $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    /**
     * Display eligible students for promotion.
     */
    public function index(Request $request)
    {
        $fromSessionId = $request->filled('from_session_id') 
            ? $request->from_session_id 
            : AcademicSession::where('is_active', true)->first()?->id;

        $programId = $request->filled('program_id') ? $request->program_id : null;
        $divisionId = $request->filled('division_id') ? $request->division_id : null;

        // Get all active sessions for dropdown
        $sessions = AcademicSession::active()->orderBy('start_date', 'desc')->get();
        
        // Get programs for dropdown
        $programs = Program::where('is_active', true)->get();
        
        // Get divisions based on selected program
        $divisions = $divisionId 
            ? Division::where('id', $divisionId)->get()
            : ($programId 
                ? Division::where('program_id', $programId)->where('is_active', true)->get()
                : Division::where('is_active', true)->get());
        
        // Get target divisions (for next session promotion)
        // Show all active divisions for promotion target selection
        $targetDivisions = Division::where('is_active', true)
            ->with('program')
            ->orderBy('program_id')
            ->orderBy('division_name')
            ->get();

        // Get eligible students
        $eligibleStudents = collect();
        if ($fromSessionId) {
            $eligibleStudents = $this->promotionService->getEligibleStudents($fromSessionId, $programId);
            
            // Filter by division if selected
            if ($divisionId) {
                $eligibleStudents = $eligibleStudents->filter(function ($record) use ($divisionId) {
                    return $record->division_id == $divisionId;
                })->values();
            }
        }

        // Get all students with their records for display (including ineligible) - paginated
        $perPage = $request->filled('per_page') ? (int)$request->per_page : 12;
        
        $allRecords = StudentAcademicRecord::with(['student', 'program', 'division', 'academicSession'])
            ->when($fromSessionId, fn($q) => $q->forSession($fromSessionId))
            ->when($programId, fn($q) => $q->where('program_id', $programId))
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->whereNotIn('result_status', [
                StudentAcademicRecord::STATUS_TC_ISSUED,
                StudentAcademicRecord::STATUS_COMPLETED,
            ])
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        // Get all records count for eligibility checking (without pagination)
        $allRecordsCount = StudentAcademicRecord::with(['student', 'program', 'division', 'academicSession'])
            ->when($fromSessionId, fn($q) => $q->forSession($fromSessionId))
            ->when($programId, fn($q) => $q->where('program_id', $programId))
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->whereNotIn('result_status', [
                StudentAcademicRecord::STATUS_TC_ISSUED,
                StudentAcademicRecord::STATUS_COMPLETED,
            ])
            ->whereNull('deleted_at')
            ->count();

        // Check eligibility for each record on current page
        $studentsWithEligibility = $allRecords->getCollection()->map(function ($record) {
            $eligibility = $this->promotionService->checkEligibility($record);
            return [
                'record' => $record,
                'student' => $record->student,
                'program' => $record->program,
                'division' => $record->division,
                'academicSession' => $record->academicSession,
                'result_status' => $record->result_status,
                'attendance_percentage' => $record->attendance_percentage,
                'backlog_count' => $record->backlog_count,
                'promotion_status' => $record->promotion_status,
                'eligible' => $eligibility['eligible'],
                'conditional' => $eligibility['conditional'],
                'reasons' => $eligibility['reasons'],
                'warnings' => $eligibility['warnings'],
                'promotion_type' => $this->getPromotionType($record, $eligibility),
            ];
        });

        // Get next session info
        $nextSession = null;
        $nextAcademicYear = null;
        if ($fromSessionId) {
            $currentSession = AcademicSession::find($fromSessionId);
            if ($currentSession) {
                $nextSession = AcademicSession::where('start_date', '>', $currentSession->start_date)
                    ->orderBy('start_date', 'asc')
                    ->first();
                // Calculate next academic year (e.g., 2025-26 -> 2026-27)
                if (preg_match('/(\d{4})-(\d{2})/', $currentSession->session_name, $matches)) {
                    $startYear = (int)$matches[1];
                    $endYear = (int)$matches[2] + 1;
                    $nextAcademicYear = ($startYear + 1) . '-' . str_pad($endYear, 2, '0', STR_PAD_LEFT);
                }
            }
        }

        return view('academic.promotions.index', compact(
            'sessions',
            'programs',
            'divisions',
            'targetDivisions',
            'studentsWithEligibility',
            'allRecords',
            'fromSessionId',
            'programId',
            'divisionId',
            'nextSession',
            'nextAcademicYear'
        ));
    }

    /**
     * Preview promotion results before execution.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'integer|exists:students,id',
            'to_session_id' => 'required|integer|exists:academic_sessions,id',
            'to_program_id' => 'required|integer|exists:standards,id',
            'to_division_id' => 'required|integer|exists:divisions,id',
            'to_academic_year' => 'required|string',
        ]);

        $studentIds = $request->student_ids;
        $toSessionId = $request->to_session_id;
        $toProgramId = $request->to_program_id;
        $toDivisionId = $request->to_division_id;
        $toAcademicYear = $request->to_academic_year;

        $toSession = AcademicSession::findOrFail($toSessionId);
        $toProgram = Program::findOrFail($toProgramId);
        $toDivision = Division::findOrFail($toDivisionId);

        // Validate program doesn't change for each student
        $errors = [];
        foreach ($studentIds as $studentId) {
            $student = Student::findOrFail($studentId);
            $currentRecord = StudentAcademicRecord::where('student_id', $studentId)
                ->where('academic_session_id', $toSessionId)
                ->first();
            
            if ($currentRecord) {
                $currentProgram = Program::find($currentRecord->program_id);
                if ($currentProgram && $currentProgram->id != $toProgramId) {
                    $errors[] = "Student {$student->first_name} {$student->last_name} (ID: {$student->id}) is in program '{$currentProgram->name}' but target is '{$toProgram->name}'. Program change is not allowed during promotion.";
                }
            }
        }
        
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Program change is not allowed during promotion. Only division can be changed.',
                'errors' => $errors,
            ], 422);
        }

        $previews = [];
        foreach ($studentIds as $studentId) {
            $student = Student::findOrFail($studentId);
            $preview = $this->promotionService->getPromotionPreview(
                $student,
                $toSession,
                $toProgram,
                $toAcademicYear,
                $toDivision
            );
            $previews[] = $preview;
        }

        return response()->json([
            'success' => true,
            'previews' => $previews,
            'target' => [
                'session' => $toSession->name,
                'program' => $toProgram->name,
                'division' => $toDivision->division_name,
                'academic_year' => $toAcademicYear,
            ],
        ]);
    }

    /**
     * Handle bulk promotion.
     */
    public function promote(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'integer|exists:students,id',
            'to_session_id' => 'required|integer|exists:academic_sessions,id',
            'to_program_id' => 'required|integer|exists:standards,id',
            'to_division_id' => 'required|integer|exists:divisions,id',
            'to_academic_year' => 'required|string',
        ]);

        $studentIds = $request->student_ids;
        $toSession = AcademicSession::findOrFail($request->to_session_id);
        $toProgram = Program::findOrFail($request->to_program_id);
        $toDivision = Division::findOrFail($request->to_division_id);
        $toAcademicYear = $request->to_academic_year;
        $promotedBy = Auth::user();

        // Validate program doesn't change for each student
        $programChangeErrors = [];
        foreach ($studentIds as $studentId) {
            $student = Student::findOrFail($studentId);
            $currentRecord = StudentAcademicRecord::where('student_id', $studentId)
                ->where('academic_session_id', $request->to_session_id)
                ->first();
            
            if ($currentRecord) {
                $currentProgram = Program::find($currentRecord->program_id);
                if ($currentProgram && $currentProgram->id != $toProgram->id) {
                    $programChangeErrors[] = "{$student->first_name} {$student->last_name}";
                }
            }
        }
        
        if (!empty($programChangeErrors)) {
            return back()->with('error', 'Program change is not allowed during promotion. Only division can be changed. Student(s) affected: ' . implode(', ', $programChangeErrors));
        }

        try {
            $results = $this->promotionService->bulkPromote(
                $studentIds,
                $toSession,
                $toProgram,
                $toAcademicYear,
                $toDivision,
                $promotedBy
            );

            Log::info('Bulk promotion completed', [
                'user_id' => $promotedBy->id,
                'total' => $results['total'],
                'successful' => $results['successful'],
                'failed' => $results['failed'],
            ]);

            return redirect()->route('academic.promotions.history')
                ->with('success', "Promotion completed: {$results['successful']} students promoted successfully. {$results['failed']} failed.");
        } catch (\Exception $e) {
            Log::error('Bulk promotion failed', [
                'user_id' => $promotedBy->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Promotion failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle individual student promotion.
     */
    public function promoteStudent(Request $request, $studentId)
    {
        $request->validate([
            'to_session_id' => 'required|integer|exists:academic_sessions,id',
            'to_program_id' => 'required|integer|exists:standards,id',
            'to_division_id' => 'required|integer|exists:divisions,id',
            'to_academic_year' => 'required|string',
        ]);

        $student = Student::findOrFail($studentId);
        $toSession = AcademicSession::findOrFail($request->to_session_id);
        $toProgram = Program::findOrFail($request->to_program_id);
        $toDivision = Division::findOrFail($request->to_division_id);
        $toAcademicYear = $request->to_academic_year;
        $promotedBy = Auth::user();

        // Validate program doesn't change
        $currentRecord = StudentAcademicRecord::where('student_id', $studentId)
            ->where('academic_session_id', $request->to_session_id)
            ->first();
        
        if ($currentRecord) {
            $currentProgram = Program::find($currentRecord->program_id);
            if ($currentProgram && $currentProgram->id != $toProgram->id) {
                return back()->with('error', 'Program change is not allowed during promotion. Only division can be changed.');
            }
        }

        try {
            $result = $this->promotionService->promoteStudent(
                $student,
                $toSession,
                $toProgram,
                $toAcademicYear,
                $toDivision,
                $promotedBy,
                $request->boolean('is_override', false),
                $request->string('override_reason', null)
            );

            Log::info('Individual promotion completed', [
                'student_id' => $studentId,
                'user_id' => $promotedBy->id,
                'promotion_type' => $result['promotion_type'],
            ]);

            return redirect()->route('academic.promotions.history')
                ->with('success', "Student {$student->full_name} promoted successfully as {$result['promotion_type']}.");
        } catch (\Exception $e) {
            Log::error('Individual promotion failed', [
                'student_id' => $studentId,
                'user_id' => $promotedBy->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Promotion failed: ' . $e->getMessage());
        }
    }

    /**
     * Display promotion history.
     */
    public function history(Request $request)
    {
        $search = $request->filled('search') ? $request->search : null;
        $fromSessionId = $request->filled('from_session_id') ? $request->from_session_id : null;
        $promotionType = $request->filled('promotion_type') ? $request->promotion_type : null;

        $query = PromotionLog::with([
            'student',
            'fromAcademicSession',
            'fromProgram',
            'fromDivision',
            'toAcademicSession',
            'toProgram',
            'toDivision',
            'promotedBy',
        ])->orderBy('created_at', 'desc');

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        if ($fromSessionId) {
            $query->where('from_academic_session_id', $fromSessionId);
        }

        if ($promotionType) {
            $query->where('promotion_type', $promotionType);
        }

        $promotions = $query->paginate(20);
        
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();

        return view('academic.promotions.history', compact(
            'promotions',
            'sessions',
            'search',
            'fromSessionId',
            'promotionType'
        ));
    }

    /**
     * Rollback a promotion.
     */
    public function rollback(Request $request, $promotionId)
    {
        $promotion = PromotionLog::findOrFail($promotionId);
        $rolledBackBy = Auth::user();

        try {
            $result = $this->promotionService->rollbackPromotion($promotionId, $rolledBackBy);

            Log::warning('Promotion rolled back', [
                'promotion_id' => $promotionId,
                'student_id' => $promotion->student_id,
                'rolled_back_by' => $rolledBackBy->id,
            ]);

            return redirect()->route('academic.promotions.history')
                ->with('success', 'Promotion rolled back successfully.');
        } catch (\Exception $e) {
            Log::error('Promotion rollback failed', [
                'promotion_id' => $promotionId,
                'user_id' => $rolledBackBy->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Rollback failed: ' . $e->getMessage());
        }
    }

    /**
     * Determine promotion type label for display.
     */
    private function getPromotionType(StudentAcademicRecord $record, array $eligibility): string
    {
        if ($record->result_status === StudentAcademicRecord::STATUS_FAIL) {
            return 'Fail';
        }
        
        if ($eligibility['conditional']) {
            return 'ATKT';
        }

        if ($record->result_status === StudentAcademicRecord::STATUS_ATKT) {
            return 'ATKT';
        }

        return 'Normal';
    }

    /**
     * Get available divisions for a program and session.
     */
    public function getDivisions(Request $request)
    {
        $programId = $request->filled('program_id') ? $request->program_id : null;
        $sessionId = $request->filled('session_id') ? $request->session_id : null;

        $divisions = Division::where('is_active', true)
            ->when($programId, fn($q) => $q->where('program_id', $programId))
            ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
            ->get(['id', 'division_name', 'program_id', 'session_id']);

        return response()->json([
            'success' => true,
            'divisions' => $divisions,
        ]);
    }

    /**
     * Get next session information.
     */
    public function getNextSession(Request $request)
    {
        $sessionId = $request->filled('session_id') ? $request->session_id : null;

        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'No session selected',
            ]);
        }

        $currentSession = AcademicSession::find($sessionId);
        
        if (!$currentSession) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found',
            ]);
        }

        $nextSession = AcademicSession::where('start_date', '>', $currentSession->start_date)
            ->orderBy('start_date', 'asc')
            ->first();

        // Calculate next academic year
        $nextAcademicYear = null;
        if (preg_match('/(\d{4})-(\d{2})/', $currentSession->session_name, $matches)) {
            $startYear = (int)$matches[1];
            $endYear = (int)$matches[2] + 1;
            $nextAcademicYear = ($startYear + 1) . '-' . str_pad($endYear, 2, '0', STR_PAD_LEFT);
        }

        return response()->json([
            'success' => true,
            'next_session' => $nextSession ? [
                'id' => $nextSession->id,
                'name' => $nextSession->name,
            ] : null,
            'next_academic_year' => $nextAcademicYear,
        ]);
    }
}
