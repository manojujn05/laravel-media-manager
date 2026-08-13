<?php

namespace Innopanda\AssetManager\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Innopanda\AssetManager\Actions\UploadMediaAction;
use Innopanda\AssetManager\DTOs\UploadMediaData;
use Innopanda\AssetManager\Tests\Fixtures\User;
use Innopanda\AssetManager\Exceptions\AssetDuplicateException;
use Innopanda\AssetManager\Tests\TestCase;
use Innopanda\AssetManager\Models\Asset;

class TenantDuplicateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Config::set('asset-manager.duplicates.enabled', true);
        Config::set('asset-manager.duplicates.behavior', 'warn');
    }

    public function test_duplicate_detection_respects_tenants_if_scoped()
    {
        // If the package supports tenant scoping via a scope or user_id,
        // this test should ensure that tenant A uploading file X doesn't conflict
        // with tenant B uploading file X.

        // Since the current asset model doesn't have tenant_id directly built-in
        // but has created_by, we should simulate scoping if applied, or 
        // at least verify duplicate logic doesn't crash.
        // Currently, duplicate detection checks hash AND folder_id.
        // So if they are in different folders (tenant folders), it should allow it.

        $uploadAction = app(UploadMediaAction::class);
        
        $file1 = UploadedFile::fake()->image('duplicate.jpg');
        $file2 = UploadedFile::fake()->image('duplicate.jpg'); // Same content (fake images are 10x10 black squares, so same hash)

        $data1 = new UploadMediaData(folder_id: 1, title: 'File 1', disk: 'public');
        $data2 = new UploadMediaData(folder_id: 2, title: 'File 2', disk: 'public');

        // Upload for Tenant A (Folder 1)
        $uploadAction->execute($file1, $data1);

        // Upload for Tenant B (Folder 2)
        // It should NOT throw duplicate exception because folder_id differs.
        $asset2 = $uploadAction->execute($file2, $data2);

        $this->assertNotNull($asset2);
        $this->assertEquals(2, Asset::count());
    }

    public function test_duplicate_detection_warns_in_same_folder()
    {
        $uploadAction = app(UploadMediaAction::class);
        
        $file1 = UploadedFile::fake()->image('duplicate.jpg');
        $file2 = UploadedFile::fake()->image('duplicate.jpg');

        $data1 = new UploadMediaData(folder_id: 1, title: 'File 1', disk: 'public');
        $data2 = new UploadMediaData(folder_id: 1, title: 'File 2', disk: 'public');

        $uploadAction->execute($file1, $data1);

        $this->expectException(AssetDuplicateException::class);
        $uploadAction->execute($file2, $data2);
    }
}
