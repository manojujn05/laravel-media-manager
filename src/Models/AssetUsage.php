<?php

namespace Innopanda\AssetManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AssetUsage extends Model
{
    protected $table = 'asset_usages';

    protected $fillable = [
        'asset_id',
        'usable_type',
        'usable_id',
        'field',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function usable(): MorphTo
    {
        return $this->morphTo();
    }
}
