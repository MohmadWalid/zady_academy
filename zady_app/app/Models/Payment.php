<?php

namespace App\Models;

use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $fillable = [
        'payment_code',
        'subscription_id',
        'amount',
        'method',
        'status',
        'proof_image',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * The user who created the payment (secretary/admin for cash; parent for transfer).
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The user who last changed the status — serves as approved_by / rejected_by / refunded_by.
     * (Per System-Design §4.7 — no separate approved_by column.)
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
