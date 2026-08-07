<?php

namespace Innopanda\AssetManager\Traits;

use Illuminate\Support\Facades\Schema;

trait HasAssetDependencies
{
    public function getUsageDetails(): array
    {
        $usage = [];
        $models = config('asset-manager.usage_models', []);

        foreach ($models as $label => $modelClass) {
            if (class_exists($modelClass)) {
                $modelInstance = new $modelClass;
                $table = $modelInstance->getTable();

                // Check karein ki table mein 'asset_id' column hai ya nahi
                if (Schema::hasColumn($table, 'asset_id')) {
                    $count = $modelClass::where('asset_id', $this->id)->count();
                    if ($count > 0) {
                        $usage[$label] = $count;
                    }
                }
            }
        }

        return $usage;
    }

    public function isUsedInDependencies(): bool
    {
        return count($this->getUsageDetails()) > 0;
    }
}