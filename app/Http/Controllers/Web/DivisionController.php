<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Academic\Division;
use App\Models\Academic\Program;
use App\Models\Academic\AcademicSession;
use App\Models\User;
use App\Models\User\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class DivisionController extends Controller
{
    public function index(Request $request): View
    {
        // Default per page is 15, allow user to customize
        $perPage = $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50]) ? (int) $perPage : 15;

        $query = Division::with(['program', 'session', 'classTeacher', 'teachers'])
            ->withCount(['students' => fn($q) => $q->where('student_status', 'active')])
            ->when($request->filled('program_id'), fn($q) => $q->where('program_id', $request->program_id))
            ->when($request->filled('session_id'), fn($q) => $q->where('session_id', $request->session_id))
            ->when($request->filled('search'), fn($q) => $q->search($request->search))
            ->when($request->filled('status'), fn($q) => $q->where('is_active', $request->status === 'active'))
            ->latest();

        $divisions = $query->paginate($perPage)->appends($request->query());
        $programs = Program::where('is_active', true)->get();
        $sessions = AcademicSession::where('is_active', true)->get();

        return view('academic.divisions.index', compact('divisions', 'programs', 'sessions', 'perPage'));
    }

    public function create(): View
    {
        $programs = Program::where('is_active', true)->get();
        $sessions = AcademicSession::where('is_active', true)->get();
        $teachers = User::role('teacher')->get();

        return view('academic.divisions.create', compact('programs', 'sessions', 'teachers'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Get current academic year
        $academicYear = \App\Models\Academic\AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            return back()->with('error', 'No active academic year found. Please set an academic year first.');
        }

        $validated = $request->validate([
            'program_id' => 'required|exists:standards,id',
            'session_id' => 'required|exists:academic_sessions,id',
            'division_name' => [
                'required',
                'string',
                'max:10',
                // Unique combination of program, session, and division name
                \Illuminate\Validation\Rule::unique('divisions')
                    ->where(function ($query) use ($request) {
                        return $query->where('program_id', $request->program_id)
                                    ->where('session_id', $request->session_id)
                                    ->where('division_name', $request->division_name);
                    })
            ],
            'max_students' => 'required|integer|min:1|max:200',
            'class_teacher_id' => 'nullable|exists:users,id',
            'classroom' => 'nullable|string|max:50',
            'is_active' => 'boolean'
        ], [
            'division_name.unique' => 'This division name already exists for the selected program and session. Please choose a different name.'
        ]);

        // Add academic_year_id to the validated data
        $validated['academic_year_id'] = $academicYear->id;

        Division::create($validated);

        return redirect()->route('academic.divisions.index')->with('success', 'Division created successfully!');
    }

    public function show(Division $division): View
    {
        $division->load(['program', 'session', 'classTeacher', 'teachers', 'timetables' => fn($q) => $q->with('subject'), 'students' => fn($q) => $q->where('student_status', 'active')]);
        return view('academic.divisions.show', compact('division'));
    }

    public function edit(Division $division): View
    {
        $programs = Program::where('is_active', true)->get();
        $sessions = AcademicSession::where('is_active', true)->get();
        $teachers = User::role('teacher')->get();

        return view('academic.divisions.edit', compact('division', 'programs', 'sessions', 'teachers'));
    }

    public function update(Request $request, Division $division): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:standards,id',
            'session_id' => 'required|exists:academic_sessions,id',
            'division_name' => [
                'required',
                'string',
                'max:10',
                // Unique combination, excluding current division
                \Illuminate\Validation\Rule::unique('divisions')
                    ->where(function ($query) use ($request, $division) {
                        return $query->where('program_id', $request->program_id)
                                    ->where('session_id', $request->session_id)
                                    ->where('division_name', $request->division_name)
                                    ->where('id', '!=', $division->id);
                    })
            ],
            'max_students' => 'required|integer|min:1|max:200',
            'class_teacher_id' => 'nullable|exists:users,id',
            'classroom' => 'nullable|string|max:50',
            'is_active' => 'boolean'
        ], [
            'division_name.unique' => 'This division name already exists for the selected program and session. Please choose a different name.'
        ]);

        $division->update($validated);

        return redirect()->route('academic.divisions.index')->with('success', 'Division updated successfully!');
    }

    public function toggleStatus(Division $division): RedirectResponse
    {
        $division->update(['is_active' => !$division->is_active]);
        return redirect()->back()->with('success', 'Division status updated!');
    }

    public function destroy(Division $division): RedirectResponse
    {
        if ($division->students()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete division with assigned students!');
        }

        // Check for timetable records
        if ($division->timetables()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete division with assigned timetable entries! Please delete the timetable first.');
        }

        // Check for attendance records
        if (\App\Models\Academic\Attendance::where('division_id', $division->id)->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete division with attendance records!');
        }

        $division->delete();
        return redirect()->route('academic.divisions.index')->with('success', 'Division deleted successfully!');
    }

    public function assignStudents(Request $request, Division $division): RedirectResponse
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        if (!$division->canAssignStudents($validated['student_ids'])) {
            return redirect()->back()->with('error', "Insufficient capacity! Available: {$division->available_seats}");
        }

        Student::whereIn('id', $validated['student_ids'])->update(['division_id' => $division->id]);

        return redirect()->back()->with('success', count($validated['student_ids']) . ' students assigned successfully!');
    }

    public function removeStudent(Division $division, Student $student): RedirectResponse
    {
        $student->update(['division_id' => null]);
        return redirect()->back()->with('success', 'Student removed from division!');
    }

    public function unassignedStudents(Request $request): JsonResponse
    {
        $students = Student::whereNull('division_id')
            ->where('student_status', 'active')
            ->when($request->filled('program_id'), fn($q) => $q->where('program_id', $request->program_id))
            ->select('id', 'first_name', 'last_name', 'admission_number')
            ->get();

        return response()->json($students);
    }
}
