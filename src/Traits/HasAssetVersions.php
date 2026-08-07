<?php

namespace Innopanda\AssetManager\Traits;

use Innopanda\AssetManager\Models\AssetVersion;
use Illuminate\Support\Facades\Storage;

trait HasAssetVersions
{
    /**
     * Relationship: Asset has many old versions
     */
    public function versions()
    {
        return $this->hasMany(AssetVersion::class)->orderBy('version_number', 'desc');
    }

    /**
     * Replace current asset with a new file and archive current state
     */
    public function replaceWithFile(string $newPath, string $mimeType, int $size, string $extension, ?int $userId = null): AssetVersion
    {
        // 1. Next Version Number Determine Karein
        $nextVersion = ($this->versions()->max('version_number') ?? 1) + 1;

        // 2. Current Active State ko Archive/Version History me Save Karein
        $archivedVersion = $this->versions()->create([
            'version_number' => $nextVersion - 1, // Current version is archived
            'disk'           => $this->disk,
            'path'           => $this->path,
            'mime_type'      => $this->mime_type,
            'size'           => $this->size,
            'extension'      => $this->extension,
            'created_by'     => $this->updated_by ?? $userId,
        ]);

        // 3. Current Asset Table ko New File Details se Update Karein
        $this->update([
            'path'       => $newPath,
            'mime_type'  => $mimeType,
            'size'       => $size,
            'extension'  => $extension,
            'updated_by' => $userId,
        ]);

        return $archivedVersion;
    }

    /**
     * Rollback asset to a specific old version
     */
    public function rollbackToVersion(int $versionId): bool
    {
        $version = $this->versions()->where('id', $versionId)->firstOrFail();

        // 1. Current state ka temporary backup banayein
        $currentPath = $this->path;
        $currentMime = $this->mime_type;
        $currentSize = $this->size;
        $currentExt  = $this->extension;

        // 2. Asset ko old version data par restore karein
        $this->update([
            'path'      => $version->path,
            'mime_type' => $version->mime_type,
            'size'      => $version->size,
            'extension' => $version->extension,
        ]);

        // 3. Rollbacked version history entry update karein
        $version->update([
            'path'      => $currentPath,
            'mime_type' => $currentMime,
            'size'      => $currentSize,
            'extension' => $currentExt,
        ]);

        return true;
    }
}