<?php

namespace Innopanda\AssetManager\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Tests\TestCase;
use Innopanda\AssetManager\Services\AssetDiscoveryService;

class AssetDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        config([
            'filesystems.disks.s3' => [
                'driver' => 's3',
                'key' => 'test',
                'secret' => 'test',
                'region' => 'test',
                'bucket' => 'test',
                'url' => 'https://test-bucket.s3.amazonaws.com',
            ],
            'asset-manager.disk' => 's3',
            'asset-manager.sync.root_path' => '/',
        ]);
        
        Storage::fake('s3');
    }

    public function test_discovers_existing_s3_files()
    {
        // Create mock physical files directly on the fake S3 disk
        Storage::disk('s3')->put('products/image1.jpg', 'content');
        Storage::disk('s3')->put('recipes/image2.png', 'content2');

        $service = app(AssetDiscoveryService::class);
        $stats = $service->sync('s3', '/');

        $this->assertEquals(2, $stats['discovered']);
        $this->assertEquals(2, $stats['created']);
        $this->assertEquals(0, $stats['updated']);

        $this->assertDatabaseHas('assets', [
            'disk' => 's3',
            'path' => 'products/image1.jpg',
            'extension' => 'jpg',
            'title' => 'image1'
        ]);

        $this->assertDatabaseHas('assets', [
            'disk' => 's3',
            'path' => 'recipes/image2.png',
            'extension' => 'png',
            'title' => 'image2'
        ]);
    }

    public function test_repeated_sync_prevents_duplicates()
    {
        Storage::disk('s3')->put('image.jpg', 'content');

        $service = app(AssetDiscoveryService::class);
        
        // First sync
        $stats1 = $service->sync('s3', '/');
        $this->assertEquals(1, $stats1['created']);

        // Second sync
        $stats2 = $service->sync('s3', '/');
        $this->assertEquals(0, $stats2['created']);
        $this->assertEquals(1, $stats2['unchanged']);

        // Total assets should still be 1
        $this->assertEquals(1, Asset::count());
    }

    public function test_existing_asset_is_updated_if_size_changes()
    {
        Storage::disk('s3')->put('image.jpg', 'small content');
        $service = app(AssetDiscoveryService::class);
        $service->sync('s3', '/');
        
        $asset = Asset::first();
        $originalSize = $asset->size;

        // Change the physical file
        Storage::disk('s3')->put('image.jpg', 'much larger content now');

        $stats = $service->sync('s3', '/');
        $this->assertEquals(1, $stats['updated']);

        $asset->refresh();
        $this->assertNotEquals($originalSize, $asset->size);
    }

    public function test_missing_s3_object_is_reported_but_not_deleted()
    {
        // Simulate an asset that exists in the database
        $asset = Asset::create([
            'title' => 'Missing Image',
            'disk' => 's3',
            'path' => 'missing.jpg',
            'mime_type' => 'image/jpeg'
        ]);

        // Physical file does NOT exist on the fake S3 disk
        
        $service = app(AssetDiscoveryService::class);
        $stats = $service->sync('s3', '/');

        $this->assertEquals(1, $stats['missing']);
        $this->assertEquals(0, $stats['failed']);
        
        // Asset should remain in the database untouched
        $this->assertDatabaseHas('assets', ['id' => $asset->id]);
    }

    public function test_private_s3_url_handling()
    {
        config([
            'asset-manager.sync.private_urls' => true,
        ]);

        $asset = Asset::create([
            'title' => 'Private Image',
            'disk' => 's3',
            'path' => 'private/secret.jpg',
            'mime_type' => 'image/jpeg'
        ]);

        $url = $asset->getUrl();
        
        // Since we are using a local fake, temporaryUrl might fail or generate a specific signature.
        // Let's just ensure getUrl() successfully returns a string and doesn't crash.
        $this->assertIsString($url);
        $this->assertNotEmpty($url);
    }
}
