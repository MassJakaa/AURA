<?php

namespace App\Models;

use App\Enums\MomentType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MomentTemplate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code',
        'category',
        'moment_type',
        'title_template',
        'narrative_template',
        'caption_template',
        'required_visual_type',
        'metadata',
    ];

    protected $casts = [
        'moment_type' => MomentType::class,
        'metadata' => 'array',
    ];
}
