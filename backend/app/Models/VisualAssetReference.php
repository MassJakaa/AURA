<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisualAssetReference extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'visual_asset_id',
        'reference_type',
        'reference_id',
    ];

    public function visualAsset(): BelongsTo
    {
        return $this->belongsTo(VisualAsset::class);
    }
}
