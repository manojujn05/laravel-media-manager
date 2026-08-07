<?php

namespace Innopanda\AssetManager\Models;

use Illuminate\Database\Eloquent\Model;

class AssetVersion extends Model
{
    protected $table = 'asset_versions';

    protected $fillable = [
        'asset_id',
        'version_number',
        'disk',
        'path',
        'mime_type',
        'size',
        'extension',
        'created_by',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}