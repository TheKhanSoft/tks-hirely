<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityAuditLog extends Model
{
    public $timestamps = false;

    protected $table = 'security_audit_logs';

    protected $fillable = [
        'user_id',
        'ip_address',
        'action_type',
        'table_name',
        'record_id',
        'state_before',
        'state_after',
    ];

    protected $casts = [
        'state_before' => 'array',
        'state_after' => 'array',
        'logged_at' => 'datetime',
    ];

    /**
     * User who initiated the changes.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Convenient helper to record a security audit entry.
     */
    public static function record(
        string $actionType,
        string $tableName,
        $recordId,
        $stateAfter = null,
        $stateBefore = null,
        $userId = null
    ): self {
        return self::create([
            'user_id' => $userId ?: auth()->id(),
            'ip_address' => request()->ip() ?: '127.0.0.1',
            'action_type' => $actionType,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'state_before' => is_array($stateBefore) ? $stateBefore : ($stateBefore ? (array) $stateBefore : null),
            'state_after' => is_array($stateAfter) ? $stateAfter : ($stateAfter ? (array) $stateAfter : null),
        ]);
    }
}
