<?php

namespace Innopanda\AssetManager\Actions;

use Innopanda\AssetManager\Models\Asset;

class MoveMediaAction
{
    public function execute(
        Asset $asset,
        int $folderId
    ): Asset {

        $asset->update([
            'folder_id' => $folderId,
        ]);

        return $asset->fresh();
    }
}