<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Innopanda\AssetManager\Models\Folder;

class FolderNode extends Component
{
    public Folder $folder;

    public bool $editing = false;

    public string $name = '';

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
    }

    /**
     * Cancel editing
     */
    public function cancel(): void
    {
        $this->editing = false;

        $this->name = $this->folder->name;
    }
public function delete(): void
{
    if ($this->folder->children()->exists()) {
        session()->flash(
            'error',
            'Please delete child folders first.'
        );

        return;
    }

    if ($this->folder->assets()->exists()) {
        session()->flash(
            'error',
            'Folder contains assets.'
        );

        return;
    }

    $this->folder->delete();

    $this->dispatch('folder-deleted');
}
    public function render()
    {
        return view(
            'asset-manager::livewire.folder-node'
        );
    }
}