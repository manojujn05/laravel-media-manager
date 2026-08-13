<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;

class BrowserToolbar extends Component
{
    public string $search = '';
    public string $sort = 'latest';
    public string $view = 'grid';
    public function updatedSearch(): void
    {
        $this->dispatch(
            'asset-search',
            search: $this->search
        );
    }

    public function updatedSort(): void
    {
        $this->dispatch(
            'asset-sort',
            sort: $this->sort
        );
    }
    public function gridView(): void
    {
        $this->view = 'grid';
        $this->dispatch(
            'asset-view',
            view: 'grid'
        );
    }

    public function listView(): void
    {
        $this->view = 'list';
        $this->dispatch(
            'asset-view',
            view: 'list'
        );
    }

    public function openUploader(): void
    {
        $this->dispatch('open-upload-modal');
    }

    public function render()
    {
        return view(
            'asset-manager::livewire.components.browser-toolbar'
        );
    }
}