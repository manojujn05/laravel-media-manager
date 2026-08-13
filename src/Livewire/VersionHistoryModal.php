<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Actions\RestoreMediaVersionAction;
use Illuminate\Support\Facades\Log;

class VersionHistoryModal extends Component
{
    public bool $isOpen = false;
    public ?int $assetId = null;
    public ?Asset $asset = null;

    #[On('open-version-history-modal')]
    public function openModal($assetId = null): void
    {
        if (is_array($assetId) && isset($assetId['assetId'])) {
            $assetId = $assetId['assetId'];
        }

        if ($assetId) {
            $this->assetId = (int)$assetId;
            $this->asset = Asset::with(['versions', 'usages.usable'])->find($this->assetId);
            if ($this->asset) {
                $this->isOpen = true;
            }
        }
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->asset = null;
        $this->assetId = null;
    }

    public function restoreVersion(int $versionId, RestoreMediaVersionAction $action): void
    {
        if (!$this->asset) return;

        try {
            $versionToRestore = $this->asset->versions()->find($versionId);
            if ($versionToRestore) {
                $action->execute($this->asset, $versionToRestore);
                $this->dispatch('notify', message: 'Version restored successfully!', type: 'success');
                $this->dispatch('assetUpdated', assetId: $this->asset->id);
                $this->dispatch('preview-asset', assetId: $this->asset->id);
                $this->closeModal();
            }
        } catch (\Exception $e) {
            Log::error("Failed to restore version: " . $e->getMessage());
            $this->dispatch('notify', message: 'Failed to restore version.', type: 'error');
        }
    }

    public function render()
    {
        return view('asset-manager::livewire.version-history-modal');
    }
}
