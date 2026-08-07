<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Innopanda\AssetManager\Models\Asset;

class BrowserPreview extends Component
{
    public ?Asset $asset = null;

    #[On('preview-asset')]
    public function loadAsset(int $assetId): void
    {
        $this->asset = Asset::find($assetId);
    }

    public function replace(): void
    {
        $this->dispatch(
            'open-replace-modal',
            assetId: $this->asset?->id
        );
    }

   public function download()
{
    if (! $this->asset) {
        return;
    }

    return response()->download(
        $this->asset->getFirstMedia('assets')->getPath()
    );
}

    public function delete(): void
    {
        if (! $this->asset) {
            return;
        }

        $this->asset->delete();

        $this->dispatch('asset-deleted');

        $this->asset = null;
    }

    public function render()
    {
        return view(
            'asset-manager::livewire.components.browser-preview'
        );
    }
}