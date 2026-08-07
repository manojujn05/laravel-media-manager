<?php

namespace Innopanda\AssetManager\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\DTOs\UploadMediaData;

class ReplaceMediaAction
{
    public function execute(
        Asset $asset,
        UploadedFile $file,
        UploadMediaData $data
    ): Asset {
        $disk = $data->disk ?? $asset->disk ?? 'public';

        // 1. Storage se purani physical file delete karein
        if ($asset->path && Storage::disk($disk)->exists($asset->path)) {
            Storage::disk($disk)->delete($asset->path);
        }

        // 2. Spatie Media Library collection clear karein (agar module setup hai)
        if (method_exists($asset, 'clearMediaCollection')) {
            $asset->clearMediaCollection($data->collection);
        }

        // 3. Nayi file physical disk me store karein
        $storedPath = $file->store('assets', $disk);

        // Image dimensions extract karein
        [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];

        // 4. Existing Database Model attributes update karein
        $asset->update([
            'title'       => $data->title ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'path'        => $storedPath,
            'disk'        => $disk,
            'mime_type'   => $file->getClientMimeType(),
            'extension'   => strtolower($file->getClientOriginalExtension()),
            'size'        => $file->getSize(),
            'width'       => $width,
            'height'      => $height,
            'alt'         => $data->alt ?? $asset->alt,
            'description' => $data->description ?? $asset->description,
        ]);

        // 5. Spatie Media Library attach karein (Optional)
        if (method_exists($asset, 'addMedia')) {
            $asset->addMedia($file)
                ->usingName($asset->title)
                ->withCustomProperties([
                    'alt'         => $asset->alt,
                    'description' => $asset->description,
                ])
                ->toMediaCollection($data->collection, $disk);
        }

        return $asset;
    }
}