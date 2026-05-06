<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    public function pending(Request $request)
    {
        $query = Payment::with(['subscription.student', 'subscription.group'])
            ->where('method', 'transfer')
            ->where('status', 'pending');

        if ($request->filled('search')) {
            $query->whereHas('subscription.student', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('payment_code', 'like', "%{$request->search}%");
            });
        }

        $payments = $query->latest()->paginate(15);

        return view('secretary.payments.pending', compact('payments'));
    }

    public function approve(Payment $payment)
    {
        try {
            $this->paymentService->approve($payment);
            return back()->with('success', 'تم قبول الدفعة بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Payment $payment)
    {
        $request->validate(['reason' => 'nullable|string|max:255']);
        
        try {
            $this->paymentService->reject($payment, $request->reason ?? '');
            return back()->with('success', 'تم رفض الدفعة.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cash(Request $request)
    {
        $student = null;
        if ($request->filled('search')) {
            $student = \App\Models\Student::with(['activeEnrollments.group', 'activeEnrollments.subscriptions' => function($q) {
                $q->where('status', 'unpaid');
            }])->where('name', 'like', "%{$request->search}%")
               ->orWhere('id', 'like', "%{$request->search}%")
               ->first();
        }

        return view('secretary.payments.cash', compact('student'));
    }

    public function store_cash(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $subscription = \App\Models\Subscription::findOrFail($request->subscription_id);

        try {
            $this->paymentService->recordCash($subscription, $request->amount);
            return redirect()->route('secretary.payments.history')
                ->with('success', 'تم تسجيل التحصيل النقدي بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function history(Request $request)
    {
        $query = Payment::with(['subscription.student', 'subscription.group']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('payment_code', 'like', "%{$request->search}%")
                  ->orWhereHas('subscription.student', function($sq) use ($request) {
                      $sq->where('name', 'like', "%{$request->search}%");
                  });
            });
        }

        $payments = $query->latest()->paginate(20);
            
        return view('secretary.payments.history', compact('payments'));
    }
}
