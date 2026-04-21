<?php

namespace App\Http\Controllers;

use App\Models\Library\Book;
use App\Models\Library\BookIssue;
use App\Models\User\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LibrarianDashboardController extends Controller
{
    /**
     * Display librarian dashboard
     */
    public function index()
    {
        $librarian = auth()->user();
        
        // Get dashboard statistics
        $totalBooks = Book::count();
        $availableBooks = Book::where('available_copies', '>', 0)->sum('available_copies');
        $issuedBooks = BookIssue::where('status', 'issued')->count();
        $overdueBooks = BookIssue::where('status', 'issued')
            ->where('due_date', '<', now())
            ->count();
        
        // Notify admin about overdue books
        $this->notifyAdminOverdueBooks($overdueBooks);
        
        // Recent issued books
        $recentIssues = BookIssue::with(['book', 'student'])
            ->where('status', 'issued')
            ->latest()
            ->limit(10)
            ->get();
        
        // Overdue books
        $overdueList = BookIssue::with(['book', 'student'])
            ->where('status', 'issued')
            ->where('due_date', '<', now())
            ->latest('due_date')
            ->limit(10)
            ->get();
        
        // Students with active issues
        $studentsWithBooks = BookIssue::where('status', 'issued')
            ->distinct('student_id')
            ->count('student_id');
        
        return view('librarian.dashboard', compact(
            'librarian',
            'totalBooks',
            'availableBooks',
            'issuedBooks',
            'overdueBooks',
            'recentIssues',
            'overdueList',
            'studentsWithBooks'
        ));
    }

    /**
     * Display issued books list
     */
    public function issuedBooks(Request $request)
    {
        $query = BookIssue::with(['book', 'student']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        $issuedBooks = $query->latest()->paginate(20);
        $students = Student::where('student_status', 'active')->get();
        
        return view('librarian.issued-books', compact('issuedBooks', 'students'));
    }

    /**
     * Display students list
     */
    public function students(Request $request)
    {
        $query = Student::with(['user', 'division']);
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('admission_number', 'like', "%{$request->search}%");
            });
        }
        
        $students = $query->where('student_status', 'active')->paginate(20);
        
        return view('librarian.students', compact('students'));
    }

    /**
     * Display student details with contact info
     */
    public function studentDetails(Student $student)
    {
        $student->load(['user', 'division', 'studentProfile']);
        
        // Get student's issued books
        $issuedBooks = BookIssue::with('book')
            ->where('student_id', $student->id)
            ->latest()
            ->get();
        
        return view('librarian.student-details', compact('student', 'issuedBooks'));
    }

    /**
     * Send message/contact to student
     */
    public function contactStudent(Request $request, Student $student)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
            'contact_method' => 'required|in:email,sms',
        ]);
        
        // Create notification for student
        \App\Models\StudentNotification::create([
            'student_id' => $student->id,
            'title' => 'Message from Librarian',
            'message' => $validated['message'],
            'type' => 'library',
            'is_read' => false,
        ]);
        
        // Also send notification to admin
        \App\Models\StudentNotification::create([
            'student_id' => $student->id,
            'title' => 'Library Message (Admin Copy)',
            'message' => 'Librarian sent the following message: ' . $validated['message'],
            'type' => 'library',
            'is_read' => false,
        ]);
        
        // If email selected, send email to student
        if ($validated['contact_method'] === 'email' && $student->email) {
            // You can add email sending logic here
            // Mail::to($student->email)->send(new LibrarianMessage($validated['message']));
        }
        
        // Also notify admin via email (optional)
        $adminEmail = \App\Models\User::role('admin')->first()?->email;
        if ($adminEmail && $validated['contact_method'] === 'email') {
            // Mail::to($adminEmail)->send(new LibrarianMessageCopy($validated['message'], $student));
        }
        
        return redirect()->back()->with('success', 'Message sent to student successfully! A copy has been sent to admin.');
    }

    /**
     * Librarian profile
     */
    public function profile()
    {
        $librarian = auth()->user();
        return view('librarian.profile', compact('librarian'));
    }

    /**
     * Update librarian profile
     */
    public function updateProfile(Request $request)
    {
        $librarian = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $librarian->id,
            'phone' => 'nullable|string|max:15',
        ]);
        
        $librarian->update($validated);
        
        return redirect()->route('librarian.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $librarian = auth()->user();

        if (!Hash::check($validated['current_password'], $librarian->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $librarian->update([
            'password' => Hash::make($validated['new_password']),
            'temp_password' => $validated['new_password'], // Update for admin view
            'password_generated_at' => now(),
        ]);

        return redirect()->route('librarian.profile')->with('success', 'Password changed successfully!');
    }

    /**
     * Notify admin about overdue books (can be called from dashboard or scheduled task)
     */
    private function notifyAdminOverdueBooks($overdueCount)
    {
        if ($overdueCount > 0) {
            // Get all admins
            $admins = \App\Models\User::role('admin')->get();
            
            foreach ($admins as $admin) {
                \App\Models\StudentNotification::create([
                    'student_id' => $admin->id,
                    'title' => 'Library: Overdue Books Alert',
                    'message' => "There are {$overdueCount} books currently overdue in the library. Please follow up with students.",
                    'type' => 'library',
                    'is_read' => false,
                ]);
            }
        }
    }
}
