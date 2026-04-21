<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Fee\FeeHead;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FeeHeadController extends Controller
{
    /**
     * Display a listing of fee heads.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 15, 25, 50]) ? (int) $perPage : 10;

        $query = FeeHead::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $feeHeads = $query->orderBy('name')->paginate($perPage)->appends($request->query());

        return view('fees.fee-heads.index', compact('feeHeads', 'perPage'));
    }

    /**
     * Show the form for creating a new fee head.
     */
    public function create()
    {
        return view('fees.fee-heads.create');
    }

    /**
     * Store a newly created fee head.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fee_heads,name',
            'code' => 'required|string|max:10|unique:fee_heads,code',
            'description' => 'nullable|string',
            'is_refundable' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_refundable'] = $request->boolean('is_refundable');
        $validated['is_active'] = $request->boolean('is_active', true);

        $feeHead = FeeHead::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Fee Head created successfully',
                'feeHead' => $feeHead
            ]);
        }

        return redirect()->route('fees.fee-heads.index')
            ->with('success', 'Fee Head created successfully');
    }

    /**
     * Display the specified fee head.
     */
    public function show(FeeHead $feeHead)
    {
        return view('fees.fee-heads.show', compact('feeHead'));
    }

    /**
     * Show the form for editing the specified fee head.
     */
    public function edit(FeeHead $feeHead)
    {
        return view('fees.fee-heads.edit', compact('feeHead'));
    }

    /**
     * Update the specified fee head.
     */
    public function update(Request $request, FeeHead $feeHead)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fee_heads,name,' . $feeHead->id,
            'code' => 'required|string|max:10|unique:fee_heads,code,' . $feeHead->id,
            'description' => 'nullable|string',
            'is_refundable' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_refundable'] = $request->boolean('is_refundable');
        $validated['is_active'] = $request->boolean('is_active', true);

        $feeHead->update($validated);

        return redirect()->route('fees.fee-heads.index')
            ->with('success', 'Fee Head updated successfully');
    }

    /**
     * Remove the specified fee head.
     */
    public function destroy(FeeHead $feeHead)
    {
        $feeHead->delete();

        return redirect()->route('fees.fee-heads.index')
            ->with('success', 'Fee Head deleted successfully');
    }

    /**
     * Toggle status of fee head.
     */
    public function toggleStatus(FeeHead $feeHead)
    {
        $feeHead->update(['is_active' => !$feeHead->is_active]);

        return redirect()->back()
            ->with('success', 'Fee Head status updated successfully');
    }

    /**
     * Get all active fee heads (AJAX).
     */
    public function getActive()
    {
        $feeHeads = FeeHead::active()->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'feeHeads' => $feeHeads
        ]);
    }

    /**
     * Store a new fee head via AJAX.
     */
    public function storeAjax(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fee_heads,name',
            'code' => 'required|string|max:10|unique:fee_heads,code',
            'description' => 'nullable|string',
            'is_refundable' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_refundable'] = $request->boolean('is_refundable');
        $validated['is_active'] = $request->boolean('is_active', true);

        $feeHead = FeeHead::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fee Head created successfully',
            'feeHead' => $feeHead
        ]);
    }

    /**
     * Update a fee head via AJAX.
     */
    public function updateAjax(Request $request, FeeHead $feeHead)
    {
        // Handle both PUT and POST methods (for form method spoofing)
        $method = $request->input('_method', $request->getMethod());
        
        $rules = [
            'name' => 'required|string|max:255|unique:fee_heads,name,' . $feeHead->id,
            'code' => 'required|string|max:10|unique:fee_heads,code,' . $feeHead->id,
            'description' => 'nullable|string',
            'is_refundable' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];

        $validated = $request->validate($rules);

        $validated['is_refundable'] = $request->boolean('is_refundable');
        $validated['is_active'] = $request->boolean('is_active', true);

        $feeHead->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fee Head updated successfully',
            'feeHead' => $feeHead
        ]);
    }

    /**
     * Delete a fee head via AJAX.
     */
    public function destroyAjax(Request $request, FeeHead $feeHead)
    {
        $feeHead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fee Head deleted successfully'
        ]);
    }
}