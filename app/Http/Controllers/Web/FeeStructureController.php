<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Fee\FeeStructure;
use App\Models\Fee\FeeHead;
use App\Models\Academic\Program;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        // Default per page is 10, allow user to customize
        $perPage = $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 15, 25, 50]) ? (int) $perPage : 10;

        // Default to active academic session if no filter provided
        $activeSessionId = \App\Models\Academic\AcademicSession::getCurrentAcademicSessionId();

        $query = FeeStructure::with(['program', 'feeHead'])
            ->when($request->filled('program_id'), function ($q) use ($request) {
                $q->where('program_id', $request->program_id);
            })
            ->when($request->filled('academic_year'), function ($q) use ($request) {
                $q->where('academic_year', $request->academic_year);
            })
            ->when($request->filled('fee_head_id'), function ($q) use ($request) {
                $q->where('fee_head_id', $request->fee_head_id);
            });

        // If no explicit academic_year filter, default to active session
        if (!$request->filled('academic_year') && $activeSessionId) {
            $activeSession = \App\Models\Academic\AcademicSession::find($activeSessionId);
            if ($activeSession) {
                $query->where('academic_year', $activeSession->session_name);
            }
        }

        $feeStructures = $query->paginate($perPage)->appends($request->query());

        // Get filter options
        $programs = \App\Models\Academic\Program::where('is_active', true)->get();
        $feeHeads = FeeHead::where('is_active', true)->get();
        $activeSession = \App\Models\Academic\AcademicSession::getCurrentAcademicSession();

        return view('fees.structures.index', compact('feeStructures', 'perPage', 'programs', 'feeHeads', 'activeSession'));
    }

    public function create()
    {
        $programs = \App\Models\Academic\Program::where('is_active', true)->get();
        $feeHeads = FeeHead::where('is_active', true)->get();
        $activeSession = \App\Models\Academic\AcademicSession::getCurrentAcademicSession();
        return view('fees.structures.create', compact('programs', 'feeHeads', 'activeSession'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:standards,id',
            'academic_year' => 'required|string|max:20',
            'fee_head_id' => 'required|exists:fee_heads,id',
            'amount' => 'required|numeric|min:0',
            'installments' => 'required|integer|min:1|max:12'
        ]);

        FeeStructure::create($request->all());
        return redirect()->route('fees.structures.index')->with('success', 'Fee structure created successfully');
    }

    public function show(FeeStructure $structure)
    {
        $structure->load(['program', 'feeHead']);
        return view('fees.structures.show', compact('structure'));
    }

    public function edit(FeeStructure $structure)
    {
        $programs = \App\Models\Academic\Program::where('is_active', true)->get();
        $feeHeads = FeeHead::where('is_active', true)->get();
        $activeSession = \App\Models\Academic\AcademicSession::getCurrentAcademicSession();
        return view('fees.structures.edit', compact('structure', 'programs', 'feeHeads', 'activeSession'));
    }

    public function update(Request $request, FeeStructure $structure)
    {
        $request->validate([
            'program_id' => 'required|exists:standards,id',
            'academic_year' => 'required|string|max:20',
            'fee_head_id' => 'required|exists:fee_heads,id',
            'amount' => 'required|numeric|min:0',
            'installments' => 'required|integer|min:1|max:12'
        ]);

        $structure->update($request->all());
        return redirect()->route('fees.structures.index')->with('success', 'Fee structure updated successfully');
    }

    public function destroy(FeeStructure $structure)
    {
        $structure->delete();
        return redirect()->route('fees.structures.index')->with('success', 'Fee structure deleted successfully');
    }
}