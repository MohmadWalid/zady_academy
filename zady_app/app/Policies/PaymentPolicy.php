<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Payment;

class PaymentPolicy
{
    public function create(User $user): bool
    {
        // For cash payments
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function submitTransfer(User $user): bool
    {
        return $user->role === 'parent';
    }

    public function approve(User $user, Payment $payment): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function reject(User $user, Payment $payment): bool
    {
        return in_array($user->role, ['admin', 'secretary']);
    }

    public function refund(User $user, Payment $payment): bool
    {
        return $user->role === 'admin';
    }

    public function viewProof(User $user, Payment $payment): bool
    {
        if (in_array($user->role, ['admin', 'secretary'])) {
            return true;
        }

        // Parent who owns the payment can view
        if ($user->role === 'parent' && $payment->subscription && $payment->subscription->student) {
            return $payment->subscription->student->parent_id === $user->id;
        }

        return false;
    }
}
