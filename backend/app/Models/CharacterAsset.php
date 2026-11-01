<?php

namespace App\Models;

use App\Enums\AssetApprovalStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterAsset extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'character_id',
        'version_id',
        'asset_type',
        'name',
        'file_path',
        'cdn_url',
        'approval_status',
        'is_master_reference',
        'metadata',
    ];

    protected $casts = [
        'approval_status' => AssetApprovalStatus::class,
        'is_master_reference' => 'boolean',
        'metadata' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(CharacterVersion::class, 'version_id');
    }
}
