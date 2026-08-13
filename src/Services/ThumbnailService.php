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

        if (! $asset->hasMedia('original')) {
            return '';
        }

        return $asset
            ->getFirstMedia('original')
            ->getUrl($conversion);
    }

    /**
     * Get original image.
     */
    public function original(Asset $asset): string
    {
        if (! $asset->hasMedia('original')) {
            return '';
        }

        return $asset
            ->getFirstMedia('original')
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