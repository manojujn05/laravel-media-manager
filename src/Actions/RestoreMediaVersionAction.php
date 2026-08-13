<?php

namespace Innopanda\AssetManager\Actions;

use Illuminate\Support\Facades\DB;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Models\AssetVersion;
use Innopanda\AssetManager\Models\AssetActivityLog;
use Throwable;

class RestoreMediaVersionAction
{
    public function execute(Asset $asset, AssetVersion $versionToRestore): Asset
    {
        try {
            return DB::transaction(function () use ($asset, $versionToRestore) {
                // 1. Create a version for the current state before replacing
                $currentMax = $asset->versions()->max('version') ?? 0;
                $newVersionForCurrentState = $currentMax + 1;

                $asset->versions()->create([
                    'version' => $newVersionForCurrentState,
                    'disk' => $asset->disk,
                    'path' => $asset->path,
                    'file_name' => basename($asset->path ?? ''),
                    'mime_type' => $asset->mime_type,
                    'extension' => $asset->extension,
                    'size' => $asset->size,
                    'hash' => $asset->hash,
                    'width' => $asset->width,
                    'height' => $asset->height,
                    'metadata' => ['alt' => $asset->alt, 'description' => $asset->description],
                    'change_note' => 'State prior to restoration',
                    'created_by' => $asset->updated_by ?? $asset->created_by,
                ]);

                // 2. We don't modify the version history, we just pull the data from $versionToRestore 
                // and update the Asset table to make it current again.
                // Note: The physical file path from $versionToRestore must exist.
                
                $asset->update([
                    'path'        => $versionToRestore->path,
                    'disk'        => $versionToRestore->disk,
                    'mime_type'   => $versionToRestore->mime_type,
                    'extension'   => $versionToRestore->extension,
                    'size'        => $versionToRestore->size,
                    'hash'        => $versionToRestore->hash,
                    'width'       => $versionToRestore->width,
                    'height'      => $versionToRestore->height,
                    'alt'         => $versionToRestore->metadata['alt'] ?? $asset->alt,
                    'description' => $versionToRestore->metadata['description'] ?? $asset->description,
                ]);

                // 3. Log activity
                AssetActivityLog::create([
                    'asset_id' => $asset->id,
                    'user_id' => auth()->id(),
                    'action' => 'restored',
                    'metadata' => [
                        'restored_from_version' => $versionToRestore->version,
                    ]
                ]);

                return $asset->fresh();
            });
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
