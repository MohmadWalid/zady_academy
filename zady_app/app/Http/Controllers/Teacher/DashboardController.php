<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        #TODO: We should work from here next time 

        
        // Home page (حلقات اليوم) -   يبقى عنده امكانية تغيير التاريخ(حلقاتي)  - الاعدادات ( تسجل الخروج + الكود)
        $teacherId = auth()->id();
        $today = Carbon::today()->format('Y-m-d');

        // Fetch groups with student counts and schedule sessions
        $groups = Group::where('teacher_id', $teacherId)
            ->with(['sessions'])
            ->withCount('activeEnrollments')
            ->get();

        // Calculate total students across all groups
        $totalStudents = $groups->sum('active_enrollments_count');

        // Check which groups have attendance taken today
        $attendanceDoneToday = Attendance::whereIn('group_id', $groups->pluck('id'))
            ->whereDate('date', $today)
            ->distinct()
            ->pluck('group_id')
            ->toArray();

        // Find the next upcoming session (simple logic for now)
        $nextSession = null;
        // In a real app, we'd compare day-of-week and time
        // For now, let's just count groups needing attendance
        $groupsNeedingAttendance = $groups->count() - count($attendanceDoneToday);

        return view('teacher.dashboard', compact(
            'groups', 
            'totalStudents', 
            'attendanceDoneToday', 
            'groupsNeedingAttendance'
        ));
    }
}
