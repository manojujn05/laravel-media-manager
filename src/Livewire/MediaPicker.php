<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Modelable;
use Illuminate\Support\Facades\Log;
use Innopanda\AssetManager\Models\Asset;

/**
 * Media Picker Component
 * 
 * Public API for consuming applications to select an image from the Media Library.
 * 
 * Usage:
 * @livewire(
 *     'asset-manager.media-picker',
 *     [
 *         'collection' => 'recipe-images', // Optional: Spatie media collection name
 *         'selectedAssetId' => 10,         // Optional: pre-load an existing asset ID
 *         'showPreview' => true,           // Optional: toggle image preview thumbnail
 *         'showRemove' => true,            // Optional: toggle remove button
 *         'showUrl' => true,               // Optional: toggle text box with URL
 *         'inputClass' => '...',           // Optional custom CSS for input field
 *         'buttonClass' => '...',          // Optional custom CSS for button
 *         'wrapperClass' => '...',         // Optional custom CSS for wrapper
 *     ]
 * )
 * 
 * 
 * The parent component should persist the selected ID:
 * 
 * public ?int $imageAssetId = null;
 */
class MediaPicker extends Component
{
    public bool $isOpen = false;

    public ?string $collection = null;

    public string $accept = 'image/*';

    public ?string $selectedAssetUrl = null;
    
    #[Modelable]
    public ?int $selectedAssetId = null;

    public bool $showPreview = true;
    
    public bool $showRemove = true;
    
    public bool $showUrl = true;

    public string $buttonLabel = 'Select Image';

    public string $inputClass = '';
    
    public string $buttonClass = '';
    
    public string $wrapperClass = '';

    // Removed old emit options

    public function mount(): void
    {
        if ($this->selectedAssetId) {
            $this->loadAsset();
        }
    }

    public function updatedSelectedAssetId(): void
    {
        $this->loadAsset();
    }

    public function loadAsset(): void
    {
        if (! $this->selectedAssetId) return;

        $asset = Asset::find($this->selectedAssetId);
        if ($asset) {
            $this->selectedAssetUrl = $asset->getFirstMediaUrl('original') 
                ?: $asset->getFirstMediaUrl('assets') 
                ?: (str_starts_with($asset->path, 'http') ? $asset->path : \Illuminate\Support\Facades\Storage::disk($asset->disk ?? 'public')->url($asset->path));
        }
    }

    #[On('open-media-picker')]
    public function open(): void
    {
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function removeMedia(): void
    {
        $this->selectedAssetId = null;
        $this->selectedAssetUrl = null;
    }

    public function handleAssetSelected(int $id, string $url): void
    {
        if (! $this->isOpen) {
            return;
        }

        $asset = Asset::find($id);
        
        if (! $asset) {
            $this->dispatch('notify', 'Unable to select this file. Asset not found.', 'error');
            $this->close();
            return;
        }

        // Always resolve from server as authoritative truth, fallback to passed URL
        $resolvedUrl = $asset->getFirstMediaUrl('original') 
            ?: $asset->getFirstMediaUrl('assets') 
            ?: (str_starts_with($asset->path, 'http') ? $asset->path : \Illuminate\Support\Facades\Storage::disk($asset->disk ?? 'public')->url($asset->path));
        
        $this->selectedAssetUrl = $resolvedUrl ?: $url;
        $this->selectedAssetId = (int) $asset->id;

        Log::info('MediaPicker: selectedAssetId updated', [
            'selectedAssetId_after' => $this->selectedAssetId,
        ]);
        
        $this->close();
    }

    // Tests removed

    public function render()
    {
        return view('asset-manager::livewire.media-picker');
    }
}
