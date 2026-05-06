<?php
namespace App\Http\Controllers\Secretary;
use App\Http\Controllers\Controller;
class DashboardController extends Controller
{
    public function index()
    {
        $cashToday = \App\Models\Payment::where('method', 'cash')
            ->where('status', 'approved')
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->sum('amount');
        
        $pendingTransfersCount = \App\Models\Payment::where('method', 'transfer')
            ->where('status', 'pending')
            ->count();
        
        $inquiriesCount = 0; // No inquiry model yet

        return view('secretary.dashboard', compact(
            'cashToday', 
            'pendingTransfersCount', 
            'inquiriesCount'
        ));
    }
}
