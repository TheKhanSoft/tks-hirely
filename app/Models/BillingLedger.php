<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingLedger extends Model
{
    protected $table = 'billing_ledger';

    protected $fillable = [
        'application_id',
        'consumer_id',
        'amount',
        'status',
        'transaction_id',
        'payment_timestamp',
        'raw_response_log',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_timestamp' => 'datetime',
        'raw_response_log' => 'array',
    ];

    /**
     * Parent application.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
