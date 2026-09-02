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

use Illuminate\Database\Eloquent\Relations\HasMany;
use Innopanda\AssetManager\Exceptions\AssetInUseException;
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

        static::deleting(function ($asset) {
            if ($asset->usages()->exists() || $asset->isUsedInDependencies()) {
                throw new AssetInUseException("Cannot delete asset because it is currently linked to active dependencies.");
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

    public function usages(): HasMany
    {
        return $this->hasMany(AssetUsage::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(AssetVersion::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(AssetActivityLog::class);
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
            ->useDisk(config('asset-manager.disk', 'public'));
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

    /*
    |--------------------------------------------------------------------------
    | URL Generation
    |--------------------------------------------------------------------------
    */

    public function getUrl(string $conversion = ''): string
    {
        $diskName = $this->disk ?? config('asset-manager.disk', 'public');
        $storage = \Illuminate\Support\Facades\Storage::disk($diskName);
        $config = config("filesystems.disks.{$diskName}", []);
        $isS3Driver = ($config['driver'] ?? null) === 's3';

        /*
        |--------------------------------------------------------------------------
        | Private S3
        |--------------------------------------------------------------------------
        |
        | Private S3 assets must always use a temporary signed URL.
        | This check intentionally happens before getFirstMediaUrl()
        | because Spatie may already return a normal URL.
        |
        */
        if ($isS3Driver && config('asset-manager.sync.private_urls', false)) {
            $expirationMinutes = (int) config('asset-manager.sync.temporary_url_expiration', 60);

            try {
                return $storage->temporaryUrl(
                    $this->path,
                    now()->addMinutes($expirationMinutes)
                );
            } catch (\Throwable $e) {
                // Fall through to normal URL generation.
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Spatie Media Library URL
        |--------------------------------------------------------------------------
        */
        $url = $this->getFirstMediaUrl('original', $conversion);

        if (!$url) {
            $url = $this->getFirstMediaUrl('assets', $conversion);
        }

        /*
        |--------------------------------------------------------------------------
        | Fallback to Asset path
        |--------------------------------------------------------------------------
        */
        if (!$url) {
            if (filter_var($this->path, FILTER_VALIDATE_URL)) {
                $url = $this->path;
            } elseif ($this->path) {
                $url = $storage->url($this->path);
            } else {
                return '';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize URL
        |--------------------------------------------------------------------------
        */
        if ($isS3Driver) {
            return static::normalizeS3Url($url, $config);
        }

        return static::normalizeLocalUrl($url);
    }

    public static function normalizeS3Url(string $url, array $config): string
    {
        // If it's already an absolute URL, we trust Laravel's url generator
        if (Str::startsWith($url, 'http://') || Str::startsWith($url, 'https://')) {
            return $url;
        }

        $bucket = $config['bucket'] ?? '';
        $region = $config['region'] ?? 'us-east-1';
        $endpoint = $config['endpoint'] ?? null;
        $configuredUrl = $config['url'] ?? null;

        // Clean up the relative path
        $path = ltrim($url, '/');

        if ($configuredUrl) {
            return rtrim($configuredUrl, '/') . '/' . $path;
        } elseif ($endpoint) {
            // For custom endpoints like MinIO
            return rtrim($endpoint, '/') . '/' . $bucket . '/' . $path;
        }

        // Standard AWS S3 format
        return "https://{$bucket}.s3.{$region}.amazonaws.com/" . $path;
    }

    public static function normalizeLocalUrl(string $url): string
    {
        if (Str::startsWith($url, 'http://') || Str::startsWith($url, 'https://')) {
            return $url;
        }
        
        return url($url);
    }
}