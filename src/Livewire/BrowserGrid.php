<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Innopanda\AssetManager\Models\Asset;

class BrowserGrid extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sort = 'latest';

    public string $view = 'grid';

    public array $selected = [];

    protected string $paginationTheme = 'tailwind';

    #[On('asset-search')]
    public function updateSearch(string $search): void
    {
        $this->search = $search;

        $this->resetPage();
    }

    #[On('asset-sort')]
    public function updateSort(string $sort): void
    {
        $this->sort = $sort;

        $this->resetPage();
    }

    #[On('asset-view')]
    public function updateView(string $view): void
    {
        $this->view = $view;
    }

    public function toggleSelection(int $assetId): void
    {
        if (in_array($assetId, $this->selected)) {

            $this->selected = array_values(
                array_diff($this->selected, [$assetId])
            );

            return;
        }

        $this->selected[] = $assetId;
    }

    public function clearSelection(): void
    {
        $this->selected = [];
    }

    public function preview(int $assetId): void
    {
        $this->dispatch(
            'preview-asset',
            assetId: $assetId
        );
    }

    public function getAssetsProperty()
    {
        return Asset::query()

            ->when($this->search, function ($query) {

                $query->where('title', 'like', "%{$this->search}%");
            })

            ->when(
                $this->sort === 'latest',
                fn($q) => $q->latest()
            )

            ->when(
                $this->sort === 'oldest',
                fn($q) => $q->oldest()
            )

            ->when(
                $this->sort === 'name',
                fn($q) => $q->orderBy('title')
            )

            ->when(
                $this->sort === 'size',
                fn($q) => $q->orderByDesc('size')
            )

            ->paginate(24);
    }

    public function render()
    {
        return view(
            'asset-manager::livewire.components.browser-grid',
            [
                'assets' => $this->assets,
            ]
        );
    }
}