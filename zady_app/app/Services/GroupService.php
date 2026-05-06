<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupSession;
use RuntimeException;

class GroupService
{
    /**
     * Create a new group.
     */
    public function create(array $data): Group
    {
        return Group::create($data);
    }

    /**
     * Add a weekly session to a group.
     *
     * Enforces the max-2-sessions rule at the application layer (Implementation-Rules §10).
     * There is no DB-level constraint — this method is the single gate.
     *
     * $sessionData = ['day' => 'Saturday', 'time' => '10:00']
     *
     * @throws RuntimeException if the group already has 2 sessions
     */
    public function addSession(Group $group, array $sessionData): GroupSession
    {
        if ($group->sessions()->count() >= 2) {
            throw new RuntimeException(
                'لا يمكن إضافة أكثر من جلستين أسبوعياً للمجموعة.'
            );
        }

        return $group->sessions()->create($sessionData);
    }

    /**
     * Update an existing session (hard update — no soft delete on sessions, per spec §10).
     */
    public function updateSession(GroupSession $session, array $data): GroupSession
    {
        $session->update($data);
        return $session;
    }

    /**
     * Remove a session (hard delete — schedule changes are permanent, per spec §10).
     */
    public function deleteSession(GroupSession $session): void
    {
        $session->delete();
    }
}
