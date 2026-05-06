<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    /**
     * Display a listing of subscriptions.
     */
    public function index(Request $request)
    {
        $query = Subscription::with(['student', 'group']);

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        $subscriptions = $query->latest('month')->paginate(20);

        return view('admin.academic.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Trigger manual bulk generation for the current or next month.
     */
    public function generate(Request $request)
    {
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        
        try {
            $count = $this->subscriptionService->generateForMonth($month);
            return back()->with('success', "تم توليد {$count} اشتراك جديد لشهر {$month} بنجاح.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified subscription (Soft delete).
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return back()->with('success', 'تم حذف الاشتراك.');
    }

    /**
     * Restore a soft-deleted subscription.
     */
    public function restore(string $id)
    {
        $sub = Subscription::onlyTrashed()->findOrFail($id);
        $sub->restore();
        return back()->with('success', 'تم استعادة الاشتراك.');
    }
}
