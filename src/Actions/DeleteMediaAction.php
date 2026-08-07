<?php

namespace Innopanda\AssetManager\Actions;

use Innopanda\AssetManager\Models\Asset;
use Illuminate\Support\Facades\Storage;

class DeleteMediaAction
{
    public function execute(
        Asset $asset,
        string $collection = 'assets'
    ): void {
        // 1. Clear Spatie Media Library collection (if used)
        if (method_exists($asset, 'clearMediaCollection')) {
            $asset->clearMediaCollection($collection);
        }

        // 2. Clear physical storage file
        if ($asset->disk && $asset->path && Storage::disk($asset->disk)->exists($asset->path)) {
            Storage::disk($asset->disk)->delete($asset->path);
        }

        // 3. Delete database record
        $asset->delete();
    }
}