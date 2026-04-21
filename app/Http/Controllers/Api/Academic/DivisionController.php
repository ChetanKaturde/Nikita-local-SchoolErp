<?php

namespace App\Http\Controllers\Api\Academic;

use App\Http\Controllers\Controller;
use App\Models\Academic\Division;
use App\Models\User\Student; // Correct Student model
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class DivisionController extends Controller
{
    /* ================================
     * GET /api/divisions/public - Public access (for admission form)
     * ================================ */
    public function indexPublic(Request $request): JsonResponse
    {
        $query = Division::where('is_active', true);

        // Filter by program_id if provided
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        $divisions = $query->get();

        return response()->json([
            'success' => true,
            'data' => $divisions
        ]);
    }

    /* ================================
     * GET /api/divisions
     * ================================ */
    public function index(Request $request): JsonResponse
    {
        $query = Division::where('is_active', true);
        
        // Filter by program_id if provided
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }
        
        $divisions = $query->get();

        return response()->json([
            'success' => true,
            'data' => $divisions
        ]);
    }

    /* ================================
     * POST /api/divisions
     * ================================ */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'division_name'    => 'required|string|max:10',
            'max_students'     => 'nullable|integer|min:1',
            'class_teacher_id' => 'nullable|exists:users,id',
            'classroom'        => 'nullable|string|max:50',
            'is_active'        => 'nullable|boolean'
        ]);

        $division = Division::create([
            'academic_year_id' => $validated['academic_year_id'],
            'division_name'    => $validated['division_name'],
            'max_students'     => $validated['max_students'] ?? 60,
            'class_teacher_id' => $validated['class_teacher_id'] ?? null,
            'classroom'        => $validated['classroom'] ?? null,
            'is_active'        => $validated['is_active'] ?? true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Division created successfully',
            'data' => $division
        ], 201);
    }

    /* ================================
     * GET /api/divisions/{id}
     * ================================ */
    public function show(int $id): JsonResponse
    {
        $division = Division::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $division
        ]);
    }

    /* ================================
     * PUT /api/divisions/{id}
     * ================================ */
    public function update(Request $request, int $id): JsonResponse
    {
        $division = Division::findOrFail($id);

        $validated = $request->validate([
            'academic_year_id' => 'sometimes|required|exists:academic_years,id',
            'division_name'    => 'sometimes|required|string|max:10',
            'max_students'     => 'nullable|integer|min:1',
            'class_teacher_id' => 'nullable|exists:users,id',
            'classroom'        => 'nullable|string|max:50',
            'is_active'        => 'nullable|boolean'
        ]);

        $division->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Division updated successfully',
            'data' => $division
        ]);
    }

    /* ================================
     * DELETE /api/divisions/{id}
     * (Soft Delete)
     * ================================ */
    public function destroy(int $id): JsonResponse
    {
        $division = Division::findOrFail($id);

        $division->update([
            'is_active' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Division deleted successfully'
        ]);
    }

    /* ================================
     * GET /api/divisions/{division}/students
     * ================================ */
   public function students(Division $division): JsonResponse
{
    $division->load('students');

    return response()->json([
        'success' => true,
        'division' => [
            'id' => $division->id,
            'name' => $division->division_name,
            'max_students' => $division->max_students
        ],
        'students' => $division->students
    ]);
}

    /* ================================
     * GET ACTIVE DIVISIONS
     * ================================ */
    public function active(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Division::where('is_active', true)->get()
        ]);
    }
    

    /* ================================
     * ASSIGN STUDENTS
     * ================================ */
    public function assignStudents(Request $request, Division $division): JsonResponse
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        Student::whereIn('id', $request->student_ids)
            ->update(['division_id' => $division->id]);

        return response()->json([
            'success' => true,
            'message' => 'Students assigned successfully'
        ]);
    }

    /* ================================
     * UPDATE CLASS TEACHER
     * ================================ */
    public function updateClassTeacher(Request $request, Division $division): JsonResponse
    {
        $request->validate([
            'class_teacher_id' => 'required|exists:users,id'
        ]);

        $division->update([
            'class_teacher_id' => $request->class_teacher_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Class teacher updated successfully'
        ]);
    }

    /* ================================
     * DIVISION CAPACITY
     * ================================ */
    public function capacity(Division $division): JsonResponse
    {
        $current = Student::where('division_id', $division->id)->count();

        return response()->json([
            'success' => true,
            'division' => $division->division_name,
            'max_students' => $division->max_students,
            'current_students' => $current,
            'available_seats' => $division->max_students - $current
        ]);
    }
    

    
}
