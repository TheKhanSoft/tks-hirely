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
}
