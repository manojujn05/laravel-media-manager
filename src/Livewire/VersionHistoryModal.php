<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Models\AssetVersion;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;

class VersionHistoryModal extends Component
{
    public bool $isOpen = false;
    public ?int $assetId = null;
    public ?Asset $asset = null;

    protected $listeners = [
        'openVersionHistory' => 'loadAssetVersions',
    ];

 public function loadAssetVersions(int $assetId): void
{
    $this->assetId = $assetId;
    $this->asset = Asset::with(['versions'])->find($assetId);
    $this->isOpen = true;
}

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->assetId = null;
        $this->asset = null;
    }

    public function rollback(int $versionId): void
    {
        if (!$this->asset) {
            return;
        }

        // Execute Trait Rollback Logic
        $this->asset->rollbackToVersion($versionId);

        session()->flash('message', 'Asset successfully rolled back!');
$this->dispatch('assetUpdated', assetId: $this->asset->id);
        $this->loadAssetVersions($this->asset->id);
    }

  public function render()
{
    
    return view('asset-manager::livewire.version-history-modal');
}
}