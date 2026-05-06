<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSession extends Model
{
    use HasFactory;

    // No SoftDeletes  — schedule changes are hard updates/deletes (spec §10)
    // No AuditableTrait — group_sessions has only created_at / updated_at (spec §4.1)

    protected $fillable = [
        'group_id',
        'day',
        'time',
    ];

    protected $casts = [
        'time' => 'string',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
