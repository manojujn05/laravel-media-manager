<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Innopanda\AssetManager\Models\Asset;

class AssetCard extends Component
{
    public Asset $asset;

    public bool $selected = false;

    public function mount(Asset $asset): void
    {
        $this->asset = $asset;
    }

    public function toggleSelection(): void
    {
        $this->selected = ! $this->selected;

        $this->dispatch(
            'asset-selection-changed',
            assetId: $this->asset->id,
            selected: $this->selected
        );
    }

    public function toggleFavorite(): void
    {
        $this->asset->update([
            'is_favorite' => ! $this->asset->is_favorite,
        ]);

        $this->asset->refresh();
    }

    public function preview(): void
    {
        $this->dispatch(
            'preview-asset',
            assetId: $this->asset->id
        );
    }

    public function render()
    {
        return view(
            'asset-manager::livewire.livewire.components.asset-card'
        );
    }
}