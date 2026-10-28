<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Publication extends Model
{
    use Auditable;

    protected $fillable = [
        'basic_info_id',
        'title',
        'journal_name',
        'issn_print',
        'hjrs_category',
        'impact_factor',
        'author_order',
        'is_corresponding_author',
        'authors',
        'doi',
        'volume_issue',
        'url',
        'proof_path',
        'publication_year',
        'is_verified',
        'verified_by',
    ];

    protected $casts = [
        'impact_factor' => 'decimal:3',
        'is_corresponding_author' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function basicInfo(): BelongsTo
    {
        return $this->belongsTo(BasicInfo::class, 'basic_info_id');
    }

    /**
     * User who verified this publication.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
