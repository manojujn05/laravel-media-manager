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
use Innopanda\AssetManager\Traits\WithConfirmation;

class MediaBrowser extends Component
{
    use WithPagination, WithFileUploads, WithBulkActions, WithConfirmation;

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

    public int $perPage = 10;

    public string|int|null $previewAsset = null;

    public $file = null;
    

    public string $pickerId = '';
    
    public bool $multiple = false;
    public array $pickerSelectedAssets = [];

    public bool $showInUseModal = false;
    public string $inUseMessage = '';
    public array $inUseUsages = [];

    public function mount(string $pickerId = '', bool $multiple = false): void
    {
        $this->pickerId = $pickerId;
        $this->multiple = $multiple;
    }

    #[Livewire\Attributes\On('asset-picker-opened')]
    public function handlePickerOpened(string $pickerId, array $selection): void
    {
        if ($this->pickerId !== $pickerId) {
            return;
        }

        $this->pickerSelectedAssets = array_values(
            array_map('intval', array_filter($selection, fn($val) => is_numeric($val)))
        );
    }

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

    public bool $hasDuplicate = false;
    public ?int $duplicateAssetId = null;
    public bool $forceUpload = false;

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
        
        $this->hasDuplicate = false;
        $this->duplicateAssetId = null;

        $dto = new UploadMediaData(
            folder_id: $this->folder ? (int)$this->folder : null,
            disk: config('asset-manager.disk', 'public'),
            collection: 'original',
            force_upload: $this->forceUpload
        );

        try {
            $action->execute($this->file, $dto);
            $this->reset(['file', 'forceUpload']);
            $this->dispatch('notify', 'File uploaded successfully!', 'success');
        } catch (\Innopanda\AssetManager\Exceptions\AssetDuplicateException $e) {
            $this->hasDuplicate = true;
            $this->duplicateAssetId = $e->duplicateAsset?->id;
        } catch (\Exception $e) {
            Log::error('Upload Error: ' . $e->getMessage());
            $this->dispatch('notify', 'Upload failed: ' . $e->getMessage(), 'error');
            $this->reset(['file', 'forceUpload']);
        }
    }

    public function uploadAnyway(UploadMediaAction $action): void
    {
        $this->forceUpload = true;
        $this->uploadFile($action);
    }

    public function useExisting(): void
    {
        if ($this->duplicateAssetId) {
            $this->preview($this->duplicateAssetId);
        }
        $this->cancelDuplicate();
    }

    public function cancelDuplicate(): void
    {
        $this->hasDuplicate = false;
        $this->duplicateAssetId = null;
        $this->reset(['file', 'forceUpload']);
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

    /*
    |--------------------------------------------------------------------------
    | Preview
    |--------------------------------------------------------------------------
    */

    public function preview(string|int $assetId): void
    {
        $this->previewAsset = $assetId;
    }

    public function togglePickerSelection(int $assetId): void
    {
        \Illuminate\Support\Facades\Log::info('Asset picker selection', [
            'multiple' => $this->multiple,
            'asset_id' => $assetId,
            'before' => $this->pickerSelectedAssets,
        ]);

        if (in_array($assetId, $this->pickerSelectedAssets)) {
            $this->pickerSelectedAssets = array_values(array_diff($this->pickerSelectedAssets, [$assetId]));
        } else {
            $this->pickerSelectedAssets[] = $assetId;
        }

        \Illuminate\Support\Facades\Log::info('Asset picker selection after', [
            'selected' => $this->pickerSelectedAssets,
        ]);
    }

    public function confirmSelection(): void
    {
        if (empty($this->pickerSelectedAssets)) {
            return;
        }

        $assets = Asset::whereIn('id', $this->pickerSelectedAssets)
            ->get()
            ->sortBy(fn($asset) => array_search($asset->id, $this->pickerSelectedAssets))
            ->values();
        $payloadData = [];

        foreach ($assets as $asset) {
            $url = $asset->getFirstMediaUrl('original')
                ?: $asset->getFirstMediaUrl('assets')
                ?: (
                    str_starts_with($asset->path, 'http')
                        ? $asset->path
                        : \Illuminate\Support\Facades\Storage::disk(
                            $asset->disk ?? 'public'
                        )->url($asset->path)
                );

            $payloadData[] = [
                'id' => (int) $asset->id,
                'url' => $url,
            ];
        }

        $this->dispatch(
            'asset-manager:assets-selected',
            pickerId: $this->pickerId,
            assets: $payloadData
        );
        
        $this->pickerSelectedAssets = [];
    }

    public function selectAsset(string|int $assetId): void
    {
        if ($this->multiple) {
            $this->togglePickerSelection($assetId);
            return;
        }

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

        $this->dispatch(
            'asset-manager:image-selected',
            pickerId: $this->pickerId,
            id: (int) $asset->id,
            url: $url
        );
    }

    public bool $showAssetDeleteModal = false;
    public ?int $assetToDeleteId = null;

    public function confirmDeleteAsset(int $assetId): void
    {
        $this->assetToDeleteId = $assetId;
        $this->showAssetDeleteModal = true;
    }

    public function performAssetDeletion(\Innopanda\AssetManager\Actions\DeleteMediaAction $action): void
    {
        if ($this->assetToDeleteId) {
            $this->deleteAsset($this->assetToDeleteId, $action);
            $this->showAssetDeleteModal = false;
            $this->assetToDeleteId = null;
        }
    }

    public function deleteAsset(string|int $assetId, ?\Innopanda\AssetManager\Actions\DeleteMediaAction $action = null): void
    {
        $action = $action ?? app(\Innopanda\AssetManager\Actions\DeleteMediaAction::class);
        $asset = Asset::find($assetId);
        
        if ($asset) {
            try {
                $action->execute($asset);

                if ($this->previewAsset == $assetId) {
                    $this->previewAsset = null;
                }

                if (in_array($assetId, $this->selected)) {
                    $this->selected = array_values(array_diff($this->selected, [$assetId]));
                }

                $this->dispatch('notify', 'Asset deleted successfully!', 'success');
            } catch (\Innopanda\AssetManager\Exceptions\AssetInUseException $e) {
                $this->dispatch('show-asset-in-use-modal', message: $e->getMessage(), usages: $asset->usages()->with('usable')->get()->toArray());
            } catch (\Exception $e) {
                Log::error('Failed to delete asset: ' . $e->getMessage());
                $this->dispatch('notify', 'Failed to delete asset.', 'error');
            }
        }
    }

    #[Livewire\Attributes\On('show-asset-in-use-modal')]
    public function openInUseModal(string $message, array $usages = []): void
    {
        $this->inUseMessage = $message;
        $this->inUseUsages = $usages;
        $this->showInUseModal = true;
    }

    public function closeInUseModal(): void
    {
        $this->showInUseModal = false;
        $this->inUseMessage = '';
        $this->inUseUsages = [];
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
        $query = Asset::query()->with('media');

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