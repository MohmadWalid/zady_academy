<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Student;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function update(User $user, Student $student): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function delete(User $user, Student $student): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function restore(User $user, Student $student): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }
}
