<?php

namespace Innopanda\AssetManager\Traits;

use Innopanda\AssetManager\Models\Asset;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

trait WithBulkActions
{
    public array $selectedAssets = [];
    public bool $selectAll = false;
    public string|int|null $bulkFolderId = null;
    public array $bulkTagIds = [];
    public bool $showBulkMoveModal = false;
    public bool $showBulkTagModal = false;

    // Toggle Select All Logic
    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedAssets = $this->assets->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedAssets = [];
        }
    }

    // Clear Selection
    public function clearSelection(): void
    {
        $this->selectedAssets = [];
        $this->selectAll = false;
    }

    // 1. Bulk Delete
 // 1. Bulk Delete
public function bulkDelete(): void
{
    if (empty($this->selectedAssets)) return;

    // Ensure we flatten the array in case it got nested
    $ids = collect($this->selectedAssets)->flatten()->filter()->toArray();

    if (empty($ids)) return;

    $assets = Asset::whereIn('id', $ids)->get();
    
    foreach ($assets as $asset) {
        // Check if path is not null before deleting from storage
        if (!empty($asset->path)) {
            Storage::disk($asset->disk ?? config('asset-manager.disk', 'public'))->delete($asset->path);
        }
        $asset->delete();
    }

    $this->clearSelection();
    $this->dispatch('notify', 'Selected assets deleted successfully!');
}
public function deleteAsset($id): void
{
    $asset = Asset::find($id);

    if ($asset) {
        if (!empty($asset->path)) {
            Storage::disk($asset->disk ?? config('asset-manager.disk', 'public'))->delete($asset->path);
        }
        $asset->delete();
        
        $this->dispatch('notify', 'Asset deleted successfully!');
    }
}
    // 2. Bulk Favorite / Unfavorite
    public function bulkToggleFavorite(bool $status = true): void
    {
        if (empty($this->selectedAssets)) return;

        Asset::whereIn('id', $this->selectedAssets)->update(['is_favorite' => $status]);
        $this->clearSelection();
        $this->dispatch('notify', $status ? 'Added to favorites!' : 'Removed from favorites!');
    }

    // 3. Bulk Move
    public function bulkMove(): void
    {
        if (empty($this->selectedAssets)) return;

        Asset::whereIn('id', $this->selectedAssets)->update([
            'folder_id' => $this->bulkFolderId ? $this->bulkFolderId : null
        ]);

        $this->showBulkMoveModal = false;
        $this->bulkFolderId = null;
        $this->clearSelection();
        $this->dispatch('notify', 'Assets moved successfully!');
    }

    // 4. Bulk Tagging
    public function bulkAttachTags(): void
    {
        if (empty($this->selectedAssets) || empty($this->bulkTagIds)) return;

        $assets = Asset::whereIn('id', $this->selectedAssets)->get();
        foreach ($assets as $asset) {
            $asset->tags()->syncWithoutDetaching($this->bulkTagIds);
        }

        $this->showBulkTagModal = false;
        $this->bulkTagIds = [];
        $this->clearSelection();
        $this->dispatch('notify', 'Tags attached successfully!');
    }

    // 5. Bulk Download Zip
    public function bulkDownloadZip()
    {
        if (empty($this->selectedAssets)) return null;

        $assets = Asset::whereIn('id', $this->selectedAssets)->get();
        $zipFileName = 'assets-export-' . time() . '.zip';
        $diskPath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive();
        if ($zip->open($diskPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($assets as $asset) {
                $filePath = storage_path('app/public/' . $asset->path);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $asset->file_name ?? basename($asset->path));
                }
            }
            $zip->close();
        }

        $this->clearSelection();
        return response()->download($diskPath)->deleteFileAfterSend(true);
    }
}