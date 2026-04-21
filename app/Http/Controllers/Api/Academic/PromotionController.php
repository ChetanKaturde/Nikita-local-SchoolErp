<?php

namespace App\Http\Controllers\Api\Academic;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use App\Models\Academic\StudentAcademicRecord;
use App\Models\Academic\AcademicSession;
use App\Models\Academic\Program;
use App\Models\Academic\Division;
use App\Services\PromotionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Promotion Controller
 *
 * Handles student promotion operations via API.
 *
 * Features:
 * - Eligibility checking
 * - Promotion preview
 * - Single student promotion
 * - Bulk promotion
 * - Promotion rollback
 * - Promotion history
 *
 * @package App\Http\Controllers\Api\Academic
 */
class PromotionController extends Controller
{
    public function __construct(
        private PromotionService $promotionService
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Check promotion eligibility for a student.
     *
     * @param int $studentId
     * @return JsonResponse
     */
    public function checkEligibility(int $studentId): JsonResponse
    {
        $student = Student::with('currentAcademicRecord')->findOrFail($studentId);
        $currentRecord = $student->currentAcademicRecord;

        if (!$currentRecord) {
            return response()->json([
                'success' => false,
                'message' => 'No current academic record found',
            ], 404);
        }

        $eligibility = $this->promotionService->checkEligibility($currentRecord);

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->full_name,
                    'admission_number' => $student->admission_number,
                ],
                'current_record' => [
                    'session' => $currentRecord->academicSession->name,
                    'program' => $currentRecord->program->name,
                    'year' => $currentRecord->academic_year,
                    'division' => $currentRecord->division->division_name,
                    'result_status' => $currentRecord->result_status,
                    'attendance' => $currentRecord->attendance_percentage,
                    'backlogs' => $currentRecord->backlog_count,
                ],
                'eligibility' => $eligibility,
            ],
        ]);
    }

    /**
     * Get promotion preview for a student.
     *
     * @param Request $request
     * @param int $studentId
     * @return JsonResponse
     */
    public function preview(Request $request, int $studentId): JsonResponse
    {
        $validated = $request->validate([
            'to_session_id' => 'required|exists:academic_sessions,id',
            'to_program_id' => 'required|exists:standards,id',
            'to_academic_year' => 'required|string|max:20',
            'to_division_id' => 'required|exists:divisions,id',
        ]);

        $student = Student::with('currentAcademicRecord')->findOrFail($studentId);
        
        $toSession = AcademicSession::findOrFail($validated['to_session_id']);
        $toProgram = Program::findOrFail($validated['to_program_id']);
        $toDivision = Division::findOrFail($validated['to_division_id']);

        $preview = $this->promotionService->getPromotionPreview(
            $student,
            $toSession,
            $toProgram,
            $validated['to_academic_year'],
            $toDivision
        );

        if (isset($preview['error'])) {
            return response()->json([
                'success' => false,
                'message' => $preview['error'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $preview,
        ]);
    }

    /**
     * Promote a student to the next academic session.
     *
     * @param Request $request
     * @param int $studentId
     * @return JsonResponse
     */
    public function promote(Request $request, int $studentId): JsonResponse
    {
        $validated = $request->validate([
            'to_session_id' => 'required|exists:academic_sessions,id',
            'to_program_id' => 'required|exists:standards,id',
            'to_academic_year' => 'required|string|max:20',
            'to_division_id' => 'required|exists:divisions,id',
            'is_override' => 'boolean',
            'override_reason' => 'nullable|string|max:500',
        ]);

        $student = Student::findOrFail($studentId);
        $toSession = AcademicSession::findOrFail($validated['to_session_id']);
        $toProgram = Program::findOrFail($validated['to_program_id']);
        $toDivision = Division::findOrFail($validated['to_division_id']);
        $promotedBy = Auth::user();

        try {
            $result = $this->promotionService->promoteStudent(
                $student,
                $toSession,
                $toProgram,
                $validated['to_academic_year'],
                $toDivision,
                $promotedBy,
                $validated['is_override'] ?? false,
                $validated['override_reason'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'promotion_type' => $result['promotion_type'],
                    'is_conditional' => $result['is_conditional'],
                    'new_record_id' => $result['new_record_id'],
                    'log_id' => $result['log_id'],
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get list of students eligible for promotion.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function eligibleStudents(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_session_id' => 'required|exists:academic_sessions,id',
            'program_id' => 'nullable|exists:standards,id',
        ]);

        $students = $this->promotionService->getEligibleStudents(
            $validated['from_session_id'],
            $validated['program_id'] ?? null
        );

        return response()->json([
            'success' => true,
            'data' => [
                'students' => $students->map(function ($record) {
                    return [
                        'student_id' => $record->student->id,
                        'name' => $record->student->full_name,
                        'admission_number' => $record->student->admission_number,
                        'current_session' => $record->academicSession->name,
                        'program' => $record->program->name,
                        'year' => $record->academic_year,
                        'division' => $record->division->division_name,
                        'result_status' => $record->result_status,
                        'attendance' => $record->attendance_percentage,
                        'backlogs' => $record->backlog_count,
                    ];
                }),
                'total' => $students->count(),
            ],
        ]);
    }

    /**
     * Bulk promote multiple students.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkPromote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'to_session_id' => 'required|exists:academic_sessions,id',
            'to_program_id' => 'required|exists:standards,id',
            'to_academic_year' => 'required|string|max:20',
            'to_division_id' => 'required|exists:divisions,id',
        ]);

        $toSession = AcademicSession::findOrFail($validated['to_session_id']);
        $toProgram = Program::findOrFail($validated['to_program_id']);
        $toDivision = Division::findOrFail($validated['to_division_id']);
        $promotedBy = Auth::user();

        $result = $this->promotionService->bulkPromote(
            $validated['student_ids'],
            $toSession,
            $toProgram,
            $validated['to_academic_year'],
            $toDivision,
            $promotedBy
        );

        $statusCode = $result['failed'] > 0 ? 207 : 200;

        return response()->json([
            'success' => $result['successful'] > 0,
            'message' => "{$result['successful']} students promoted, {$result['failed']} failed",
            'data' => [
                'total' => $result['total'],
                'successful' => $result['successful'],
                'failed' => $result['failed'],
                'conditional' => $result['conditional'],
                'errors' => $result['errors'],
            ],
        ], $statusCode);
    }

    /**
     * Get promotion history for a student.
     *
     * @param int $studentId
     * @return JsonResponse
     */
    public function history(int $studentId): JsonResponse
    {
        $student = Student::with([
            'promotionLogs' => function ($query) {
                $query->with([
                    'fromAcademicSession',
                    'toAcademicSession',
                    'fromProgram',
                    'toProgram',
                    'fromDivision',
                    'toDivision',
                    'promotedBy',
                    'newAcademicRecord',
                ])
                ->orderBy('created_at', 'desc');
            }
        ])->findOrFail($studentId);

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->full_name,
                    'admission_number' => $student->admission_number,
                ],
                'promotion_history' => $student->promotionLogs->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'promotion_type' => $log->promotion_type_label,
                        'from_session' => $log->fromAcademicSession->name,
                        'to_session' => $log->toAcademicSession->name,
                        'from_details' => $log->from_details,
                        'to_details' => $log->to_details,
                        'was_eligible' => $log->was_eligible,
                        'is_override' => $log->is_override,
                        'override_reason' => $log->override_reason,
                        'status' => $log->status,
                        'promoted_by' => $log->promotedBy->name ?? null,
                        'created_at' => $log->created_at->toIso8601String(),
                    ];
                }),
            ],
        ]);
    }

    /**
     * Rollback a promotion.
     *
     * @param Request $request
     * @param int $promotionLogId
     * @return JsonResponse
     */
    public function rollback(Request $request, int $promotionLogId): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $rolledBackBy = Auth::user();

        try {
            $result = $this->promotionService->rollbackPromotion(
                $promotionLogId,
                $rolledBackBy
            );

            return response()->json([
                'success' => true,
                'message' => $result['message'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
