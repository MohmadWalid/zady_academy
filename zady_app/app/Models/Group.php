<?php

namespace App\Models;

use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $fillable = [
        'name',
        'type',
        'monthly_price',
        'teacher_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function sessions()
    {
        return $this->hasMany(GroupSession::class);
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

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getScheduleDayAttribute(): string
    {
        $days = $this->sessions->pluck('day')->unique()->map(function($day) {
            return match($day) {
                'Saturday' => 'السبت',
                'Sunday' => 'الأحد',
                'Monday' => 'الاثنين',
                'Tuesday' => 'الثلاثاء',
                'Wednesday' => 'الأربعاء',
                'Thursday' => 'الخميس',
                'Friday' => 'الجمعة',
                default => $day
            };
        });

        return $days->isEmpty() ? 'لم يحدد' : $days->implode('، ');
    }

    public function getScheduleTimeAttribute(): string
    {
        $time = $this->sessions->first()?->time;
        if (!$time) return 'لم يحدد';
        
        return \Carbon\Carbon::parse($time)->format('g:i A');
    }
}
