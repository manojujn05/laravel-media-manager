<?php

namespace Innopanda\AssetManager\Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Models\Folder;
use Innopanda\AssetManager\Tests\TestCase;

class AssetApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function test_it_can_list_assets_with_pagination()
    {
        Asset::create([
            'title' => 'Asset 1',
            'disk' => 'public',
            'path' => 'assets/1.png',
            'mime_type' => 'image/png'
        ]);

        Asset::create([
            'title' => 'Asset 2',
            'disk' => 'public',
            'path' => 'assets/2.png',
            'mime_type' => 'image/png'
        ]);

        $response = $this->getJson('/api/asset-manager/assets');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'path', 'mime_type']
                ],
                'meta' => ['current_page', 'last_page', 'total']
            ])
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function test_it_can_filter_assets_by_folder_and_search()
    {
        $folder = Folder::create(['name' => 'Images']);
        
        Asset::create([
            'title' => 'Dog Image',
            'disk' => 'public',
            'path' => 'assets/dog.png',
            'mime_type' => 'image/png',
            'folder_id' => $folder->id,
        ]);

        Asset::create([
            'title' => 'Cat Image',
            'disk' => 'public',
            'path' => 'assets/cat.png',
            'mime_type' => 'image/png',
            'folder_id' => $folder->id,
        ]);

        Asset::create([
            'title' => 'Dog Document',
            'disk' => 'public',
            'path' => 'assets/dog.pdf',
            'mime_type' => 'application/pdf',
        ]);

        // Filter by folder
        $response = $this->getJson("/api/asset-manager/assets?folder_id={$folder->id}");
        $response->assertStatus(200)->assertJsonCount(2, 'data');

        // Filter by search
        $response = $this->getJson("/api/asset-manager/assets?search=Dog");
        $response->assertStatus(200)->assertJsonCount(2, 'data');

        // Filter by type
        $response = $this->getJson("/api/asset-manager/assets?type=image");
        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    /** @test */
    public function test_it_can_upload_an_asset()
    {
        $file = UploadedFile::fake()->image('test-image.png');

        $response = $this->postJson('/api/asset-manager/assets/upload', [
            'file' => $file,
            'title' => 'Custom Title',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Asset uploaded successfully.',
            ]);

        $assetData = $response->json('data');
        $this->assertEquals('Custom Title', $assetData['title']);
        $this->assertNotEmpty($assetData['path']);

        // Check file exists on disk
        $asset = Asset::find($assetData['id']);
        Storage::disk('public')->assertExists($asset->path);
    }

    /** @test */
    public function test_it_can_move_assets_to_another_folder()
    {
        $folder = Folder::create(['name' => 'Target Folder']);
        
        $asset = Asset::create([
            'title' => 'Move Me',
            'disk' => 'public',
            'path' => 'assets/move.png',
            'mime_type' => 'image/png'
        ]);

        $response = $this->postJson('/api/asset-manager/assets/move', [
            'asset_ids' => [$asset->id],
            'folder_id' => $folder->id,
        ]);

        $response->assertStatus(200);

        $this->assertEquals($folder->id, $asset->fresh()->folder_id);
    }

    /** @test */
    public function test_it_can_soft_delete_assets()
    {
        $asset = Asset::create([
            'title' => 'Delete Me',
            'disk' => 'public',
            'path' => 'assets/delete.png',
            'mime_type' => 'image/png'
        ]);

        $response = $this->deleteJson('/api/asset-manager/assets', [
            'asset_ids' => [$asset->id]
        ]);

        $response->assertStatus(200);

        $this->assertSoftDeleted('assets', [
            'id' => $asset->id
        ]);
    }

    /** @test */
    public function test_it_can_force_delete_assets_and_remove_file()
    {
        $file = UploadedFile::fake()->image('force-delete.png');
        $path = $file->store('assets', 'public');

        $asset = Asset::create([
            'title' => 'Force Delete',
            'disk' => 'public',
            'path' => $path,
            'mime_type' => 'image/png'
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->deleteJson('/api/asset-manager/assets', [
            'asset_ids' => [$asset->id],
            'force' => true
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('assets', [
            'id' => $asset->id
        ]);

        Storage::disk('public')->assertMissing($path);
    }
}
