@extends('student.layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Pay Fees Online</h2>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Fee Details</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>Fee Head:</th>
                            <td>{{ $fee->feeStructure->feeHead->name }}</td>
                        </tr>
                        <tr>
                            <th>Total Amount:</th>
                            <td>₹{{ number_format($fee->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Discount:</th>
                            <td>₹{{ number_format($fee->discount_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Final Amount:</th>
                            <td>₹{{ number_format($fee->final_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Paid Amount:</th>
                            <td class="text-success">₹{{ number_format($fee->paid_amount, 2) }}</td>
                        </tr>
                        <tr class="table-warning">
                            <th>Outstanding Amount:</th>
                            <td><strong>₹{{ number_format($fee->outstanding_amount, 2) }}</strong></td>
                        </tr>
                    </table>

                    @php
                        $installments = $fee->feeStructure->installments ?? 1;
                        $singleInstallment = $installments > 1 ? $fee->final_amount / $installments : $fee->outstanding_amount;
                        
                        // Get existing payments to determine which installments are paid
                        $paidPayments = \App\Models\Fee\FeePayment::where('student_fee_id', $fee->id)
                            ->where('status', 'success')
                            ->get();
                        $paidInstallments = $paidPayments->pluck('installment_number')->toArray();
                        
                        // Calculate remaining installments
                        $remainingInstallments = [];
                        for ($i = 1; $i <= $installments; $i++) {
                            if (!in_array($i, $paidInstallments)) {
                                // Check if partial payment exists for this installment
                                $partialPayment = $paidPayments->where('installment_number', $i)->first();
                                if ($partialPayment) {
                                    $remainingAmount = $singleInstallment - $partialPayment->amount;
                                    if ($remainingAmount > 0) {
                                        $remainingInstallments[] = [
                                            'number' => $i,
                                            'amount' => $remainingAmount
                                        ];
                                    }
                                } else {
                                    $remainingInstallments[] = [
                                        'number' => $i,
                                        'amount' => $singleInstallment
                                    ];
                                }
                            }
                        }
                        
                        // Check if Razorpay credentials are configured
                        $razorpayKey = config('services.razorpay.key');
                        $razorpaySecret = config('services.razorpay.secret');
                        $useRazorpay = !empty($razorpayKey) && !empty($razorpaySecret);
                    @endphp

                    @if($useRazorpay)
                        <div class="alert alert-info mt-3">
                            <small>Secure payment powered by Razorpay</small>
                        </div>
                    @else
                        <div class="alert alert-warning mt-3">
                            <small><i class="bi bi-info-circle"></i> Razorpay credentials not configured. Payment will be submitted directly.</small>
                        </div>
                    @endif

                    <form id="paymentForm" method="POST" action="{{ route('student.fees.process-payment') }}">
                        @csrf
                        <input type="hidden" name="student_fee_id" value="{{ $fee->id }}">
                        <input type="hidden" name="payment_mode" value="{{ $useRazorpay ? 'online' : 'cash' }}">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Installment(s) to Pay</label>
                            @if(count($remainingInstallments) > 0)
                                <div class="border rounded p-3">
                                    @foreach($remainingInstallments as $installment)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input installment-checkbox" 
                                               type="checkbox" 
                                               name="installments[]" 
                                               value="{{ $installment['number'] }}"
                                               id="installment_{{ $installment['number'] }}"
                                               data-amount="{{ $installment['amount'] }}">
                                        <label class="form-check-label" for="installment_{{ $installment['number'] }}">
                                            <strong>Installment {{ $installment['number'] }}</strong> 
                                            <span class="text-success">₹{{ number_format($installment['amount'], 2) }}</span>
                                            @if(in_array($installment['number'], array_column($paidPayments->toArray(), 'installment_number')))
                                                <span class="badge bg-warning text-dark">Partial</span>
                                            @endif
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-info-circle"></i> Select one or more installments. Each installment amount is fixed (₹{{ number_format($singleInstallment, 2) }}).
                                </small>
                            @else
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle"></i> All installments have been paid!
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Amount to Pay</label>
                            <input type="number" name="amount" id="amount" class="form-control" 
                                   min="0" max="{{ $fee->outstanding_amount }}" 
                                   value="0" readonly
                                   step="0.01">
                            <small class="text-muted">Amount is calculated automatically based on selected installment(s)</small>
                        </div>

                        @if(!$useRazorpay)
                        <div class="mb-3">
                            <label class="form-label">Payment Remarks (Optional)</label>
                            <input type="text" name="remarks" class="form-control" 
                                   placeholder="Enter payment reference or notes">
                        </div>
                        @endif

                        @if(count($remainingInstallments) > 0)
                        <button type="submit" id="payButton" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-credit-card"></i> Pay Now
                        </button>
                        @else
                        <button type="button" class="btn btn-secondary btn-lg w-100" disabled>
                            <i class="bi bi-check-circle"></i> Fully Paid
                        </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Payment Methods</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="bi bi-credit-card text-primary"></i> Credit/Debit Card</li>
                        <li><i class="bi bi-bank text-success"></i> Net Banking</li>
                        <li><i class="bi bi-phone text-warning"></i> UPI</li>
                        <li><i class="bi bi-wallet2 text-info"></i> Wallets</li>
                        @if(!$useRazorpay)
                        <li><i class="bi bi-cash text-secondary"></i> Offline Payment</li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <!-- Payment History for this fee -->
            @if($paidPayments->count() > 0)
            <div class="card mt-3">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Payment History</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tbody>
                            @foreach($paidPayments as $payment)
                            <tr>
                                <td>
                                    <small><strong>₹{{ number_format($payment->amount, 2) }}</strong></small>
                                    <br><small class="text-muted">{{ $payment->payment_date->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ ucfirst($payment->status) }}</span>
                                    <br><small class="text-muted">Installment {{ $payment->installment_number }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($useRazorpay)
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.installment-checkbox');
    const amountInput = document.getElementById('amount');
    const paymentForm = document.getElementById('paymentForm');
    
    // Calculate total amount when checkboxes change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            let total = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    total += parseFloat(cb.dataset.amount);
                }
            });
            amountInput.value = total.toFixed(2);
        });
    });
    
    // Handle form submission
    paymentForm.addEventListener('submit', function(e) {
        const selectedAmount = parseFloat(amountInput.value);
        
        if (selectedAmount <= 0) {
            e.preventDefault();
            alert('Please select at least one installment to pay.');
            return;
        }
        
        @if($useRazorpay)
        e.preventDefault();
        
        const amount = selectedAmount;
        const studentFeeId = {{ $fee->id }};

        fetch('{{ route("razorpay.create-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                student_fee_id: studentFeeId,
                amount: amount
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            const options = {
                key: data.key,
                amount: data.amount,
                currency: data.currency,
                order_id: data.order_id,
                name: 'School ERP',
                description: 'Fee Payment',
                handler: function(response) {
                    verifyPayment(response, studentFeeId);
                },
                prefill: {
                    name: '{{ $student->full_name }}',
                    email: '{{ $student->email }}'
                },
                theme: {
                    color: '#0d6efd'
                }
            };

            const rzp = new Razorpay(options);
            rzp.open();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to create payment order. Please try again.');
        });
        @else
        // For offline payment, show confirmation
        if (!confirm('Razorpay credentials not found. Submit payment directly? This will record the payment offline.')) {
            e.preventDefault();
        }
        @endif
    });
});

@if($useRazorpay)
function verifyPayment(response, studentFeeId) {
    fetch('{{ route("razorpay.verify-payment") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            razorpay_order_id: response.razorpay_order_id,
            razorpay_payment_id: response.razorpay_payment_id,
            razorpay_signature: response.razorpay_signature,
            student_fee_id: studentFeeId
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("fees.payments.receipt", ["payment" => ""]) }}' + data.receipt_id;
        } else {
            alert('Payment verification failed');
        }
    });
}
@endif
</script>
@endsection
