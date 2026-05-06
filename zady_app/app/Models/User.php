<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'access_code',
        'role',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Disable remember-token — no remember-me in this system.
     */
    public function getRememberTokenName(): ?string
    {
        return null;
    }

    // ── Role helpers ──────────────────────────────────────────────────────────

    public function isAdmin(): bool     { return $this->role === 'admin'; }
    public function isSecretary(): bool { return $this->role === 'secretary'; }
    public function isTeacher(): bool   { return $this->role === 'teacher'; }
    public function isParent(): bool    { return $this->role === 'parent'; }

    public function dashboardRoute(): string
    {
        return $this->role . '.dashboard';
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    /** Students whose parent is this user (role = parent). */
    public function children()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }

    /** Groups where this user is the assigned teacher (role = teacher). */
    public function assignedGroups()
    {
        return $this->hasMany(Group::class, 'teacher_id');
    }
}
