<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Advertisement extends Model
{
    protected $table = 'advertisements';

    protected $fillable = [
        'advertisement_no',
        'title',
        'year',
        'details',
        'valid_from',
        'valid_to',
        'newspaper_reference',
        'attachment_path',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'year' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (empty($model->year)) {
                $model->year = (int)date('Y');
            }
        });
    }

    /**
     * Recruitment job campaigns published under this advertisement.
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'advertisement_id');
    }

    /**
     * Chronological date extension history & corrigenda audit ledger.
     */
    public function dateTracks(): HasMany
    {
        return $this->hasMany(AdvertisementDateTrack::class, 'advertisement_id')->orderBy('extended_at', 'desc')->orderBy('id', 'desc');
    }

    /**
     * User or officer who created this advertisement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Extend validity date with automated track recording.
     */
    public function extendValidity(
        $newValidTo,
        ?string $reason = null,
        ?string $corrigendumNo = null,
        ?int $userId = null,
        bool $syncCampaigns = true,
        ?string $attachmentPath = null
    ): AdvertisementDateTrack {
        $previousValidTo = $this->valid_to;
        $parsedNewValidTo = Carbon::parse($newValidTo);

        // 1. Record in date tracks ledger
        $track = $this->dateTracks()->create([
            'previous_valid_to' => $previousValidTo,
            'extended_valid_to' => $parsedNewValidTo,
            'corrigendum_no' => $corrigendumNo,
            'reason' => $reason,
            'extended_at' => now(),
            'extended_by' => $userId ?: auth()->id(),
            'attachment_path' => $attachmentPath,
        ]);

        // 2. Update active valid_to
        $this->update([
            'valid_to' => $parsedNewValidTo,
        ]);

        // 3. Optionally sync all published jobs under this advertisement
        if ($syncCampaigns) {
            $this->campaigns()->update([
                'closing_date' => $parsedNewValidTo,
            ]);
        }

        return $track;
    }

    /**
     * Check if advertisement date has ever been extended.
     */
    public function getIsExtendedAttribute(): bool
    {
        return $this->dateTracks()->exists();
    }

    /**
     * Check if advertisement validity period has expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_to ? $this->valid_to->isPast() : false;
    }

    /**
     * Remaining days until expiry.
     */
    public function getDaysRemainingAttribute(): int
    {
        if (!$this->valid_to || $this->valid_to->isPast()) {
            return 0;
        }
        return (int)ceil(now()->diffInDays($this->valid_to, false));
    }

    /**
     * Formatted date range string.
     */
    public function getFormattedValidityAttribute(): string
    {
        $from = $this->valid_from ? $this->valid_from->format('d M Y') : 'N/A';
        $to = $this->valid_to ? $this->valid_to->format('d M Y') : 'N/A';
        return "{$from} — {$to}";
    }

    /**
     * Dynamic status badge representation.
     */
    public function getStatusBadgeAttribute(): array
    {
        if (!$this->is_active) {
            return [
                'label' => 'INACTIVE',
                'color' => '#ef4444',
                'bg' => 'rgba(239, 68, 68, 0.15)',
                'border' => 'rgba(239, 68, 68, 0.35)',
            ];
        }

        if ($this->is_expired) {
            return [
                'label' => 'EXPIRED',
                'color' => '#94a3b8',
                'bg' => 'rgba(148, 163, 184, 0.15)',
                'border' => 'rgba(148, 163, 184, 0.35)',
            ];
        }

        if ($this->is_extended) {
            $extensionsCount = $this->dateTracks()->count();
            return [
                'label' => "EXTENDED (x{$extensionsCount})",
                'color' => '#38bdf8',
                'bg' => 'rgba(56, 189, 248, 0.15)',
                'border' => 'rgba(56, 189, 248, 0.35)',
            ];
        }

        return [
            'label' => 'ACTIVE',
            'color' => '#10b981',
            'bg' => 'rgba(16, 185, 129, 0.15)',
            'border' => 'rgba(16, 185, 129, 0.35)',
        ];
    }
}
