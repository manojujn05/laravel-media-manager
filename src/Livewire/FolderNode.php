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
        Log::info('--- DELETE ATTEMPT START ---');
        Log::info('Target Folder ID: ' . $this->folder->id);
        Log::info('Target Folder Name: ' . $this->folder->name);

        if ($this->folder->children()->exists()) {
            Log::info('Blocked: Folder has child folders.');
            $this->showDeleteModal = false;
            session()->flash('error', 'Please delete child folders first.');
            $this->dispatch('notify', 'Please delete child folders first.');
            return;
        }

        if ($this->folder->assets()->exists()) {
            Log::info('Blocked: Folder contains assets count: ' . $this->folder->assets()->count());
            $this->showDeleteModal = false;
            session()->flash('error', 'Folder contains assets. Please empty it first.');
            $this->dispatch('notify', 'Folder contains assets.');
            return;
        }

        Log::info('Passed all checks. Executing delete for ID: ' . $this->folder->id);
        $this->folder->delete();

        $this->showDeleteModal = false;
        $this->dispatch('folder-deleted');
        $this->dispatch('notify', 'Folder deleted successfully!');
        Log::info('--- DELETE ATTEMPT END ---');
    }

    public function render()
    {
        return view(
            'asset-manager::livewire.folder-node'
        );
    }
}