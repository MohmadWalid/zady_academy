<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Support\Collection;

class AttendanceService
{
    /**
     * Bulk upsert attendance for a group on a given date.
     *
     * Creates or updates one Attendance row per student entry.
     * The unique constraint on (student_id, group_id, date) prevents duplicates.
     *
     * Rules (Implementation-Rules §9):
     *  - taken_by is set EXPLICITLY here — it is NOT managed by AuditableTrait.
     *  - taken_by must reference a valid users.id with role in [teacher, secretary, admin].
     *    This is enforced at the controller level via RoleMiddleware before this method is called.
     *  - Attendance dates are free-form; no validation against group_sessions.
     *  - Attendance is editable — calling this again for the same (group, date) updates records.
     *
     * @param int    $groupId        groups.id
     * @param string $date           Date string (YYYY-MM-DD)
     * @param array  $attendanceData [['student_id' => int, 'present' => bool], ...]
     * @param int    $takenBy        auth()->id() of the recording user
     */
    public function recordBulk(
        int    $groupId,
        string $date,
        array  $attendanceData,
        int    $takenBy
    ): void {
        $parsedDate = \Carbon\Carbon::parse($date)->startOfDay();
        foreach ($attendanceData as $entry) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $entry['student_id'],
                    'group_id'   => $groupId,
                    'date'       => $parsedDate,
                ],
                [
                    'present'  => $entry['present'] ?? false,
                    'taken_by' => $takenBy,
                ]
            );
        }
    }

    /**
     * Retrieve all attendance records for a group on a specific date.
     * Eager-loads the student relationship for display in the attendance view.
     *
     * Returns an empty Collection (not null) when no records exist yet.
     *
     * @param int    $groupId
     * @param string $date  YYYY-MM-DD
     */
    public function getGroupAttendance(int $groupId, string $date): Collection
    {
        return Attendance::with('student')
            ->where('group_id', $groupId)
            ->where('date', $date)
            ->orderBy('student_id')
            ->get();
    }
}
