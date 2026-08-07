<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Innopanda\AssetManager\Models\Folder;
use Innopanda\AssetManager\Models\Asset;
// use Innopanda\AssetManager\Models\AssetTag;

class BrowserSidebar extends Component
{
    public ?int $selectedFolder = null;

    public array $selectedTags = [];

    public string $type = 'all';


    public function selectFolder(?int $folderId): void
    {
        $this->selectedFolder = $folderId;

        $this->dispatch(
            'folder-selected',
            folderId: $folderId
        );
    }


    public function toggleTag(int $tagId): void
    {
        if (in_array($tagId, $this->selectedTags)) {

            $this->selectedTags = array_values(
                array_diff($this->selectedTags, [$tagId])
            );

        } else {

            $this->selectedTags[] = $tagId;

        }


        $this->dispatch(
            'tags-selected',
            tags: $this->selectedTags
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


                'tags' => [],


                // Footer Storage Data
                'usedStorage' => $usedStorage,

                'storagePercentage' => $storagePercentage,

            ]
        );
    }
}