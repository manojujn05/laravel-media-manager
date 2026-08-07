<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Models\Folder;
use Innopanda\AssetManager\Models\Tag;
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

    public string|int|null $tag = null; // Phase 9: Active Tag Filter

    public int $perPage = 24;

    public array $selected = [];

    public string|int|null $previewAsset = null;

    public $file = null;

    /*
    |--------------------------------------------------------------------------
    | Phase 9: Tag Management Modal State
    |--------------------------------------------------------------------------
    */

    public bool $showTagModal = false;

    public string $newTagName = '';

    public string $newTagColor = '#6366f1'; // Default Indigo Color

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
        'tag'       => ['except' => null], // Phase 9 URL Support
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

    /**
     * Phase 9: Select or toggle active Tag filter
     */
    public function selectTag(string|int|null $tagId = null): void
    {
        $this->tag = ($this->tag == $tagId) ? null : $tagId;
        $this->resetPage();
    }

    /**
     * Phase 9: Toggle tag attachment to specific Asset
     */
    public function toggleAssetTag(string|int $assetId, string|int $tagId): void
    {
        $asset = Asset::find($assetId);
        if ($asset) {
            $asset->tags()->toggle($tagId);
            $this->dispatch('notify', 'Asset tag updated!');
        }
    }

    /**
     * Phase 9: Open Tag Creation Modal
     */
    public function openTagModal(): void
    {
        $this->reset(['newTagName', 'newTagColor']);
        $this->newTagColor = '#6366f1';
        $this->showTagModal = true;
    }

    /**
     * Phase 9: Store a new Tag
     */
    public function createTag(): void
    {
        $this->validate([
            'newTagName' => 'required|string|max:50|unique:asset_tags,name',
            'newTagColor' => 'required|string|max:7',
        ]);

        Tag::create([
            'name' => trim($this->newTagName),
            'color' => $this->newTagColor,
            'slug' => Str::slug($this->newTagName),
        ]);

        $this->reset(['newTagName', 'newTagColor', 'showTagModal']);
        $this->dispatch('notify', 'Tag created successfully!');
    }

    /**
     * Phase 9: Delete a Tag
     */
    public function deleteTag(string|int $tagId): void
    {
        $tagModel = Tag::find($tagId);

        if ($tagModel) {
            $tagModel->assets()->detach();
            $tagModel->delete();

            if ($this->tag == $tagId) {
                $this->tag = null;
            }

            $this->dispatch('notify', 'Tag deleted successfully!');
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'type', 'favorites', 'folder', 'tag']);
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
            folder_id: $this->folder ? (string)$this->folder : null,
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

    public function updatingTag()
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

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */

    public function getAssetsProperty()
    {
        $query = Asset::query()->with('tags');

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

        // Phase 9: Filter assets by dynamic Tag ID
        if ($this->tag) {
            $query->whereHas('tags', function ($q) {
                $q->where('asset_tags.id', $this->tag);
            });
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

    public function getAllTagsProperty()
    {
        return Tag::withCount('assets')->get();
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
                'allTags'       => $this->allTags,
            ]
        );
    }
}