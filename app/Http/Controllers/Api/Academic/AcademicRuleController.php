<?php

namespace App\Http\Controllers\Api\Academic;

use App\Http\Controllers\Controller;
use App\Models\Academic\AcademicRule;
use App\Models\Academic\RuleConfiguration;
use App\Services\RuleEngineService;
use App\Services\ResultEvaluationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Academic Rule Controller
 *
 * Handles management of configurable academic rules via API.
 *
 * Features:
 * - Rule listing and filtering
 * - Rule value configuration
 * - Override management with approval
 * - Rule evaluation testing
 *
 * @package App\Http\Controllers\Api\Academic
 */
class AcademicRuleController extends Controller
{
    public function __construct(
        private RuleEngineService $ruleEngine,
        private ResultEvaluationService $resultEvaluation
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * List all academic rules.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'nullable|in:result,attendance,promotion,fee,atkt,examination,general',
            'active' => 'nullable|boolean',
            'search' => 'nullable|string|max:100',
        ]);

        $query = AcademicRule::query();

        // Filter by category
        if (isset($validated['category'])) {
            $query->byCategory($validated['category']);
        }

        // Filter by active status
        if (isset($validated['active'])) {
            $query->where('is_active', $validated['active']);
        }

        // Search by name or code
        if (!empty($validated['search'])) {
            $query->where(function ($q) use ($validated) {
                $q->where('name', 'like', "%{$validated['search']}%")
                  ->orWhere('rule_code', 'like', "%{$validated['search']}%")
                  ->orWhere('description', 'like', "%{$validated['search']}%");
            });
        }

        $rules = $query->ordered()->paginate(25);

        return response()->json([
            'success' => true,
            'data' => [
                'rules' => $rules->items(),
                'pagination' => [
                    'current_page' => $rules->currentPage(),
                    'per_page' => $rules->perPage(),
                    'total' => $rules->total(),
                    'last_page' => $rules->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Get rule details by code.
     *
     * @param string $ruleCode
     * @return JsonResponse
     */
    public function show(string $ruleCode): JsonResponse
    {
        $rule = AcademicRule::byCode($ruleCode)->first();

        if (!$rule) {
            return response()->json([
                'success' => false,
                'message' => "Rule not found: {$ruleCode}",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'rule' => $rule,
                'configurations' => $rule->configurations()->with([
                    'academicSession',
                    'program',
                    'department',
                ])->get(),
            ],
        ]);
    }

    /**
     * Get rules by category.
     *
     * @param string $category
     * @param Request $request
     * @return JsonResponse
     */
    public function getByCategory(string $category, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => 'nullable|exists:academic_sessions,id',
            'program_id' => 'nullable|exists:standards,id',
        ]);

        $rules = $this->ruleEngine->getRulesByCategory(
            $category,
            $validated['session_id'] ?? null,
            $validated['program_id'] ?? null
        );

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'rules' => collect($rules)->map(function ($ruleData) {
                    return [
                        'rule_code' => $ruleData['rule']->rule_code,
                        'name' => $ruleData['rule']->name,
                        'description' => $ruleData['rule']->description,
                        'value_type' => $ruleData['rule']->value_type_label,
                        'value' => $ruleData['value'],
                        'formatted_value' => $ruleData['rule']->formatted_value,
                        'has_configuration' => $ruleData['configuration'] !== null,
                        'is_override' => $ruleData['configuration']?->is_override ?? false,
                    ];
                })->values(),
            ],
        ]);
    }

    /**
     * Get common academic rules.
     *
     * @return JsonResponse
     */
    public function getCommonRules(): JsonResponse
    {
        $rules = $this->ruleEngine->getCommonRules();

        return response()->json([
            'success' => true,
            'data' => $rules,
        ]);
    }

    /**
     * Update or create a rule configuration.
     *
     * @param Request $request
     * @param string $ruleCode
     * @return JsonResponse
     */
    public function configure(Request $request, string $ruleCode): JsonResponse
    {
        $validated = $request->validate([
            'value' => 'required',
            'session_id' => 'nullable|exists:academic_sessions,id',
            'program_id' => 'nullable|exists:standards,id',
            'department_id' => 'nullable|exists:departments,id',
            'is_override' => 'boolean',
            'override_reason' => 'nullable|string|max:500',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after:effective_from',
        ]);

        $rule = AcademicRule::byCode($ruleCode)->first();

        if (!$rule) {
            return response()->json([
                'success' => false,
                'message' => "Rule not found: {$ruleCode}",
            ], 404);
        }

        // Check if override requires approval
        $requiresApproval = $validated['is_override'] ?? false;
        $approvedBy = $requiresApproval ? Auth::id() : null;

        try {
            $config = $this->ruleEngine->setConfiguration(
                $ruleCode,
                $validated['value'],
                $validated['session_id'] ?? null,
                $validated['program_id'] ?? null,
                $validated['department_id'] ?? null,
                $validated['is_override'] ?? false,
                $validated['override_reason'] ?? null,
                $approvedBy
            );

            return response()->json([
                'success' => true,
                'message' => 'Rule configuration updated successfully',
                'data' => [
                    'configuration' => $config,
                    'rule' => $rule,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Test rule evaluation for a student.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function testEvaluation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'examination_id' => 'required|exists:examinations,id',
        ]);

        $student = \App\Models\User\Student::with('currentAcademicRecord')->findOrFail(
            $validated['student_id']
        );

        if (!$student->currentAcademicRecord) {
            return response()->json([
                'success' => false,
                'message' => 'No current academic record found',
            ], 404);
        }

        $examination = \App\Models\Result\Examination::findOrFail(
            $validated['examination_id']
        );

        $result = $this->resultEvaluation->evaluateResult(
            $student,
            $examination,
            $student->currentAcademicRecord
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Get rule configuration history.
     *
     * @param string $ruleCode
     * @return JsonResponse
     */
    public function history(string $ruleCode): JsonResponse
    {
        $rule = AcademicRule::byCode($ruleCode)->first();

        if (!$rule) {
            return response()->json([
                'success' => false,
                'message' => "Rule not found: {$ruleCode}",
            ], 404);
        }

        $configurations = $rule->configurations()
            ->withTrashed()
            ->with([
                'academicSession',
                'program',
                'department',
                'createdBy',
                'updatedBy',
                'overrideApprovedBy',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'rule' => $rule,
                'history' => $configurations->map(function ($config) {
                    return [
                        'id' => $config->id,
                        'value' => $config->value,
                        'typed_value' => $config->typed_value,
                        'context' => $config->context_description,
                        'is_override' => $config->is_override,
                        'override_reason' => $config->override_reason,
                        'effective_from' => $config->effective_from,
                        'effective_to' => $config->effective_to,
                        'created_by' => $config->createdBy->name ?? null,
                        'created_at' => $config->created_at->toIso8601String(),
                        'updated_by' => $config->updatedBy->name ?? null,
                        'updated_at' => $config->updated_at->toIso8601String(),
                        'deleted_at' => $config->deleted_at?->toIso8601String(),
                    ];
                }),
            ],
        ]);
    }

    /**
     * Delete a rule configuration.
     *
     * @param int $configId
     * @return JsonResponse
     */
    public function deleteConfiguration(int $configId): JsonResponse
    {
        $config = RuleConfiguration::findOrFail($configId);
        $ruleCode = $config->academicRule->rule_code;

        $config->delete();

        // Clear cache
        $this->ruleEngine->clearCache(
            $ruleCode,
            $config->academic_session_id,
            $config->program_id,
            $config->department_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Rule configuration deleted successfully',
        ]);
    }

    /**
     * Restore a deleted rule configuration.
     *
     * @param int $configId
     * @return JsonResponse
     */
    public function restoreConfiguration(int $configId): JsonResponse
    {
        $config = RuleConfiguration::withTrashed()->findOrFail($configId);
        $config->restore();

        // Clear cache
        $this->ruleEngine->clearCache(
            $config->academicRule->rule_code,
            $config->academic_session_id,
            $config->program_id,
            $config->department_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Rule configuration restored successfully',
        ]);
    }
}
