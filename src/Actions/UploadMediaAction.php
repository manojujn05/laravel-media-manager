<?php

namespace Innopanda\AssetManager\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Models\AssetActivityLog;
use Innopanda\AssetManager\DTOs\UploadMediaData;
use Innopanda\AssetManager\Services\AssetValidationService;
use Throwable;

class UploadMediaAction
{
    public function __construct(protected AssetValidationService $validator)
    {}

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
            // Validate file
            $this->validator->validate($file);

            $folderId = !empty($data->folder_id) ? $data->folder_id : null;
            $clientOriginalName = $file->getClientOriginalName();
            
            $mimeType = $file->getMimeType() ?: 'application/octet-stream';
            $extension = strtolower($file->getClientOriginalExtension());
            if (empty($extension)) {
                $extension = strtolower(pathinfo($clientOriginalName, PATHINFO_EXTENSION));
            }

            $size = $file->getSize();
            [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];
            $hash = hash_file('sha256', $file->getRealPath());

            if (!$asset && config('asset-manager.duplicates.enabled', true) && !$data->force_upload) {
                $duplicate = Asset::where('hash', $hash)
                    ->where('folder_id', $folderId)
                    ->first();
                if ($duplicate) {
                    $behavior = config('asset-manager.duplicates.behavior', 'warn');
                    if ($behavior === 'warn') {
                        throw new \Innopanda\AssetManager\Exceptions\AssetDuplicateException("This file already exists.", $duplicate);
                    } elseif ($behavior === 'reject') {
                        throw new \Innopanda\AssetManager\Exceptions\AssetDuplicateException("Duplicate uploads are not allowed.", $duplicate);
                    }
                }
            }

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

            $relativePath = $media->id . '/' . $media->file_name;

            $asset->update([
                'folder_id'   => $folderId ?? $asset->folder_id,
                'title'       => $data->title ?? pathinfo($clientOriginalName, PATHINFO_FILENAME),
                'path'        => $relativePath,
                'disk'        => $media->disk,
                'mime_type'   => $mimeType,
                'extension'   => $extension,
                'size'        => $media->size ?? $size,
                'hash'        => $hash,
                'width'       => $width,
                'height'      => $height,
                'alt'         => $data->alt ?? $asset->alt,
                'description' => $data->description ?? $asset->description,
            ]);

            // Log activity
            AssetActivityLog::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'action' => 'uploaded',
                'metadata' => [
                    'file_name' => $clientOriginalName,
                    'size' => $size,
                ]
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