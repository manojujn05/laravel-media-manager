<?php

namespace Innopanda\AssetManager\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Innopanda\AssetManager\Actions\UploadMediaAction;
use Innopanda\AssetManager\DTOs\UploadMediaData;
use Innopanda\AssetManager\Exceptions\InvalidAssetTypeException;
use Innopanda\AssetManager\Exceptions\AssetTooLargeException;
use Innopanda\AssetManager\Tests\TestCase;

class UploadValidationTest extends TestCase
{
    protected UploadMediaAction $uploadAction;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        $this->uploadAction = app(UploadMediaAction::class);
    }

    public function test_it_allows_valid_image_uploads()
    {
        $file = UploadedFile::fake()->image('test-image.jpg');
        $data = new UploadMediaData(title: 'Valid Image', disk: 'public');

        $asset = $this->uploadAction->execute($file, $data);

        $this->assertNotNull($asset);
        $this->assertEquals('image/jpeg', $asset->mime_type);
        $this->assertEquals('jpg', $asset->extension);
        
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'title' => 'Valid Image',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
        ]);
    }

    public function test_it_rejects_unsupported_mime_type()
    {
        $this->expectException(InvalidAssetTypeException::class);
        $this->expectExceptionMessage("Unsupported file type: application/json.");

        // Create a fake json file
        $file = UploadedFile::fake()->create('document.json', 10, 'application/json');
        $data = new UploadMediaData(title: 'JSON Document', disk: 'public');

        $this->uploadAction->execute($file, $data);
    }

    public function test_it_rejects_unsupported_extension()
    {
        $this->expectException(InvalidAssetTypeException::class);
        $this->expectExceptionMessage("Unsupported file extension: .exe.");

        // Fake an exe file but give it an allowed mime type (simulate malicious rename or bad config check)
        // Wait, UploadedFile::fake()->create uses the extension to guess mime type.
        // Let's create a real fake object
        $file = UploadedFile::fake()->create('malicious.exe', 10, 'image/jpeg');
        $data = new UploadMediaData(title: 'Malicious', disk: 'public');

        $this->uploadAction->execute($file, $data);
    }

    public function test_it_rejects_oversized_files()
    {
        // Max size is 10240 KB (10 MB)
        $this->expectException(AssetTooLargeException::class);

        $file = UploadedFile::fake()->create('large-video.mp4', 20000, 'video/mp4');
        // Wait, mp4 is not allowed mime type, so it might fail on mime first.
        // Let's use an allowed mime type that is too large.
        $largeFile = UploadedFile::fake()->create('huge-image.jpg', 20000, 'image/jpeg');
        $data = new UploadMediaData(title: 'Huge Image', disk: 'public');

        $this->uploadAction->execute($largeFile, $data);
    }
}
