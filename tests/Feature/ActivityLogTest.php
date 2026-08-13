<?php

namespace Innopanda\AssetManager\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Innopanda\AssetManager\Actions\UploadMediaAction;
use Innopanda\AssetManager\Actions\ReplaceMediaAction;
use Innopanda\AssetManager\Actions\RestoreMediaVersionAction;
use Innopanda\AssetManager\Actions\DeleteMediaAction;
use Innopanda\AssetManager\DTOs\UploadMediaData;
use Innopanda\AssetManager\Tests\TestCase;

class ActivityLogTest extends TestCase
{
    protected UploadMediaAction $uploadAction;
    protected ReplaceMediaAction $replaceAction;
    protected RestoreMediaVersionAction $restoreAction;
    protected DeleteMediaAction $deleteAction;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        $this->uploadAction = app(UploadMediaAction::class);
        $this->replaceAction = app(ReplaceMediaAction::class);
        $this->restoreAction = app(RestoreMediaVersionAction::class);
        $this->deleteAction = app(DeleteMediaAction::class);
    }

    public function test_actions_create_activity_logs()
    {
        // 1. Upload
        $file = UploadedFile::fake()->image('test.jpg');
        $data = new UploadMediaData(title: 'Test', disk: 'public');
        $asset = $this->uploadAction->execute($file, $data);

        $this->assertDatabaseHas('asset_activity_logs', [
            'asset_id' => $asset->id,
            'action' => 'uploaded',
        ]);

        // 2. Replace
        $replaceFile = UploadedFile::fake()->image('replace.png');
        $replaceData = new UploadMediaData(title: 'Replace', disk: 'public');
        $asset = $this->replaceAction->execute($asset, $replaceFile, $replaceData);

        $this->assertDatabaseHas('asset_activity_logs', [
            'asset_id' => $asset->id,
            'action' => 'replaced',
        ]);

        // 3. Restore
        $versionToRestore = $asset->versions()->where('version', 1)->first();
        $asset = $this->restoreAction->execute($asset, $versionToRestore);

        $this->assertDatabaseHas('asset_activity_logs', [
            'asset_id' => $asset->id,
            'action' => 'restored',
        ]);

        // 4. Delete
        $this->deleteAction->execute($asset);

        $this->assertDatabaseHas('asset_activity_logs', [
            'asset_id' => $asset->id,
            'action' => 'deleted',
        ]);
    }
}
