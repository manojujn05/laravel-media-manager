<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Innopanda\AssetManager\Models\Asset;

class ReplaceFileDrawer extends Component
{
    use WithFileUploads;

    public bool $isOpen = false;
    public ?int $assetId = null;
    public ?Asset $asset = null;
    public $newFile;

    protected $listeners = [
        'openReplaceDrawer' => 'openDrawer',
        'open-replace-drawer' => 'openDrawer'
    ];

    public function openDrawer($assetId = null): void
    {
        // Livewire v3 payload parameter check
        if (is_array($assetId) && isset($assetId['assetId'])) {
            $assetId = $assetId['assetId'];
        }

        if ($assetId) {
            $this->assetId = (int)$assetId;
            $this->asset = Asset::find($this->assetId);
            $this->reset(['newFile']);
            $this->isOpen = true;
        }
    }

    protected $rules = [
        'newFile' => 'required|file|max:51200', // 50MB Max
    ];

    public function closeDrawer(): void
    {
        $this->isOpen = false;
        $this->reset(['assetId', 'asset', 'newFile']);
    }

    public function replaceFile(): void
    {
        $this->validate();

        if (!$this->asset) {
            return;
        }

        $storedPath = $this->newFile->store('assets/uploads', 'public');

        $this->asset->replaceWithFile(
            newPath: $storedPath,
            mimeType: $this->newFile->getClientMimeType(),
            size: $this->newFile->getSize(),
            extension: $this->newFile->getClientOriginalExtension(),
            userId: auth()->id()
        );

        session()->flash('message', 'Asset replaced successfully! Old version archived.');

        $this->dispatch('assetUpdated', assetId: $this->asset->id);
        $this->closeDrawer();
    }

    public function render()
    {
        return view('asset-manager::livewire.replace-file-drawer');
    }
}