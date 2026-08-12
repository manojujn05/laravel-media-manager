<?php

namespace Innopanda\AssetManager;

use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;
use Illuminate\Support\ServiceProvider;

class AssetManagerFilamentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (class_exists(FilamentAsset::class)) {
            FilamentAsset::register([
                Css::make('asset-manager-styles', __DIR__ . '/../dist/css/asset-manager.css'),
            ], 'innopanda/asset-manager');
        }
    }
}
