<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Traits\WithConfirmation;

class BrowserPreview extends Component
{
    use WithConfirmation;

    public ?Asset $asset = null;

    #[On('preview-asset')]
    public function loadAsset(int $assetId): void
    {
        $this->asset = Asset::find($assetId);
    }

    public function replace(): void
    {
        $this->dispatch(
            'open-replace-modal',
            assetId: $this->asset?->id
        );
    }

    public function selectAsset(): void
    {
        if (! $this->asset) {
            Log::warning('BrowserPreview: selectAsset called but no asset');

            return;
        }

        $url = $this->asset->getFirstMediaUrl('original')
            ?: $this->asset->getFirstMediaUrl('assets')
            ?: (
                str_starts_with($this->asset->path, 'http')
                    ? $this->asset->path
                    : \Illuminate\Support\Facades\Storage::disk(
                        $this->asset->disk ?? 'public'
                    )->url($this->asset->path)
            );

        $payload = json_encode([
            'id' => (int) $this->asset->id,
            'url' => $url,
        ]);

        Log::info('BrowserPreview: dispatching image-selected', [
            'asset_id' => $this->asset->id,
            'url' => $url,
            'payload' => $payload,
        ]);

        $this->js("
            console.log('BrowserPreview JS: dispatching image-selected');

            window.dispatchEvent(new CustomEvent('image-selected', {
                detail: {$payload}
            }));

            console.log('BrowserPreview JS: image-selected dispatched');
        ");
    }

    public function preview(): void
    {
        if (! $this->asset) {
            return;
        }

        $url = $this->asset->getFirstMediaUrl('assets') ?: (str_starts_with($this->asset->path, 'http') ? $this->asset->path : \Illuminate\Support\Facades\Storage::disk($this->asset->disk ?? 'public')->url($this->asset->path));
        
        $this->dispatch('open-url', url: $url);
    }

    public function copyUrl(): void
    {
        if (! $this->asset) {
            return;
        }

        $url = $this->asset->getFirstMediaUrl('assets') ?: (str_starts_with($this->asset->path, 'http') ? $this->asset->path : \Illuminate\Support\Facades\Storage::disk($this->asset->disk ?? 'public')->url($this->asset->path));
        
        $this->dispatch('clipboard-copy', text: $url);
        $this->dispatch('notify', message: 'URL copied to clipboard!', type: 'success');
    }

   public function download()
{
    if (! $this->asset) {
        return;
    }

    return response()->download(
        $this->asset->getFirstMedia('assets')->getPath()
    );
}

    public function delete(): void
    {
        if (! $this->asset) {
            return;
        }

        $this->asset->delete();

        $this->dispatch('asset-deleted');

        $this->asset = null;
    }

    public function render()
    {
        return view(
            'asset-manager::livewire.components.browser-preview'
        );
    }
}