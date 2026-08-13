<?php

namespace Innopanda\AssetManager\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Innopanda\AssetManager\Actions\UploadMediaAction;
use Innopanda\AssetManager\Actions\ReplaceMediaAction;
use Innopanda\AssetManager\Actions\RestoreMediaVersionAction;
use Innopanda\AssetManager\DTOs\UploadMediaData;
use Innopanda\AssetManager\Tests\TestCase;

class RestoreTest extends TestCase
{
    protected UploadMediaAction $uploadAction;
    protected ReplaceMediaAction $replaceAction;
    protected RestoreMediaVersionAction $restoreAction;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        $this->uploadAction = app(UploadMediaAction::class);
        $this->replaceAction = app(ReplaceMediaAction::class);
        $this->restoreAction = app(RestoreMediaVersionAction::class);
    }

    public function test_restore_creates_a_new_current_version_from_the_restored_version()
    {
        // 1. Upload initial asset (v1 equivalent)
        $file1 = UploadedFile::fake()->image('v1.jpg');
        $data1 = new UploadMediaData(title: 'V1 Image', disk: 'public');
        $asset = $this->uploadAction->execute($file1, $data1);
        
        // 2. Replace (v2 equivalent)
        $file2 = UploadedFile::fake()->image('v2.png');
        $data2 = new UploadMediaData(title: 'V2 Image', disk: 'public');
        $asset = $this->replaceAction->execute($asset, $file2, $data2);

        // 3. Replace again (v3 equivalent - current)
        $file3 = UploadedFile::fake()->image('v3.webp');
        $data3 = new UploadMediaData(title: 'V3 Image', disk: 'public');
        $asset = $this->replaceAction->execute($asset, $file3, $data3);

        // Now we have:
        // versions table: version 1 (jpg), version 2 (png)
        // current asset: webp

        $this->assertEquals(2, $asset->versions()->count());
        $this->assertEquals('image/webp', $asset->mime_type);

        // 4. Restore v1
        $versionToRestore = $asset->versions()->where('version', 1)->first();
        $this->assertNotNull($versionToRestore);
        $this->assertEquals('image/jpeg', $versionToRestore->mime_type);

        $restoredAsset = $this->restoreAction->execute($asset, $versionToRestore);

        // Assertions after restore:
        // Current asset should now reflect v1 data (jpg)
        $this->assertEquals('image/jpeg', $restoredAsset->mime_type);
        
        // Versions count should be 3 (v1, v2, v3)
        // v3 (the webp) should now be in the versions table as version 3
        $this->assertEquals(3, $restoredAsset->versions()->count());
        
        $newlySavedVersion = $restoredAsset->versions()->where('version', 3)->first();
        $this->assertNotNull($newlySavedVersion);
        $this->assertEquals('image/webp', $newlySavedVersion->mime_type);

        // Verify v1 is completely untouched in the history
        $v1Check = $restoredAsset->versions()->where('version', 1)->first();
        $this->assertNotNull($v1Check);
        $this->assertEquals('image/jpeg', $v1Check->mime_type);
    }
}
