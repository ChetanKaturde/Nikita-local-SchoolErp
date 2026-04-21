<?php

namespace App\Http\Controllers\Api\Academic;

use App\Http\Controllers\Controller;
use App\Models\Academic\Subject;
use App\Models\Academic\Program;
use App\Models\Academic\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubjectController extends Controller
{
    /**
     * Get all subjects with optional filters
     * GET /api/subjects
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Default per page is 20, allow user to customize
            $perPage = $request->input('per_page', 20);
            $perPage = in_array($perPage, [10, 20, 25, 50, 100]) ? $perPage : 20;

            $query = Subject::with(['program', 'academicYear']);

            // Filter by program
            if ($request->filled('program_id')) {
                $query->where('program_id', $request->program_id);
            }

            // Filter by semester
            if ($request->filled('semester')) {
                $query->where('semester', $request->semester);
            }

            // Filter by academic year
            if ($request->filled('academic_year_id')) {
                $query->where('academic_year_id', $request->academic_year_id);
            }

            // Filter by type (theory/practical)
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            // Search by name or code
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('code', 'like', '%' . $request->search . '%');
                });
            }

            // Only active subjects
            if ($request->boolean('active')) {
                $query->where('is_active', true);
            }

            $subjects = $query->orderBy('semester')->orderBy('name')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $subjects,
                'pagination' => [
                    'current_page' => $subjects->currentPage(),
                    'per_page' => $subjects->perPage(),
                    'total' => $subjects->total(),
                    'last_page' => $subjects->lastPage(),
                    'from' => $subjects->firstItem(),
                    'to' => $subjects->lastItem()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subjects',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single subject details
     * GET /api/subjects/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $subject = Subject::with(['program', 'academicYear', 'timetables'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $subject
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subject not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Create new subject
     * POST /api/subjects
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:subjects,code',
            'program_id' => 'required|exists:standards,id',
            'academic_year_id' => 'required|exists:academic_sessions,id',
            'semester' => 'required|integer|min:1|max:8',
            'type' => 'required|in:theory,practical,both',
            'credit' => 'required|numeric|min:0|max:10',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $subject = Subject::create([
                'name' => $request->name,
                'code' => $request->code,
                'program_id' => $request->program_id,
                'academic_year_id' => $request->academic_year_id,
                'semester' => $request->semester,
                'type' => $request->type,
                'credit' => $request->credit,
                'is_active' => $request->has('is_active') ? $request->is_active : true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subject created successfully',
                'data' => $subject->load(['program', 'academicYear'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subject',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update existing subject
     * PUT /api/subjects/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $subject = Subject::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subject not found',
                'error' => $e->getMessage()
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:20|unique:subjects,code,' . $id,
            'program_id' => 'sometimes|required|exists:standards,id',
            'academic_year_id' => 'sometimes|required|exists:academic_sessions,id',
            'semester' => 'sometimes|required|integer|min:1|max:8',
            'type' => 'sometimes|required|in:theory,practical,both',
            'credit' => 'sometimes|required|numeric|min:0|max:10',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $subject->update($request->only([
                'name',
                'code',
                'program_id',
                'academic_year_id',
                'semester',
                'type',
                'credit',
                'is_active'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Subject updated successfully',
                'data' => $subject->fresh(['program', 'academicYear'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update subject',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete subject
     * DELETE /api/subjects/{id}
     */
    public function destroy($id): JsonResponse
    {
        try {
            $subject = Subject::findOrFail($id);

            // Check if subject has any timetables
            if ($subject->timetables()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete subject with associated timetables'
                ], 409);
            }

            $subject->delete();

            return response()->json([
                'success' => true,
                'message' => 'Subject deleted successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subject not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get subjects by program
     * GET /api/programs/{programId}/subjects
     */
    public function getByProgram($programId): JsonResponse
    {
        try {
            $program = Program::findOrFail($programId);

            $subjects = Subject::where('program_id', $programId)
                ->with('academicYear')
                ->orderBy('semester')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'program' => $program,
                    'subjects' => $subjects
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Program not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get subjects by semester
     * GET /api/programs/{programId}/semester/{semester}/subjects
     */
    public function getBySemester(Request $request, $programId, $semester): JsonResponse
    {
        try {
            $subjects = Subject::where('program_id', $programId)
                ->where('semester', $semester)
                ->with('academicYear')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $subjects
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subjects',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle subject status
     * PATCH /api/subjects/{id}/toggle-status
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $subject = Subject::findOrFail($id);

            $subject->update(['is_active' => !$subject->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Subject status updated successfully',
                'data' => $subject
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subject not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
