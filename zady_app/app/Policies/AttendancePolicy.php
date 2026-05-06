<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Group;

class AttendancePolicy
{
    public function create(User $user, Group $group): bool
    {
        if (in_array($user->role, ['admin', 'secretary'])) {
            return true;
        }

        if ($user->role === 'teacher') {
            return $user->id === $group->teacher_id;
        }

        return false;
    }

    public function update(User $user, Group $group): bool
    {
        if (in_array($user->role, ['admin', 'secretary'])) {
            return true;
        }

        if ($user->role === 'teacher') {
            return $user->id === $group->teacher_id;
        }

        return false;
    }
}
