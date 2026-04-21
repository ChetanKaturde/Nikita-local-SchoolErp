<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Academic\Program;
use App\Models\Academic\Department;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProgramController extends Controller
{
    /**
     * Display a listing of programs
     */
    public function index(Request $request): View
    {
        // Default per page is 15, allow user to customize
        $perPage = $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50]) ? (int) $perPage : 15;

        $query = Program::with('department')
            ->withCount(['students' => function($q) {
                $q->where('student_status', 'active');
            }])
            ->when($request->filled('department_id'), function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                          ->orWhere('code', 'like', '%' . $request->search . '%');
                });
            })
            ->latest();

        $programs = $query->paginate($perPage)->appends($request->query());
        $departments = Department::where('is_active', true)->get();

        return view('academic.programs.index', compact('programs', 'departments', 'perPage'));
    }

    /**
     * Show form to create new program
     */
    public function create(): View
    {
        $departments = Department::where('is_active', true)->get();
        return view('academic.programs.create', compact('departments'));
    }

    /**
     * Store newly created program
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:standards,name',
            'short_name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:standards,code',
            'department_id' => 'required|exists:departments,id',
            'duration_years' => 'required|integer|min:1|max:5',
            'total_semesters' => 'nullable|integer|min:1|max:10',
            'total_seats' => 'nullable|integer|min:1',
            'program_type' => 'required|in:undergraduate,postgraduate,diploma',
            'university_affiliation' => 'nullable|string|max:100',
            'university_program_code' => 'nullable|string|max:20',
            'default_grade_scale_name' => 'required|string|max:100'
        ]);

        Program::create([
            'name' => $validated['name'],
            'short_name' => $validated['short_name'],
            'code' => $validated['code'],
            'department_id' => $validated['department_id'],
            'duration_years' => $validated['duration_years'],
            'total_semesters' => $validated['total_semesters'] ?? ($validated['duration_years'] * 2),
            'total_seats' => $validated['total_seats'] ?? null,
            'program_type' => $validated['program_type'],
            'university_affiliation' => $validated['university_affiliation'],
            'university_program_code' => $validated['university_program_code'],
            'default_grade_scale_name' => $validated['default_grade_scale_name'],
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect()
            ->route('academic.programs.index')
            ->with('success', 'Program created successfully!');
    }

    /**
     * Show program details
     */
    public function show(Program $program): View
    {
        $program->load(['department', 'students']);
        return view('academic.programs.show', compact('program'));
    }

    /**
     * Show edit form
     */
    public function edit(Program $program): View
    {
        $departments = Department::where('is_active', true)->get();
        return view('academic.programs.edit', compact('program', 'departments'));
    }

    /**
     * Update program
     */
    public function update(Request $request, Program $program): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:standards,name,' . $program->id,
            'short_name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:standards,code,' . $program->id,
            'department_id' => 'required|exists:departments,id',
            'duration_years' => 'required|integer|min:1|max:5',
            'total_semesters' => 'nullable|integer|min:1|max:10',
            'program_type' => 'required|in:undergraduate,postgraduate,diploma',
            'university_affiliation' => 'nullable|string|max:100',
            'university_program_code' => 'nullable|string|max:20',
            'default_grade_scale_name' => 'required|string|max:100'
        ]);

        $program->update([
            'name' => $validated['name'],
            'short_name' => $validated['short_name'],
            'code' => $validated['code'],
            'department_id' => $validated['department_id'],
            'duration_years' => $validated['duration_years'],
            'total_semesters' => $validated['total_semesters'] ?? ($validated['duration_years'] * 2),
            'program_type' => $validated['program_type'],
            'university_affiliation' => $validated['university_affiliation'],
            'university_program_code' => $validated['university_program_code'],
            'default_grade_scale_name' => $validated['default_grade_scale_name'],
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect()
            ->route('academic.programs.index')
            ->with('success', 'Program updated successfully!');
    }

    /**
     * Deactivate/Activate program
     */
    public function toggleStatus(Program $program): RedirectResponse
    {
        $program->update(['is_active' => !$program->is_active]);
        
        $status = $program->is_active ? 'activated' : 'deactivated';
        return redirect()
            ->back()
            ->with('success', "Program {$status} successfully!");
    }

    /**
     * Delete program (soft delete recommended)
     */
    public function destroy(Program $program): RedirectResponse
    {
        if ($program->students()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete program with enrolled students!');
        }

        $program->delete();
        return redirect()
            ->route('academic.programs.index')
            ->with('success', 'Program deleted successfully!');
    }
}