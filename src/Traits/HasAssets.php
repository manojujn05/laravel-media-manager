<?php

namespace Innopanda\AssetManager\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Innopanda\AssetManager\Models\AssetUsage;
use Innopanda\AssetManager\Models\Asset;

trait HasAssets
{
    public function assetUsages(): MorphMany
    {
        return $this->morphMany(AssetUsage::class, 'usable');
    }

    public function attachAsset(Asset $asset, string $field = 'asset'): void
    {
        $this->assetUsages()->firstOrCreate([
            'asset_id' => $asset->id,
            'field'    => $field,
        ]);
    }

    public function detachAsset(Asset $asset, string $field = 'asset'): void
    {
        $this->assetUsages()->where([
            'asset_id' => $asset->id,
            'field'    => $field,
        ])->delete();
    }
}
