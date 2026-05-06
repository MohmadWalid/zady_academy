<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Group;

class GroupPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function update(User $user, Group $group): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function delete(User $user, Group $group): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function restore(User $user, Group $group): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function addSession(User $user, Group $group): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function deleteSession(User $user, Group $group): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }
}
