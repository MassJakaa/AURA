<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemoryEmbedding extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'memory_id',
        'embedding_model',
        'embedding_vector',
        'dimensions',
    ];

    protected $casts = [
        'dimensions' => 'integer',
    ];

    public function memory(): BelongsTo
    {
        return $this->belongsTo(Memory::class);
    }
}
