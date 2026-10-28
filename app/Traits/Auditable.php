<?php

namespace App\Traits;

use App\Models\SecurityAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Boot the trait and register model event listeners.
     */
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->logAudit('CREATED', null, $model->attributesToArray());
        });

        static::updated(function ($model) {
            $before = [];
            $after = [];

            foreach ($model->getDirty() as $key => $value) {
                $before[$key] = $model->getOriginal($key);
                $after[$key] = $value;
            }

            if (!empty($after)) {
                $model->logAudit('UPDATED', $before, $after);
            }
        });

        static::deleted(function ($model) {
            $model->logAudit('DELETED', $model->attributesToArray(), null);
        });
    }

    /**
     * Log the audit transaction.
     */
    protected function logAudit(string $action, ?array $before, ?array $after): void
    {
        if ($this->getTable() === 'security_audit_logs') {
            return;
        }

        try {
            // Create a security log entry
            SecurityAuditLog::create([
                'user_id' => Auth::id(),
                'ip_address' => Request::ip() ?? '127.0.0.1',
                'action_type' => $action,
                'table_name' => $this->getTable(),
                'record_id' => $this->getKey(),
                'state_before' => $before ? json_encode($before) : null,
                'state_after' => $after ? json_encode($after) : json_encode([]),
            ]);
        } catch (\Exception $e) {
            // Silently log or handle to avoid disrupting main application flow
            logger()->error("Failed to write security audit log: " . $e->getMessage());
        }
    }
}
