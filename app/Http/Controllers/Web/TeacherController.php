<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Academic\Department;
use App\Models\Academic\Division;
use App\Helpers\PasswordHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        // Default per page is 15, allow user to customize
        $perPage = $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50]) ? (int) $perPage : 15;

        $sortBy = $request->query('sort', 'created_at');
        $sortDir = $request->query('dir', 'desc');
        $allowedSorts = ['name', 'email', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc';

        $teachers = User::role('teacher')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)->appends($request->query());

        return view('dashboard.teachers.index', compact('teachers', 'sortBy', 'sortDir', 'perPage'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $divisions = Division::where('is_active', true)->get();
        return view('dashboard.teachers.create', compact('departments', 'divisions'));
    }

    public function store(Request $request)
    {
        // If password is not provided, generate one
        if ($request->filled('password')) {
            $password = $request->input('password');
            $generatedPassword = $password;
        } else {
            $generatedPassword = PasswordHelper::generate(10);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:15',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($generatedPassword),
            'temp_password' => $generatedPassword,
            'password_generated_at' => now(),
        ]);

        $user->assignRole('teacher');

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('teachers', 'public');
            $user->update(['photo_path' => $path]);
        }

        return redirect()->route('dashboard.teachers.index')
            ->with('success', 'Teacher created successfully! Password: ' . $generatedPassword);
    }

    public function show(User $teacher)
    {
        return view('dashboard.teachers.show', compact('teacher'));
    }

    public function edit(User $teacher)
    {
        $departments = Department::where('is_active', true)->get();
        $divisions = Division::where('is_active', true)->get();
        return view('dashboard.teachers.edit', compact('teacher', 'departments', 'divisions'));
    }

    public function update(Request $request, User $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->id,
            'password' => $request->filled('password') ? 'required|string|min:8|confirmed' : 'nullable',
            'department_id' => 'nullable|exists:departments,id',
            'division_id' => 'nullable|exists:divisions,id',
            'phone' => 'nullable|string|max:15',
            'photo' => 'nullable|image|max:2048',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Update password only if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
            // Store plain text password in temp_password for admin to view
            $updateData['temp_password'] = $validated['password'];
            $updateData['password_generated_at'] = now();
        }

        $teacher->update($updateData);

        // Handle division assignment via teacher_assignments table
        if ($request->filled('division_id')) {
            // Remove existing division assignments
            \App\Models\TeacherAssignment::where('teacher_id', $teacher->id)
                ->where('assignment_type', 'division')
                ->delete();
            
            // Create new division assignment
            \App\Models\TeacherAssignment::create([
                'teacher_id' => $teacher->id,
                'division_id' => $validated['division_id'],
                'assignment_type' => 'division',
                'is_primary' => true,
                'is_active' => true,
            ]);
        } else {
            // Remove all division assignments if no division selected
            \App\Models\TeacherAssignment::where('teacher_id', $teacher->id)
                ->where('assignment_type', 'division')
                ->delete();
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('teachers', 'public');
            $teacher->update(['photo_path' => $path]);
        }

        return redirect()->route('dashboard.teachers.index')
            ->with('success', 'Teacher updated successfully!' . ($request->filled('password') ? ' New password: ' . $validated['password'] : ''));
    }

    public function destroy(User $teacher)
    {
        // Check for timetable assignments
        if ($teacher->timetables()->exists()) {
            return redirect()
                ->route('dashboard.teachers.index')
                ->with('error', 'Teacher is assigned to timetable. Please remove assignments first.');
        }

        // Check for class teacher assignment
        if ($teacher->assignedDivision()->exists()) {
            return redirect()
                ->route('dashboard.teachers.index')
                ->with('error', 'Teacher is assigned as class teacher. Please reassign the class first.');
        }

        $teacher->delete();
        return redirect()->route('dashboard.teachers.index')
            ->with('success', 'Teacher deleted successfully!');
    }
}
