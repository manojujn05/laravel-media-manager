<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Innopanda\AssetManager\Models\Folder;

class FolderTree extends Component
{
    public ?int $selectedFolder = null;

    protected $listeners = [
        'folder-created' => '$refresh',
        'folder-renamed' => '$refresh',
        'folder-deleted' => '$refresh',
    ];

    public function selectFolder(int $id): void
    {
        $this->selectedFolder = $id;

        $this->dispatch(
            'folder-selected',
            folderId: $id
        );
    }

    public function render()
    {
        $folders = Folder::query()
            ->whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('name')
            ->get();

        return view(
            'asset-manager::livewire.folder-tree',
            [
                'folders' => $folders,
            ]
        );
    }
}