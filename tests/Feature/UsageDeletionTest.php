<?php

namespace Innopanda\AssetManager\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Innopanda\AssetManager\Actions\UploadMediaAction;
use Innopanda\AssetManager\Actions\DeleteMediaAction;
use Innopanda\AssetManager\DTOs\UploadMediaData;
use Innopanda\AssetManager\Exceptions\AssetInUseException;
use Innopanda\AssetManager\Models\AssetUsage;
use Innopanda\AssetManager\Tests\Fixtures\User; // Assuming User fixture
use Innopanda\AssetManager\Tests\TestCase;
use Innopanda\AssetManager\Models\Asset;

class UsageDeletionTest extends TestCase
{
    protected UploadMediaAction $uploadAction;
    protected DeleteMediaAction $deleteAction;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        $this->uploadAction = app(UploadMediaAction::class);
        $this->deleteAction = app(DeleteMediaAction::class);
    }

    public function test_unused_asset_can_be_deleted()
    {
        $file = UploadedFile::fake()->image('test.jpg');
        $data = new UploadMediaData(title: 'Test', disk: 'public');
        $asset = $this->uploadAction->execute($file, $data);

        $this->assertEquals(0, $asset->usages()->count());
        $assetId = $asset->id;

        $this->deleteAction->execute($asset);

        // Assert asset is soft deleted
        $this->assertSoftDeleted('assets', ['id' => $assetId]);
    }

    public function test_used_asset_throws_exception_on_deletion()
    {
        $file = UploadedFile::fake()->image('test.jpg');
        $data = new UploadMediaData(title: 'Test', disk: 'public');
        $asset = $this->uploadAction->execute($file, $data);

        $user = User::create(['name' => 'User', 'email' => 'test@test.com', 'password' => '123']);
        
        AssetUsage::create([
            'asset_id' => $asset->id,
            'usable_type' => User::class,
            'usable_id' => $user->id,
            'field' => 'avatar'
        ]);

        $this->expectException(AssetInUseException::class);
        $this->expectExceptionMessage("Cannot delete asset because it is currently linked to active dependencies.");

        $this->deleteAction->execute($asset);
    }

    public function test_asset_with_multiple_usages_blocked_from_deletion()
    {
        $file = UploadedFile::fake()->image('test.jpg');
        $data = new UploadMediaData(title: 'Test', disk: 'public');
        $asset = $this->uploadAction->execute($file, $data);

        $user1 = User::create(['name' => 'User 1', 'email' => 't1@test.com', 'password' => '123']);
        $user2 = User::create(['name' => 'User 2', 'email' => 't2@test.com', 'password' => '123']);
        
        AssetUsage::create([
            'asset_id' => $asset->id,
            'usable_type' => User::class,
            'usable_id' => $user1->id,
            'field' => 'avatar'
        ]);

        AssetUsage::create([
            'asset_id' => $asset->id,
            'usable_type' => User::class,
            'usable_id' => $user2->id,
            'field' => 'banner'
        ]);

        $this->expectException(AssetInUseException::class);

        $this->deleteAction->execute($asset);
    }
}
