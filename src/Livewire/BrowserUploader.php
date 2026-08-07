<?php

namespace Innopanda\AssetManager\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Innopanda\AssetManager\Actions\UploadMediaAction;

class BrowserUploader extends Component
{
    use WithFileUploads;

    #[Validate([
        'files.*' => 'required|file|max:10240',
    ])]
    public array $files = [];

    public bool $isUploading = false;

    public function upload(UploadMediaAction $action): void
    {
        $this->validate();

        $this->isUploading = true;

        foreach ($this->files as $file) {
            $action->handle($file);
        }

        $this->files = [];

        $this->isUploading = false;

        $this->dispatch('assets-uploaded');
    }

    public function render()
    {
        return view(
            'asset-manager::livewire.components.browser-uploader'
        );
    }
}