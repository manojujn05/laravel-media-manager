<?php

namespace Innopanda\AssetManager\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Models\AssetActivityLog;
use Innopanda\AssetManager\DTOs\UploadMediaData;
use Innopanda\AssetManager\Services\AssetValidationService;
use Throwable;

class ReplaceMediaAction
{
    public function __construct(protected AssetValidationService $validator)
    {}

    public function execute(
        Asset $asset,
        UploadedFile $file,
        UploadMediaData $data
    ): Asset {
        Log::info('--- Asset Replace Process Started ---', [
            'asset_id' => $asset->id,
            'new_file_name' => $file->getClientOriginalName(),
        ]);

        try {
            $this->validator->validate($file);

            return DB::transaction(function () use ($asset, $file, $data) {

                /*
                 * ------------------------------------------------------------
                 * 1. Determine next version number
                 * ------------------------------------------------------------
                 */
                $currentMax = $asset->versions()->max('version') ?? 0;
                $versionNumber = $currentMax + 1;

                /*
                 * ------------------------------------------------------------
                 * 2. Preserve current active asset as a version
                 * ------------------------------------------------------------
                 */
                $asset->versions()->create([
                    'version' => $versionNumber,
                    'disk' => $asset->disk,
                    'path' => $asset->path,
                    'file_name' => basename($asset->path ?? ''),
                    'mime_type' => $asset->mime_type,
                    'extension' => $asset->extension,
                    'size' => $asset->size,
                    'hash' => $asset->hash,
                    'width' => $asset->width,
                    'height' => $asset->height,
                    'metadata' => [
                        'alt' => $asset->alt,
                        'description' => $asset->description,
                    ],
                    'change_note' => 'State prior to replacement',
                    'created_by' => $asset->updated_by ?? $asset->created_by,
                ]);

                /*
                 * ------------------------------------------------------------
                 * 3. Calculate new file information
                 * ------------------------------------------------------------
                 */
                $disk = $asset->disk ?? config('asset-manager.disk', 'public');

                // IMPORTANT:
                // The active file must always be in "original".
                $collection = 'original';

                $clientOriginalName = $file->getClientOriginalName();

                $mimeType = $file->getMimeType()
                    ?: 'application/octet-stream';

                $extension = strtolower(
                    $file->getClientOriginalExtension()
                );

                if (empty($extension)) {
                    $extension = strtolower(
                        pathinfo(
                            $clientOriginalName,
                            PATHINFO_EXTENSION
                        )
                    );
                }

                $size = $file->getSize();

                [$width, $height] = @getimagesize(
                    $file->getRealPath()
                ) ?: [null, null];

                $hash = hash_file(
                    'sha256',
                    $file->getRealPath()
                );

                $title = $data->title
                    ?? pathinfo(
                        $clientOriginalName,
                        PATHINFO_FILENAME
                    );

                /*
                 * ------------------------------------------------------------
                 * 4. Remove current active Spatie media from 'original' collection
                 *    We rename its collection to 'versions' to preserve the 
                 *    physical file for asset_versions, rather than deleting it.
                 * ------------------------------------------------------------
                 */
                $oldMedia = $asset->getFirstMedia('original');
                if ($oldMedia) {
                    // Update DB record only so the physical file remains
                    $oldMedia->update(['collection_name' => 'versions']);
                }

                /*
                 * ------------------------------------------------------------
                 * 5. Add replacement as the ONLY active original media
                 * ------------------------------------------------------------
                 */
                $media = $asset
                    ->addMedia($file)
                    ->usingName($title)
                    ->withCustomProperties([
                        'alt' => $data->alt ?? $asset->alt,
                        'description' => $data->description ?? $asset->description,
                    ])
                    ->toMediaCollection('original', $disk);

                /*
                 * ------------------------------------------------------------
                 * 5. IMPORTANT:
                 * Keep old media files for version history.
                 *
                 * We do NOT clear/delete the old media here because the
                 * asset_versions records point to the previous files.
                 * ------------------------------------------------------------
                 */

                $relativePath = $media->id . '/' . $media->file_name;

                /*
                 * ------------------------------------------------------------
                 * 6. Update Asset
                 * ------------------------------------------------------------
                 */
                $asset->update([
                    'title' => $title,
                    'path' => $relativePath,
                    'disk' => $media->disk,
                    'mime_type' => $mimeType,
                    'extension' => $extension,
                    'size' => $media->size ?? $size,
                    'hash' => $hash,
                    'width' => $width,
                    'height' => $height,
                    'alt' => $data->alt ?? $asset->alt,
                    'description' => $data->description ?? $asset->description,
                ]);

                /*
                 * ------------------------------------------------------------
                 * 7. Activity log
                 * ------------------------------------------------------------
                 */
                AssetActivityLog::create([
                    'asset_id' => $asset->id,
                    'user_id' => auth()->id(),
                    'action' => 'replaced',
                    'metadata' => [
                        'version' => $versionNumber,
                        'new_file_name' => $clientOriginalName,
                        'new_media_id' => $media->id,
                    ],
                ]);

                Log::info('--- Asset Replace Process Completed Successfully ---', [
                    'asset_id' => $asset->id,
                    'version' => $versionNumber,
                    'media_id' => $media->id,
                    'new_path' => $relativePath,
                ]);

                return $asset->fresh();
            });
        } catch (Throwable $e) {
            Log::error('❌ Asset Replace Failed with Exception', [
                'asset_id' => $asset->id,
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }
}