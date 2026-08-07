<?php

namespace Innopanda\AssetManager\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\DTOs\UploadMediaData;
use Throwable;

class UploadMediaAction
{
    public function execute(
        UploadedFile $file,
        UploadMediaData $data,
        ?Asset $asset = null
    ): Asset {
        Log::info('--- Asset Upload Process Started ---', [
            'original_file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'input_folder_id' => $data->folder_id ?? null,
            'passed_asset_id' => $asset?->id,
        ]);

        try {
            $folderId = !empty($data->folder_id) ? $data->folder_id : null;

            $clientOriginalName = $file->getClientOriginalName();
            
            $mimeType = $file->getMimeType() ?: 'application/octet-stream';
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (empty($extension)) {
                $extension = strtolower(pathinfo($clientOriginalName, PATHINFO_EXTENSION));
            }

            $size = $file->getSize();
            [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];

            if (!$asset) {
                $title = $data->title ?? pathinfo($clientOriginalName, PATHINFO_FILENAME);
                
                $asset = Asset::create([
                    'folder_id' => $folderId,
                    'title'     => $title,
                ]);
            }

            $collection = $data->collection ?? 'original';
            $disk = $data->disk ?? config('asset-manager.disk', 'public');

            $media = $asset
                ->addMedia($file)
                ->usingName($data->title ?? pathinfo($clientOriginalName, PATHINFO_FILENAME))
                ->withCustomProperties([
                    'alt'         => $data->alt ?? null,
                    'description' => $data->description ?? null,
                ])
                ->toMediaCollection($collection, $disk);

            // 💡 Fix: Full URL ki jagah Spatie ka relative path ya custom relative format save karein
            // Jaise: $media->id . '/' . $media->file_name ya fir relative path
            $relativePath = $media->id . '/' . $media->file_name;

            Log::info('Step 3: Updating Asset Model with Metadata...');

            $updated = $asset->update([
                'folder_id'   => $folderId ?? $asset->folder_id,
                'title'       => $data->title ?? pathinfo($clientOriginalName, PATHINFO_FILENAME),
                'path'        => $relativePath, // Ab yahan clean relative path save hoga (e.g., 6/Management-icon.png)
                'disk'        => $media->disk,
                'mime_type'   => $mimeType,
                'extension'   => $extension,
                'size'        => $media->size ?? $size,
                'width'       => $width,
                'height'      => $height,
                'alt'         => $data->alt ?? $asset->alt,
                'description' => $data->description ?? $asset->description,
            ]);

            Log::info('--- Asset Upload Process Completed Successfully ---');

            return $asset->fresh();

        } catch (Throwable $e) {
            Log::error('❌ Asset Upload Failed with Exception', [
                'error_message' => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
            ]);

            throw $e;
        }
    }
}