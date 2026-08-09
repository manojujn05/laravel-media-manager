<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Models\Folder;
use Innopanda\AssetManager\DTOs\UploadMediaData;
use Innopanda\AssetManager\Actions\UploadMediaAction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Innopanda\AssetManager\Traits\WithBulkActions;

class MediaBrowser extends Component
{
    use WithPagination, WithFileUploads,WithBulkActions;

    protected string $paginationTheme = 'tailwind';

    /*
    |--------------------------------------------------------------------------
    | State
    |--------------------------------------------------------------------------
    */

    public string $search = '';

    public string $view = 'grid';

    public string $sort = 'latest';

    public string|int|null $folder = null;

    public string $type = '';

    public bool $favorites = false;

    public int $perPage = 24;

    public array $selected = [];

    public string|int|null $previewAsset = null;

    public $file = null;

    public string $pickerId = '';

    /*
    |--------------------------------------------------------------------------
    | Query String
    |--------------------------------------------------------------------------
    */

    protected $queryString = [
        'search'    => ['except' => ''],
        'view'      => ['except' => 'grid'],
        'sort'      => ['except' => 'latest'],
        'folder'    => ['except' => null],
        'type'      => ['except' => ''],
        'favorites' => ['except' => false],
    ];

    /*
    |--------------------------------------------------------------------------
    | Folder, Tag & File Actions
    |--------------------------------------------------------------------------
    */

    public function selectFolder(string|int|null $folderId = null): void
    {
        $this->folder = $folderId;
        $this->clearSelection();
        $this->resetPage();
    }



    public function resetFilters(): void
    {
        $this->reset(['search', 'type', 'favorites', 'folder']);
        $this->sort = 'latest';
        $this->resetPage();
    }

    public function updatedFile(UploadMediaAction $action): void
    {
        $this->uploadFile($action);
    }

    public function uploadFile(UploadMediaAction $action): void
    {
        Log::info('MediaBrowser: uploadFile action triggered', [
            'file_attached' => !is_null($this->file),
            'folder_id'     => $this->folder
        ]);

        $this->validate([
            'file' => 'required|file|max:10240',
        ]);

        $dto = new UploadMediaData(
            folder_id: $this->folder ? (int)$this->folder : null,
            disk: config('asset-manager.disk', 'public'),
            collection: 'original'
        );

        $action->execute($this->file, $dto);

        $this->reset('file');
        $this->dispatch('notify', 'File uploaded successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | Updating Hooks
    |--------------------------------------------------------------------------
    */

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFolder()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function updatingFavorites()
    {
        $this->resetPage();
    }



    /*
    |--------------------------------------------------------------------------
    | View Toggle
    |--------------------------------------------------------------------------
    */

    public function gridView(): void
    {
        $this->view = 'grid';
    }

    public function listView(): void
    {
        $this->view = 'list';
    }

    /*
    |--------------------------------------------------------------------------
    | Sorting
    |--------------------------------------------------------------------------
    */

    public function changeSort(string $sort): void
    {
        $this->sort = $sort;
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Selection
    |--------------------------------------------------------------------------
    */

    public bool $showBulkDeleteModal = false;

    public function toggleSelection(string|int $assetId): void
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
        $this->selectedAssets = [];
        $this->selectAll = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Preview
    |--------------------------------------------------------------------------
    */

    public function preview(string|int $assetId): void
    {
        $this->previewAsset = $assetId;
    }

    public function selectAsset(string|int $assetId): void
    {
        $asset = Asset::find($assetId);

        if (! $asset) {
            return;
        }

        $url = $asset->getFirstMediaUrl('original')
            ?: $asset->getFirstMediaUrl('assets')
            ?: (
                str_starts_with($asset->path, 'http')
                    ? $asset->path
                    : \Illuminate\Support\Facades\Storage::disk(
                        $asset->disk ?? 'public'
                    )->url($asset->path)
            );

        $payload = json_encode([
            'pickerId' => $this->pickerId,
            'id' => (int) $asset->id,
            'url' => $url,
        ]);

        $this->js("
            window.dispatchEvent(new CustomEvent('asset-manager:image-selected', {
                detail: {$payload}
            }));
        ");
    }

    public function deleteAsset(string|int $assetId): void
    {
        $asset = Asset::find($assetId);
        
        if ($asset) {
            try {
                $asset->delete();

                if ($this->previewAsset == $assetId) {
                    $this->previewAsset = null;
                }

                if (in_array($assetId, $this->selected)) {
                    $this->selected = array_values(array_diff($this->selected, [$assetId]));
                }

                $this->dispatch('notify', 'Asset deleted successfully!', 'success');
            } catch (\Exception $e) {
                Log::error('Failed to delete asset: ' . $e->getMessage());
                $this->dispatch('notify', 'Failed to delete asset.', 'error');
            }
        }
    }

    public function copyAssetUrl(string|int $assetId): void
    {
        $asset = Asset::find($assetId);
        if ($asset) {
            $url = $asset->getFirstMediaUrl('assets') ?: (str_starts_with($asset->path, 'http') ? $asset->path : \Illuminate\Support\Facades\Storage::disk($asset->disk ?? 'public')->url($asset->path));
            $this->dispatch('copy-to-clipboard', url: $url);
            $this->dispatch('notify', 'URL copied successfully!', 'success');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */

    public function getAssetsProperty()
    {
        $query = Asset::query();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
                    ->orWhere('alt', 'like', "%{$this->search}%")
                    ->orWhere('mime_type', 'like', "%{$this->search}%")
                    ->orWhere('extension', 'like', "%{$this->search}%");
            });
        }

        // Folder filter logic
        if ($this->folder) {
            $query->where('folder_id', $this->folder);
        } else {
            $query->whereNull('folder_id');
        }

        if ($this->favorites) {
            $query->where('is_favorite', true);
        }

        if ($this->type !== '') {
            $query->where('mime_type', 'like', $this->type . '%');
        }



        switch ($this->sort) {
            case 'oldest':
                $query->oldest();
                break;

            case 'name':
                $query->orderBy('title');
                break;

            case 'size':
                $query->orderByDesc('size');
                break;

            default:
                $query->latest();
        }

        return $query->paginate($this->perPage);
    }

    public function getFoldersProperty()
    {
        return Folder::query()
            ->where('parent_id', $this->folder)
            ->get();
    }

    public function getCurrentFolderProperty()
    {
        return $this->folder ? Folder::find($this->folder) : null;
    }



    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'asset-manager::livewire.media-browser',
            [
                'assets'        => $this->assets,
                'folders'       => $this->folders,
                'currentFolder' => $this->currentFolder,
            ]
        );
    }
}