<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Innopanda\AssetManager\Models\Asset;

class AssetUploader extends Component
{

    use WithFileUploads;

    public $file;
    public ?int $folderId = null;

    protected $listeners = [
        'asset-selection-changed' => 'assetSelected',
        'folder-selected' => 'loadFolder',
        'asset-uploaded' => '$refresh',
    ];

    public function upload()
    {
        $this->validate([
            'file' => 'required|image|max:2048'
        ]);

        $path = $this->file->store(
            'assets',
            'public'
        );

        $asset = Asset::create([
            'title' => pathinfo(
                $this->file->getClientOriginalName(),
                PATHINFO_FILENAME
            ),
            'slug' => \Illuminate\Support\Str::slug(
                pathinfo(
                    $this->file->getClientOriginalName(),
                    PATHINFO_FILENAME
                )
            ),
            'disk' => 'public',
            'path' => $path,
            'mime_type' => $this->file->getMimeType(),
            'extension' => $this->file->getClientOriginalExtension(),
            'size' => $this->file->getSize(),
            'folder_id' => $this->folderId,
        ]);

        $this->dispatch(
            'asset-uploaded',
            assetId: $asset->id
        );

        $this->dispatch(
            'asset-uploaded',
            assetId: $asset->id
        );

        $this->reset('file');
    }

    public function setFolder($folderId): void
    {
        $this->folderId = $folderId;
    }

    public function render()
    {
        return view(
            'asset-manager::livewire.asset-uploader'
        );
    }
}
