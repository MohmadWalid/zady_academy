<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendanceService) {}

    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        $query = Group::where('teacher_id', auth()->id())
            ->with(['teacher', 'sessions'])
            ->withCount('activeEnrollments');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $groups = $query->get();

        return view('teacher.attendance.index', compact('groups', 'date'));
    }

    public function show(Request $request, string $id)
    {
        $group = Group::with(['teacher', 'activeEnrollments.student'])->findOrFail($id);
        
        if ($group->teacher_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بتحضير هذه الحلقة.');
        }

        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        $existingAttendance = $this->attendanceService->getGroupAttendance($group->id, $date)
            ->pluck('present', 'student_id')
            ->toArray();

        return view('teacher.attendance.show', compact('group', 'date', 'existingAttendance'));
    }

    public function store(Request $request, Group $group)
    {
        if ($group->teacher_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.present' => 'required|boolean',
        ]);

        $this->attendanceService->recordBulk(
            $group->id,
            $data['date'],
            $data['attendance'],
            auth()->id()
        );

        return redirect()->route('teacher.dashboard')
            ->with('success', 'تم حفظ سجل التحضير بنجاح.');
    }
}
