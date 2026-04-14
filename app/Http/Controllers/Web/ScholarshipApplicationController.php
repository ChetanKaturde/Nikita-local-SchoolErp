<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Fee\Scholarship;
use App\Models\Fee\ScholarshipApplication;
use App\Models\Fee\StudentFee;
use App\Models\User\Student;
use Illuminate\Http\Request;

class ScholarshipApplicationController extends Controller
{
    public function index()
    {
        $applications = ScholarshipApplication::with(['student', 'scholarship'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('fees.scholarship-applications.index', compact('applications'));
    }

    public function create()
    {
        $scholarships = Scholarship::where('is_active', true)->get();
        $students = Student::where('student_status', 'active')->get();
        return view('fees.scholarship-applications.create', compact('scholarships', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'scholarship_id' => 'required|exists:scholarships,id',
            'documents' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'remarks' => 'nullable|string'
        ]);

        if ($request->hasFile('documents')) {
            $validated['document_path'] = $request->file('documents')->store('scholarships', 'public');
        }

        $validated['status'] = 'pending';
        ScholarshipApplication::create($validated);

        return redirect()->route('fees.scholarship-applications.index')
            ->with('success', 'Scholarship application submitted');
    }

    public function approve($id)
    {
        $application = ScholarshipApplication::with(['student', 'scholarship'])->findOrFail($id);
        $application->update(['status' => 'approved', 'approved_at' => now()]);

        // Apply scholarship to student fees
        $this->applyScholarship($application->student_id, $application->scholarship);

        return redirect()->back()->with('success', 'Scholarship approved and applied');
    }

    public function reject(Request $request, $id)
    {
        $application = ScholarshipApplication::findOrFail($id);
        $application->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        return redirect()->back()->with('success', 'Scholarship application rejected');
    }

    private function applyScholarship($studentId, $scholarship)
    {
        $fees = StudentFee::where('student_id', $studentId)
            ->where('status', '!=', 'paid')
            ->get();

        foreach ($fees as $fee) {
            $discountAmount = $scholarship->type === 'percentage'
                ? ($fee->total_amount * $scholarship->value / 100)
                : $scholarship->value;

            $fee->discount_amount += $discountAmount;
            $fee->final_amount = $fee->total_amount - $fee->discount_amount;
            $fee->outstanding_amount = $fee->final_amount - $fee->paid_amount;
            $fee->save();
        }
    }
}
