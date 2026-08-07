<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Innopanda\AssetManager\Models\Asset;


class AssetPicker extends Component
{

    use WithPagination;


    public string $search = '';

    public ?int $selectedAsset = null;
    public ?int $folderId = null;

protected $listeners = [

    'asset-selection-changed'=>'assetSelected',

    'asset-uploaded'=>'refreshAssets',
    'folder-selected'=>'loadFolder',


];

public function loadFolder($folderId)
{

$this->folderId = $folderId;

}

    public function assetSelected($assetId, $selected): void
    {

        if($selected){

            $this->selectedAsset = $assetId;


            $this->dispatch(
                'asset-selected',
                assetId:$assetId
            );

        }

    }
public function refreshAssets($assetId)
{

    $this->resetPage();


    $this->dispatch(
        'asset-selected',
        assetId:$assetId
    );

}


 public function render()
{
    $assets = Asset::query()

        // Search
        ->when(
            $this->search,
            function ($query) {

                $query->where(
                    'name',
                    'ilike',
                    "%{$this->search}%"
                );

            }
        )


        // Folder Filter
        ->when(
            $this->folderId,
            function ($query) {

                $query->where(
                    'folder_id',
                    $this->folderId
                );

            }
        )


        ->latest()

        ->paginate(24);



    return view(
        'asset-manager::livewire.livewire.asset-picker',
        compact('assets')
    );
}
}