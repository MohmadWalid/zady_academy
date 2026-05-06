<?php

namespace App\Http\Controllers\ParentRole;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Subscription;

class DashboardController extends Controller
{
    public function index()
    {
        $parentId = auth()->id();

        // Get parent's children with their active groups
        $children = Student::where('parent_id', $parentId)
            ->with(['activeEnrollments.group'])
            ->get();

        // Get count of unpaid or pending subscriptions
        $pendingPaymentsCount = Subscription::whereHas('student', function($q) use ($parentId) {
                $q->where('parent_id', $parentId);
            })
            ->whereIn('status', ['unpaid', 'pending'])
            ->count();

        // Get total unpaid amount
        $unpaidAmount = Subscription::whereHas('student', function($q) use ($parentId) {
                $q->where('parent_id', $parentId);
            })
            ->where('status', 'unpaid')
            ->join('groups', 'subscriptions.group_id', '=', 'groups.id')
            ->sum('groups.monthly_price');

        // Get the latest 5 subscriptions for current status overview
        $recentSubscriptions = Subscription::whereHas('student', function($q) use ($parentId) {
                $q->where('parent_id', $parentId);
            })
            ->with(['student', 'group'])
            ->latest('month')
            ->take(10)
            ->get();

        return view('parent.dashboard', compact('children', 'pendingPaymentsCount', 'unpaidAmount', 'recentSubscriptions'));
    }
}
