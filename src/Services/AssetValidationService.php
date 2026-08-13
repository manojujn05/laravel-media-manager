<?php

namespace Innopanda\AssetManager\Services;

use Illuminate\Http\UploadedFile;
use Innopanda\AssetManager\Exceptions\InvalidAssetTypeException;
use Innopanda\AssetManager\Exceptions\AssetTooLargeException;

class AssetValidationService
{
    public function validate(UploadedFile $file): void
    {
        $maxSize = config('asset-manager.uploads.max_size', 10240) * 1024; // Convert KB to Bytes
        if ($file->getSize() > $maxSize) {
            $mbSize = $maxSize / 1024 / 1024;
            throw new AssetTooLargeException("Maximum allowed file size is {$mbSize} MB.");
        }

        $allowedMimes = config('asset-manager.uploads.allowed_mimes', []);
        $mime = $file->getMimeType();
        
        $allowedExtensions = config('asset-manager.uploads.allowed_extensions', []);
        $extension = strtolower($file->getClientOriginalExtension());
        if (empty($extension)) {
            $extension = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
        }

        if (!in_array($mime, $allowedMimes)) {
            throw new InvalidAssetTypeException("Unsupported file type: {$mime}.");
        }

        if (!in_array($extension, $allowedExtensions)) {
            throw new InvalidAssetTypeException("Unsupported file extension: .{$extension}.");
        }
    }
}
