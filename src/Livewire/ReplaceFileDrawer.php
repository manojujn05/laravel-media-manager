<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Actions\ReplaceMediaAction;
use Innopanda\AssetManager\DTOs\UploadMediaData;
use Illuminate\Support\Facades\Log;

class ReplaceFileDrawer extends Component
{
    use WithFileUploads;

    public bool $isOpen = false;
    public ?int $assetId = null;
    public ?Asset $asset = null;
    public $newFile;
    public ?string $errorMessage = null;

    protected $listeners = [
        'openReplaceDrawer' => 'openDrawer',
        'open-replace-drawer' => 'openDrawer',
        'open-replace-modal' => 'openDrawer'
    ];

    public function openDrawer($assetId = null): void
    {
        if (is_array($assetId) && isset($assetId['assetId'])) {
            $assetId = $assetId['assetId'];
        }

        if ($assetId) {
            $this->assetId = (int)$assetId;
            $this->asset = Asset::find($this->assetId);
            $this->reset(['newFile', 'errorMessage']);
            $this->isOpen = true;
        }
    }

    protected $rules = [
        'newFile' => 'required|file', 
    ];

    public function closeDrawer(): void
    {
        $this->isOpen = false;
        $this->reset(['assetId', 'asset', 'newFile', 'errorMessage']);
    }

    public function replaceFile(ReplaceMediaAction $action): void
    {
        $this->validate();
        $this->errorMessage = null;

        if (!$this->asset) {
            return;
        }

        try {
            $data = new UploadMediaData(
                title: $this->asset->title,
                disk: $this->asset->disk
            );

            $action->execute($this->asset, $this->newFile, $data);

            $this->dispatch('notify', message: 'Asset replaced successfully!', type: 'success');
            $this->dispatch('assetUpdated', assetId: $this->asset->id);
            $this->dispatch('preview-asset', assetId: $this->asset->id); // Refresh preview
            $this->closeDrawer();
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
            $this->reset('newFile');
        }
    }

    public function render()
    {
        return view('asset-manager::livewire.replace-file-drawer');
    }
    public function updatedNewFile(): void
    {
        Log::info('REPLACE FILE UPLOADED', [
            'asset_id' => $this->assetId,
            'class' => $this->newFile ? get_class($this->newFile) : null,
            'name' => $this->newFile?->getClientOriginalName(),
            'size' => $this->newFile?->getSize(),
        ]);
    }
}