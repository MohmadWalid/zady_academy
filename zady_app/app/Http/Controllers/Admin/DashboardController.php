<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = \App\Models\Payment::where('status', 'approved')->sum('amount');
        $cashToday = \App\Models\Payment::where('method', 'cash')
            ->where('status', 'approved')
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->sum('amount');
        
        $activeStudents = \App\Models\Student::count();
        $activeGroups = \App\Models\Group::count();

        return view('admin.dashboard', compact(
            'totalRevenue', 
            'cashToday', 
            'activeStudents', 
            'activeGroups'
        ));
    }
}
