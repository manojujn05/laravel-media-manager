<?php

namespace Innopanda\AssetManager\Livewire;
use Livewire\Component;
use Innopanda\AssetManager\Models\Folder;
use Innopanda\AssetManager\Models\Asset;

class BrowserSidebar extends Component
{
    public ?int $selectedFolder = null;
    public string $type = 'all';
    public function selectFolder(?int $folderId): void
    {
        $this->selectedFolder = $folderId;

        $this->dispatch(
            'folder-selected',
            folderId: $folderId
        );
    }

   public function filterType(string $type): void
    {
        $this->type = $type;

        $this->dispatch(
            'type-filter',
            type: $type
        );
    }



    public function render()
    {

        // Storage Calculation
        $usedBytes = Asset::sum('size');


        $storageLimit = config(
            'asset-manager.storage_limit',
            1073741824 // 1GB
        );


        $storagePercentage = $storageLimit > 0

            ? min(
                round(($usedBytes / $storageLimit) * 100),
                100
            )

            : 0;


        $usedStorage = \Illuminate\Support\Number::fileSize(
            $usedBytes
        );



        return view(
            'asset-manager::livewire.livewire.components.browser-sidebar',
            [

                'folders' => Folder::whereNull('parent_id')
                    ->with('childrenRecursive')
                    ->orderBy('name')
                    ->get(),



                // Footer Storage Data
                'usedStorage' => $usedStorage,

                'storagePercentage' => $storagePercentage,

            ]
        );
    }
}