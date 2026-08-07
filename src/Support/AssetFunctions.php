<?php

use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Services\ThumbnailService;

if (! function_exists('asset_thumbnail')) {

    function asset_thumbnail(
        Asset $asset,
        string $conversion = 'thumb'
    ): string {

        return app(ThumbnailService::class)
            ->thumbnail($asset, $conversion);
    }

}

if (! function_exists('asset_original')) {

    function asset_original(
        Asset $asset
    ): string {

        return app(ThumbnailService::class)
            ->original($asset);
    }

}

if (! function_exists('asset_url')) {

    function asset_url(
        Asset $asset
    ): string {

        return app(ThumbnailService::class)
            ->url($asset);
    }

}