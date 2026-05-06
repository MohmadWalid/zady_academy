<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Subscription;

class SubscriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function update(User $user, Subscription $subscription): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function restore(User $user, Subscription $subscription): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }
}
