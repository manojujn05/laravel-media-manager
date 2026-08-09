<?php

namespace Innopanda\AssetManager\Tests\Feature;

use Tests\TestCase;
use Innopanda\AssetManager\Models\Folder;
use Innopanda\AssetManager\Livewire\MediaBrowser;
use Livewire\Livewire;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Innopanda\AssetManager\Models\Asset;

class FolderUploadTest extends TestCase
{
    public function test_uploading_file_inside_a_folder_attaches_it_to_the_folder()
    {
        Storage::fake('public');

        // 1. Create a folder
        $folder = Folder::create([
            'name' => 'Banners',
            'slug' => 'banners',
        ]);

        // 2. Select folder in Livewire component and upload file
        $file = UploadedFile::fake()->image('banner.jpg');

        Livewire::test(MediaBrowser::class)
            ->call('selectFolder', $folder->id)
            ->set('file', $file)
            ->assertDispatched('notify');

        // 3. Assert asset belongs to folder
        $this->assertDatabaseHas('assets', [
            'folder_id' => $folder->id,
            'title' => 'banner',
        ]);
    }
}
