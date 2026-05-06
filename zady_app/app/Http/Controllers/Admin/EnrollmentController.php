<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'group_id' => 'required|exists:groups,id',
        ]);

        // Check if already enrolled
        $exists = Enrollment::where('student_id', $request->student_id)
            ->where('group_id', $request->group_id)
            ->where('active', true)
            ->exists();

        if ($exists) {
            return back()->with('error', 'الطالب مسجل بالفعل في هذه الحلقة.');
        }

        Enrollment::create([
            'student_id' => $request->student_id,
            'group_id' => $request->group_id,
            'active' => true,
        ]);

        return back()->with('success', 'تم إضافة الطالب للحلقة بنجاح.');
    }

    /**
     * Remove the specified resource from storage (soft delete/deactivate).
     */
    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete(); // Soft delete per AuditableTrait

        return back()->with('success', 'تم إلغاء اشتراك الطالب من الحلقة.');
    }
}
