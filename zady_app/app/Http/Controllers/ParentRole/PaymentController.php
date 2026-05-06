<?php

namespace App\Http\Controllers\ParentRole;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    public function index()
    {
        $payments = \App\Models\Payment::whereHas('subscription.student', function($q) {
                $q->where('parent_id', auth()->id());
            })
            ->with(['subscription.student', 'subscription.group'])
            ->latest()
            ->paginate(15);

        return view('parent.payments.index', compact('payments'));
    }

    public function upload()
    {
        // Fetch unpaid subscriptions for the parent's children
        $unpaidSubscriptions = Subscription::whereHas('student', function($q) {
                $q->where('parent_id', auth()->id());
            })
            ->with(['student', 'group'])
            ->where('status', 'unpaid')
            ->get();

        return view('parent.payments.upload', compact('unpaidSubscriptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'amount' => 'required|numeric|min:1',
            'proof' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB
        ]);

        $subscription = Subscription::findOrFail($request->subscription_id);

        // Security check: ensure the subscription belongs to one of the parent's children
        if ($subscription->student->parent_id !== auth()->id()) {
            abort(403);
        }

        // Store proof image securely (private storage)
        $path = $request->file('proof')->store('private/proofs');

        try {
            $this->paymentService->submitTransfer(
                $subscription,
                $path,
                $request->amount
            );

            return redirect()->route('parent.payments.index')
                ->with('success', 'تم رفع الإيصال بنجاح وهو قيد المراجعة.');
        } catch (\Exception $e) {
            // Delete the uploaded file if DB creation fails
            Storage::delete($path);
            return back()->with('error', $e->getMessage());
        }
    }
}
