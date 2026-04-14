<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\Student;
use App\Helpers\PasswordHelper;
use App\Mail\SendCredentials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    /**
     * Display user credentials management page (Admin Only)
     */
    public function credentials(Request $request)
    {
        // Only admins can view this page
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access. Only administrators can view this page.');
        }

        // Get search parameters
        $studentSearch = $request->get('student_search', '');
        $teacherSearch = $request->get('teacher_search', '');
        $staffSearch = $request->get('staff_search', '');
        $divisionFilter = $request->get('division_filter', '');

        // Get all divisions for filter dropdown
        $divisions = \App\Models\Academic\Division::where('is_active', true)->get();

        // Get all students with their user accounts
        $studentsQuery = Student::with(['user', 'division', 'program'])
            ->where('student_status', 'active')
            ->orderBy('created_at', 'desc')
            ->distinct();

        // Apply student search
        if ($studentSearch) {
            $studentsQuery->where(function($q) use ($studentSearch) {
                $q->where('first_name', 'like', "%{$studentSearch}%")
                  ->orWhere('last_name', 'like', "%{$studentSearch}%")
                  ->orWhere('admission_number', 'like', "%{$studentSearch}%")
                  ->orWhere('roll_number', 'like', "%{$studentSearch}%")
                  ->orWhereHas('user', function($uq) use ($studentSearch) {
                      $uq->where('email', 'like', "%{$studentSearch}%");
                  });
            });
        }

        // Apply division filter
        if ($divisionFilter) {
            $studentsQuery->where('division_id', $divisionFilter);
        }

        $students = $studentsQuery->paginate(20)->appends($request->query());

        // Get all staff users (admin, principal, accountant, librarian, office staff, etc.)
        $staffRoles = ['admin', 'principal', 'accountant', 'librarian', 'office', 'staff', 'accounts_staff', 'admission_officer', 'hod_commerce', 'hod_science', 'hod_management', 'hod_arts', 'class_teacher', 'subject_teacher'];
        
        $staffQuery = User::whereHas('roles', function($q) use ($staffRoles) {
                $q->whereIn('name', $staffRoles);
            })
            ->with('roles');

        // Apply staff search
        if ($staffSearch) {
            $staffQuery->where(function($q) use ($staffSearch) {
                $q->where('name', 'like', "%{$staffSearch}%")
                  ->orWhere('email', 'like', "%{$staffSearch}%");
            });
        }

        $staff = $staffQuery->paginate(20)->appends($request->query());

        // Get teacher role users separately
        $teachersQuery = User::role('teacher')
            ->whereDoesntHave('roles', function($q) use ($staffRoles) {
                $q->whereIn('name', $staffRoles);
            })
            ->with('roles');

        // Apply teacher search
        if ($teacherSearch) {
            $teachersQuery->where(function($q) use ($teacherSearch) {
                $q->where('name', 'like', "%{$teacherSearch}%")
                  ->orWhere('email', 'like', "%{$teacherSearch}%");
            });
        }

        $teachers = $teachersQuery->paginate(20)->appends($request->query());

        return view('admin.credentials', compact('students', 'teachers', 'staff', 'divisions', 'studentSearch', 'teacherSearch', 'staffSearch', 'divisionFilter'));
    }

    /**
     * Reset password for a user (Admin Only)
     */
    public function resetPassword(Request $request, $userId)
    {
        // Only admins can reset passwords
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return back()->with('error', 'Unauthorized access. Only administrators can reset passwords.');
        }

        $user = User::findOrFail($userId);
        
        // Generate new password
        $newPassword = PasswordHelper::generate(10);
        
        $user->update([
            'password' => Hash::make($newPassword),
            'temp_password' => $newPassword,
            'password_generated_at' => now(),
        ]);

        return back()->with('success', 'Password reset successfully for ' . $user->name . '. New password: ' . $newPassword);
    }

    /**
     * Send credentials via email (Admin Only)
     */
    public function sendCredentials(Request $request, $userId)
    {
        // Only admins can send credentials
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return back()->with('error', 'Unauthorized access. Only administrators can send credentials.');
        }

        $user = User::findOrFail($userId);

        // Check if user has a temp password
        if (!$user->temp_password) {
            return back()->with('error', 'No temporary password found for this user. Please reset password first.');
        }

        // Determine user type
        $userType = 'user';
        if ($user->hasRole('student')) {
            $userType = 'student';
        } elseif ($user->hasRole('teacher')) {
            $userType = 'teacher';
        } elseif ($user->hasRole('staff')) {
            $userType = 'staff';
        }

        try {
            // Send email
            Mail::to($user->email)->send(new SendCredentials($user, $user->temp_password, $userType));

            return back()->with('success', 'Credentials sent successfully to ' . $user->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send credentials: ' . $e->getMessage());
        }
    }

    /**
     * Export credentials to CSV (Admin Only)
     */
    public function exportCredentials(Request $request)
    {
        // Only admins can export credentials
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access. Only administrators can export credentials.');
        }

        $type = $request->get('type', 'students');
        
        if ($type === 'students') {
            $users = Student::with(['user', 'division'])
                ->where('student_status', 'active')
                ->get()
                ->map(function($student) {
                    return [
                        'Name' => $student->first_name . ' ' . $student->last_name,
                        'Email' => $student->user->email ?? 'N/A',
                        'Admission No' => $student->admission_number,
                        'Roll No' => $student->roll_number,
                        'Division' => $student->division->division_name ?? 'N/A',
                        'Password' => $student->user->temp_password ?? 'Not Set',
                        'Generated On' => $student->user->password_generated_at?->format('d M Y') ?? 'N/A',
                    ];
                });
        } else {
            $users = User::role('teacher')
                ->with('roles')
                ->get()
                ->map(function($teacher) {
                    return [
                        'Name' => $teacher->name,
                        'Email' => $teacher->email,
                        'Role' => $teacher->roles->first()->name ?? 'N/A',
                        'Password' => $teacher->temp_password ?? 'Not Set',
                        'Generated On' => $teacher->password_generated_at?->format('d M Y') ?? 'N/A',
                    ];
                });
        }

        // Simple CSV export
        $filename = 'Credentials_' . $type . '_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, array_keys($users->first() ?? []));
        
        // Add data
        foreach ($users as $user) {
            fputcsv($output, $user);
        }
        
        fclose($output);
        exit;
    }
}
