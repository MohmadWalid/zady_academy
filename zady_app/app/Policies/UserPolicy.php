<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, User $target): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, User $target): bool
    {
        if ($user->role !== 'admin') {
            return false;
        }

        // block if user has active groups as teacher
        // assuming User has groups() relation or we query Group
        if ($target->role === 'teacher' && \App\Models\Group::where('teacher_id', $target->id)->exists()) {
            return false;
        }

        return true;
    }

    public function restore(User $user, User $target): bool
    {
        return $user->role === 'admin';
    }
}
