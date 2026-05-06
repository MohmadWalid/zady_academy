<?php

namespace App\Models;

use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory, AuditableTrait;

    protected $table = 'attendance';

    // No SoftDeletes — attendance has no deleted_at / deleted_by (spec §4.1 + CLAUDE.md)

    protected $fillable = [
        'student_id',
        'group_id',
        'date',
        'present',
        'taken_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'present' => 'boolean',
        'date'    => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * The teacher/secretary/admin who recorded this attendance entry.
     * Set explicitly in AttendanceService — NOT managed by AuditableTrait.
     */
    public function takenBy()
    {
        return $this->belongsTo(User::class, 'taken_by');
    }
}
