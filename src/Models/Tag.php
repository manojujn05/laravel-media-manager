<?php

namespace Innopanda\AssetManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $table = 'asset_tags';

    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    protected static function booted(): void
    {
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Relationship with Assets
     */
   public function assets(): BelongsToMany
    {
        // $table ki jagah $this ka use karein
        return $this->belongsToMany(Asset::class, 'asset_taggables', 'tag_id', 'asset_id');
    }
}