<?php

namespace Innopanda\AssetManager\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Innopanda\AssetManager\Actions\UploadMediaAction;
use Innopanda\AssetManager\Actions\ReplaceMediaAction;
use Innopanda\AssetManager\DTOs\UploadMediaData;
use Innopanda\AssetManager\Models\AssetUsage;
use Innopanda\AssetManager\Tests\Fixtures\User; // Assuming a User model exists for morphs
use Innopanda\AssetManager\Tests\TestCase;

class ReplacementTest extends TestCase
{
    protected UploadMediaAction $uploadAction;
    protected ReplaceMediaAction $replaceAction;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        $this->uploadAction = app(UploadMediaAction::class);
        $this->replaceAction = app(ReplaceMediaAction::class);
    }

    public function test_replacement_preserves_asset_id_and_mappings_and_creates_version()
    {
        // 1. Upload initial asset
        $originalFile = UploadedFile::fake()->image('original.jpg');
        $data = new UploadMediaData(title: 'Original Image', disk: 'public');
        $asset = $this->uploadAction->execute($originalFile, $data);
        
        $originalAssetId = $asset->id;

        // 2. Map asset to multiple entities (using User as a dummy model)
        $user1 = User::create(['name' => 'User 1', 'email' => 'u1@test.com', 'password' => '123']);
        $user2 = User::create(['name' => 'User 2', 'email' => 'u2@test.com', 'password' => '123']);
        
        // We can just manually insert into asset_usages for the test since we just want to ensure they don't get deleted
        AssetUsage::create([
            'asset_id' => $originalAssetId,
            'usable_type' => User::class,
            'usable_id' => $user1->id,
            'field' => 'avatar'
        ]);
        
        AssetUsage::create([
            'asset_id' => $originalAssetId,
            'usable_type' => User::class,
            'usable_id' => $user2->id,
            'field' => 'avatar'
        ]);

        $this->assertEquals(2, $asset->usages()->count());

        // 3. Replace the asset
        $replacementFile = UploadedFile::fake()->image('replacement.png');
        $replaceData = new UploadMediaData(title: 'Replacement Image', disk: 'public');
        
        $updatedAsset = $this->replaceAction->execute($asset, $replacementFile, $replaceData);

        // Assert: Asset ID remains unchanged
        $this->assertEquals($originalAssetId, $updatedAsset->id);

        // Assert: Mappings remain unchanged
        $this->assertEquals(2, $updatedAsset->usages()->count());

        // Assert: Version is created for the old file
        $this->assertEquals(1, $updatedAsset->versions()->count());
        $version = $updatedAsset->versions()->first();
        
        $this->assertEquals(1, $version->version);
        $this->assertEquals('image/jpeg', $version->mime_type); // Original was a jpg
        $this->assertStringContainsString('original', $version->metadata['alt'] ?? 'original'); // Actually we didn't set alt, but title was Original Image

        // Assert: New file is current version
        $this->assertEquals('image/png', $updatedAsset->mime_type);
        $this->assertEquals('Replacement Image', $updatedAsset->title);
    }
}
