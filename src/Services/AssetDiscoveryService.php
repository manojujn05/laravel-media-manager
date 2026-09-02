<?php

namespace Innopanda\AssetManager\Services;

use Illuminate\Support\Facades\Storage;
use Innopanda\AssetManager\Models\Asset;
use Illuminate\Support\Facades\Log;

class AssetDiscoveryService
{
    /**
     * Synchronize the database with the physical files on the disk.
     */
    public function sync(?string $diskName = null, ?string $rootPath = null): array
    {
        $diskName = $diskName ?? config('asset-manager.disk', 'public');
        $rootPath = $rootPath ?? config('asset-manager.sync.root_path', '/');
        
        $disk = Storage::disk($diskName);
        
        $stats = [
            'discovered' => 0,
            'created' => 0,
            'updated' => 0,
            'unchanged' => 0,
            'missing' => 0,
            'failed' => 0,
        ];

        try {
            $driver = $disk->getDriver();
            // Flysystem V3 listContents returns an iterable generator
            $contents = $driver->listContents($rootPath, true)
                ->filter(fn ($attributes) => $attributes->isFile());
            
            $discoveredPaths = [];

            foreach ($contents as $fileAttributes) {
                $path = $fileAttributes->path();
                $stats['discovered']++;
                $discoveredPaths[] = $path;
                
                try {
                    $this->processFile($diskName, $disk, $path, $stats);
                } catch (\Throwable $e) {
                    $stats['failed']++;
                    Log::error("Failed to sync asset: {$path}", ['error' => $e->getMessage()]);
                }
            }

            // Find missing items in the database that are no longer on disk
            $lookup = array_flip($discoveredPaths);
            
            Asset::where('disk', $diskName)
                ->where(function($query) use ($rootPath) {
                    if ($rootPath && $rootPath !== '/') {
                        $query->where('path', 'like', trim($rootPath, '/') . '/%');
                    }
                })
                ->chunkById(1000, function ($assets) use ($lookup, &$stats, $disk) {
                    foreach ($assets as $asset) {
                        if (!isset($lookup[$asset->path])) {
                            // Double check to be absolutely sure
                            if (!$disk->exists($asset->path)) {
                                $stats['missing']++;
                                Log::warning("Asset exists in database but missing from physical disk: {$asset->path}");
                                // We purposefully do NOT delete the asset here to prevent destructive actions
                                // and preserve relationships, as per requirements.
                            }
                        }
                    }
                });

        } catch (\Throwable $e) {
            Log::error("Asset sync failed: " . $e->getMessage());
            throw $e;
        }

        return $stats;
    }

    protected function processFile(string $diskName, $disk, string $path, array &$stats): void
    {
        $existingAsset = Asset::where('disk', $diskName)->where('path', $path)->first();
        
        try {
            $size = $disk->size($path);
        } catch (\Throwable $e) {
            $size = 0;
        }
        
        if ($existingAsset) {
            if ($existingAsset->size !== $size) {
                try {
                    $mimeType = $disk->mimeType($path) ?: 'application/octet-stream';
                } catch (\Throwable $e) {
                    $mimeType = 'application/octet-stream';
                }
                
                $existingAsset->update([
                    'size' => $size,
                    'mime_type' => $mimeType,
                ]);
                $stats['updated']++;
            } else {
                $stats['unchanged']++;
            }
        } else {
            try {
                $mimeType = $disk->mimeType($path) ?: 'application/octet-stream';
            } catch (\Throwable $e) {
                $mimeType = 'application/octet-stream';
            }
            
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $title = pathinfo($path, PATHINFO_FILENAME);
            
            Asset::create([
                'title' => $title,
                'disk' => $diskName,
                'path' => $path,
                'mime_type' => $mimeType,
                'extension' => $extension,
                'size' => $size,
            ]);
            
            $stats['created']++;
        }
    }
}
