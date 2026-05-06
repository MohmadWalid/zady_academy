<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Enrollment;

class EnrollmentPolicy
{
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function delete(User $user, Enrollment $enrollment): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }
}
