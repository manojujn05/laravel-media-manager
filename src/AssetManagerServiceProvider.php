<?php

namespace Innopanda\AssetManager;

use Illuminate\Support\ServiceProvider;
use Innopanda\AssetManager\Services\ThumbnailService;
use Livewire\Livewire;
use Innopanda\AssetManager\Livewire\BrowserToolbar;
use Innopanda\AssetManager\Livewire\BrowserGrid;
use Innopanda\AssetManager\Livewire\AssetCard;
use Innopanda\AssetManager\Livewire\BrowserPreview;
use Innopanda\AssetManager\Livewire\BrowserUploader;
use Innopanda\AssetManager\Livewire\BrowserSidebar;
use Innopanda\AssetManager\Livewire\MediaBrowser;
use Innopanda\AssetManager\Livewire\MediaPicker;
use Innopanda\AssetManager\Livewire\AssetPickerModal;
use Innopanda\AssetManager\Livewire\AssetUploader;
use Innopanda\AssetManager\Livewire\AssetPicker;
use Innopanda\AssetManager\Livewire\FolderNode;
use Innopanda\AssetManager\Livewire\CreateFolder;
use Innopanda\AssetManager\Livewire\FolderTree;
use Innopanda\AssetManager\Livewire\ReplaceFileDrawer;

class AssetManagerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            ThumbnailService::class,
            fn () => new ThumbnailService()
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../config/asset-manager.php',
            'asset-manager'
        );

        $this->app->singleton('asset-manager', function () {
            return new AssetManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Load Views, Routes & Migrations
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'asset-manager');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        if (file_exists(__DIR__ . '/../routes/api.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        }

        // 2. Register Livewire Components Safely
        $this->registerLivewireComponents();

        // 3. Setup Console/Publishing Utilities
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/asset-manager.php' => config_path('asset-manager.php'),
            ], 'asset-manager-config');

            // Safely verify Spatie migration path before publishing
            $spatieMigrationPath = base_path('vendor/spatie/laravel-medialibrary/database/migrations');
            if (is_dir($spatieMigrationPath)) {
                $this->publishes([
                    $spatieMigrationPath => database_path('migrations'),
                ], 'asset-manager-media-migrations');
            }
        }
    }

    /**
     * Helper to group and register all Livewire components.
     */
    protected function registerLivewireComponents(): void
    {
        Livewire::component('asset-manager.media-picker', MediaPicker::class);
        Livewire::component('asset-manager.media-browser', MediaBrowser::class);
        Livewire::component('asset-manager.browser-toolbar', BrowserToolbar::class);
        Livewire::component('asset-manager.browser-grid', BrowserGrid::class);
        Livewire::component('asset-manager.asset-card', AssetCard::class);
        Livewire::component('asset-manager.browser-preview', BrowserPreview::class);
        Livewire::component('asset-manager.browser-uploader', BrowserUploader::class);
        Livewire::component('asset-manager.browser-sidebar', BrowserSidebar::class);
        Livewire::component('asset-manager.asset-picker', AssetPicker::class);
        Livewire::component('asset-manager.folder-node', FolderNode::class);
        Livewire::component('asset-manager.create-folder', CreateFolder::class);
        Livewire::component('asset-manager.asset-picker-modal', AssetPickerModal::class);
        Livewire::component('asset-manager.asset-uploader', AssetUploader::class);
        Livewire::component('asset-manager.folder-tree', FolderTree::class);
        Livewire::component('asset-manager.replace-file-drawer', ReplaceFileDrawer::class);
    }
}