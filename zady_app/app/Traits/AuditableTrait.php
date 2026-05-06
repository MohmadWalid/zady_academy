<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Automatically populates created_by, updated_by, deleted_by on every write.
 *
 * Rules (Implementation-Rules §5):
 *  - creating  → created_by = auth()->id()  (skipped when already set, e.g. seeders)
 *  - updating  → updated_by = auth()->id()
 *  - deleting  → deleted_by = auth()->id()  (soft-delete models only)
 *
 * NOTE: attendance.taken_by is intentionally NOT managed here.
 *       It must be set explicitly in AttendanceService (per spec §9).
 */
trait AuditableTrait
{
    public static function bootAuditableTrait(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && is_null($model->created_by)) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        static::deleting(function ($model) {
            $usesSoftDeletes = in_array(
                SoftDeletes::class,
                class_uses_recursive($model)
            );

            if ($usesSoftDeletes && ! $model->isForceDeleting() && auth()->check()) {
                // SoftDeletes::runSoftDelete() builds its own UPDATE for deleted_at
                // and does NOT include arbitrary dirty attributes, so we patch
                // deleted_by directly before the soft-delete query fires.
                $model->getConnection()
                    ->table($model->getTable())
                    ->where($model->getKeyName(), $model->getKey())
                    ->update(['deleted_by' => auth()->id()]);
            }
        });
    }

    /**
     * The user who created this record.
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by')->withTrashed();
    }

    /**
     * The user who last updated this record.
     */
    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by')->withTrashed();
    }

    /**
     * The user who deleted this record (if soft-deleted).
     */
    public function deleter()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by')->withTrashed();
    }
}
