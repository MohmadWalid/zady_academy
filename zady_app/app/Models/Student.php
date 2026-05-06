<?php

namespace App\Models;

use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $fillable = [
        'name',
        'parent_id',
        'age',
        'phone',
        'address',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'age' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments()
    {
        return $this->hasMany(Enrollment::class)->where('active', true);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }
}
