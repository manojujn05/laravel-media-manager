<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;

class AssetPickerModal extends Component
{
    public bool $open = false;


    protected $listeners = [
        'open-asset-picker' => 'open',
        'asset-selected' => 'assetSelected',
    ];


    public function open(): void
    {
        $this->open = true;
    }


    public function close(): void
    {
        $this->open = false;
    }


    public function assetSelected($assetId): void
    {
        $this->dispatch(
            'asset-picked',
            assetId: $assetId
        );


        $this->close();
    }


    public function render()
    {
        return view(
            'asset-manager::livewire.asset-picker-modal'
        );
    }
}