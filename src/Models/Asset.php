<?php

namespace Innopanda\AssetManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\Image\Enums\Fit;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Trait Import
use Innopanda\AssetManager\Traits\HasAssetDependencies;

class Asset extends Model implements HasMedia
{
    use SoftDeletes;
    // use HasUuids;
    use InteractsWithMedia;
    use HasAssetDependencies;

    protected $table = 'assets';

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'disk',
        'path',
        'mime_type',
        'extension',
        'size',
        'hash',
        'width',
        'height',
        'alt',
        'description',
        'folder_id',
        'is_favorite',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Booted Methods & Event Hooks
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::creating(function ($asset) {
            if (empty($asset->uuid)) {
                $asset->uuid = (string) Str::uuid();
            }
        });

        // Phase 11: Prevent deletion if asset is used in active dependencies
        static::deleting(function ($asset) {
            if ($asset->isUsedInDependencies()) {
                throw new \Exception("Cannot delete asset because it is currently linked to active dependencies.");
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Media Collections
    |--------------------------------------------------------------------------
    */

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('original')
            ->acceptsFile(function (File $file) {
                return true;
            })
            ->useDisk(config('asset-manager.disk', 'public'))
            ->singleFile();
    }

    /*
    |--------------------------------------------------------------------------
    | Media Conversions
    |--------------------------------------------------------------------------
    */

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->fit(Fit::Crop, 150, 150)
            ->performOnCollections('original');

        $this
            ->addMediaConversion('medium')
            ->fit(Fit::Contain, 400, 400)
            ->performOnCollections('original');

        $this
            ->addMediaConversion('large')
            ->fit(Fit::Contain, 800, 800)
            ->performOnCollections('original');
    }
    
}