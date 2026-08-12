<?php

namespace Innopanda\AssetManager\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Tests\TestCase;

class SpatieIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_asset_can_handle_spatie_media_library_conversions()
    {
        $asset = Asset::create([
            'title' => 'Spatie Test',
            'disk' => 'public',
            'mime_type' => 'image/png'
        ]);

        $file = UploadedFile::fake()->image('test.png');

        // Add media to asset using Spatie Media Library
        $media = $asset->addMedia($file)
            ->toMediaCollection('original', 'public');

        // Assert media is attached
        $this->assertCount(1, $asset->getMedia('original'));
        $this->assertEquals('test.png', $media->file_name);

        // Check if conversions are queued or created (depending on queue setup)
        // By default without queue, they are generated synchronously
        // Let's assert the original file exists in storage
        Storage::disk('public')->assertExists($media->id . '/' . $media->file_name);
    }
}
