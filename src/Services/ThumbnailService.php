<?php

namespace Innopanda\AssetManager\Services;

use Innopanda\AssetManager\Models\Asset;

class ThumbnailService
{
    /**
     * Get thumbnail url.
     */
    public function thumbnail(
        Asset $asset,
        string $conversion = 'thumb'
    ): string {

        if (! $asset->hasMedia('assets')) {
            return '';
        }

        return $asset
            ->getFirstMedia('assets')
            ->getUrl($conversion);
    }

    /**
     * Get original image.
     */
    public function original(Asset $asset): string
    {
        if (! $asset->hasMedia('assets')) {
            return '';
        }

        return $asset
            ->getFirstMedia('assets')
            ->getUrl();
    }

    /**
     * Alias of original.
     */
    public function url(Asset $asset): string
    {
        return $this->original($asset);
    }
}