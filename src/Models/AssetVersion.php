<?php

namespace Innopanda\AssetManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetVersion extends Model
{
    protected $table = 'asset_versions';

    protected $fillable = [
        'asset_id',
        'version',
        'disk',
        'path',
        'file_name',
        'mime_type',
        'extension',
        'size',
        'hash',
        'width',
        'height',
        'metadata',
        'change_note',
        'created_by',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
