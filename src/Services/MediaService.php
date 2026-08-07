<?php

namespace Innopanda\AssetManager\Services;

use Illuminate\Http\UploadedFile;
use Innopanda\AssetManager\Actions\DeleteMediaAction;
use Innopanda\AssetManager\Actions\MoveMediaAction;
use Innopanda\AssetManager\Actions\ReplaceMediaAction;
use Innopanda\AssetManager\Actions\UploadMediaAction;
use Innopanda\AssetManager\DTOs\UploadMediaData;
use Innopanda\AssetManager\Models\Asset;

class MediaService
{
    public function __construct(
        protected UploadMediaAction $upload,
        protected ReplaceMediaAction $replace,
        protected DeleteMediaAction $delete,
        protected MoveMediaAction $move,
    ) {}

    public function upload(
        Asset $asset,
        UploadedFile $file,
        UploadMediaData $data
    ) {
        return $this->upload->execute($asset, $file, $data);
    }

    public function replace(
        Asset $asset,
        UploadedFile $file,
        UploadMediaData $data
    ) {
        return $this->replace->execute($asset, $file, $data);
    }

    public function delete(
        Asset $asset,
        string $collection = 'assets'
    ): void {
        $this->delete->execute($asset, $collection);
    }

    public function move(
        Asset $asset,
        int $folderId
    ): Asset {
        return $this->move->execute($asset, $folderId);
    }
}