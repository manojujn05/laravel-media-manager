<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Innopanda\AssetManager\Models\Folder;
use Illuminate\Support\Facades\Log;

class FolderNode extends Component
{
    public Folder $folder;

    public bool $editing = false;
    public bool $showDeleteModal = false;
    public bool $showSubFolderModal = false;
    public string $name = '';
    public string $subFolderName = '';

    public function mount(Folder $folder): void
    {
        $this->folder = $folder;
        $this->name = $folder->name;
    }

    public function selectFolder(): void
    {
        $this->dispatch(
            'folder-selected',
            folderId: $this->folder->id
        );
    }
                
    /**
     * Enable edit mode
     */
    public function edit(): void
    {
        $this->editing = true;
    }

    /**
     * Save folder name
     */
    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->folder->update([
            'name' => $this->name,
        ]);

        $this->folder->refresh();

        $this->editing = false;

        $this->dispatch('folder-renamed');
        $this->dispatch('notify', 'Folder renamed successfully!');
    }

    /**
     * Cancel editing
     */
    public function cancel(): void
    {
        $this->editing = false;

        $this->name = $this->folder->name;
    }

    /**
     * Create a sub-folder inside this specific node folder
     */
    public function createSubFolder(): void
    {
        $this->validate([
            'subFolderName' => 'required|string|max:255',
        ]);

        Folder::create([
            'name' => $this->subFolderName,
            'parent_id' => $this->folder->id, // Ensures it is created as a child inside this specific folder
        ]);

        $this->subFolderName = '';
        $this->showSubFolderModal = false;

        $this->dispatch('folder-created');
        $this->dispatch('notify', 'Sub-folder created successfully!');
    }

    public function delete(): void
    {
        Log::info('--- DELETE ATTEMPT START ---', [
            'folder_id' => $this->folder->id,
            'folder_name' => $this->folder->name,
        ]);

        if ($this->folder->children()->exists()) {
            $this->showDeleteModal = false;

            $this->dispatch(
                'notify',
                message: 'Please delete child folders first.'
            );

            return;
        }

        if ($this->folder->assets()->exists()) {
            $this->showDeleteModal = false;

            $this->dispatch(
                'notify',
                message: 'Folder contains assets. Please empty it first.'
            );

            return;
        }

        $folderId = $this->folder->id;

        $this->folder->delete();

        $this->showDeleteModal = false;

        $this->dispatch(
            'folder-deleted',
            folderId: $folderId
        );

        $this->dispatch(
            'notify',
            message: 'Folder deleted successfully!'
        );

        Log::info('--- DELETE ATTEMPT END ---', [
            'folder_id' => $folderId,
        ]);
    }

    public function render()
    {
        return view(
            'asset-manager::livewire.folder-node'
        );
    }
}