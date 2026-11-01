<?php

namespace App\Models;

use App\Enums\AssetApprovalStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisualAsset extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'character_id',
        'activity_id',
        'location_id',
        'asset_type',
        'asset_category',
        'file_path',
        'cdn_url',
        'file_size',
        'dimensions',
        'mime_type',
        'approval_status',
        'master_reference_version',
        'visual_prompt_id',
        'metadata',
    ];

    protected $casts = [
        'approval_status' => AssetApprovalStatus::class,
        'file_size' => 'integer',
        'metadata' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(VirtualLocation::class);
    }

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(VisualPrompt::class, 'visual_prompt_id');
    }

    public function references(): HasMany
    {
        return $this->hasMany(VisualAssetReference::class);
    }

    public function moments(): HasMany
    {
        return $this->hasMany(Moment::class);
    }
}
