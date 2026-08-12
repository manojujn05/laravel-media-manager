<?php

namespace Innopanda\AssetManager\Tests\Feature\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Innopanda\AssetManager\Tests\TestCase;
use Livewire\Livewire;
use Innopanda\AssetManager\Livewire\MediaBrowser;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Actions\UploadMediaAction;

class UploadOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_it_can_upload_a_file_via_media_browser()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');
        $action = $this->mock(UploadMediaAction::class);
        $action->shouldReceive('execute')->once();

        Livewire::test(MediaBrowser::class)
            ->set('file', $file)
            ->assertDispatched('notify', 'File uploaded successfully!')
            ->assertSet('file', null);
    }
}
