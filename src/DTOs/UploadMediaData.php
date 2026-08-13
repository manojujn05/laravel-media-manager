<?php

namespace Innopanda\AssetManager\DTOs;

class UploadMediaData
{
    public function __construct(
        public readonly ?int $folder_id = null,
        public readonly string $collection = 'assets',
        public readonly string $disk = 'public',
        public readonly ?string $title = null,
        public readonly ?string $alt = null,
        public readonly ?string $description = null,
        public readonly bool $force_upload = false,
    ) {}
}