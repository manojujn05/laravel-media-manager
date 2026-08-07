<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Innopanda\AssetManager\Models\Folder;

class CreateFolder extends Component
{
    public bool $open = false;

    public string $name = '';

    public ?int $parentId = null;

    protected $listeners = [
        'open-create-folder' => 'openModal',
    ];

    public function openModal(?int $parentId = null): void
    {
        $this->resetValidation();

        $this->name = '';

        $this->parentId = $parentId;

        $this->open = true;
    }

    public function closeModal(): void
    {
        $this->open = false;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Folder::create([
            'name' => $this->name,
            'parent_id' => $this->parentId,
        ]);

        $this->dispatch('folder-created');

        $this->closeModal();
    }

    public function render()
    {
        return view('asset-manager::livewire.create-folder', [
            'folders' => Folder::orderBy('name')->get(),
        ]);
    }
}