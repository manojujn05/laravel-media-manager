<?php

namespace Innopanda\AssetManager\Actions;

use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Models\AssetActivityLog;
use Innopanda\AssetManager\Exceptions\AssetInUseException;
use Illuminate\Support\Facades\DB;

class DeleteMediaAction
{
    public function execute(
        Asset $asset,
        string $collection = 'assets'
    ): void {
        // 1. Check usages before deleting to ensure multiple levels of protection
        if ($asset->usages()->exists()) {
            throw new AssetInUseException("Cannot delete asset because it is currently linked to active dependencies.");
        }

        DB::transaction(function () use ($asset) {
            // 2. Log activity before it gets soft-deleted
            AssetActivityLog::create([
                'asset_id' => $asset->id,
                'user_id' => auth()->id(),
                'action' => 'deleted',
                'metadata' => []
            ]);

            // 3. Delete database record (Triggers soft delete, protecting physical files for now)
            $asset->delete();
        });
    }
}