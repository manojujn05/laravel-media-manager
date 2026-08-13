<?php

namespace Innopanda\AssetManager\Exceptions;

use Exception;
use Innopanda\AssetManager\Models\Asset;

class AssetDuplicateException extends Exception
{
    public ?Asset $duplicateAsset;

    public function __construct(string $message = 'Duplicate asset detected.', ?Asset $duplicateAsset = null, int $code = 0, \Throwable $previous = null)
    {
        $this->duplicateAsset = $duplicateAsset;
        parent::__construct($message, $code, $previous);
    }
}
