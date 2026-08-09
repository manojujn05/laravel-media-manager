<?php

namespace Innopanda\AssetManager\Tests;

use Innopanda\AssetManager\Tests\TestCase;
use Innopanda\AssetManager\AssetManagerServiceProvider;
use Innopanda\AssetManager\AssetManager;

class AssetManagerServiceProviderTest extends TestCase
{
    // Setup is handled by Innopanda\AssetManager\Tests\TestCase

    public function test_asset_manager_singleton_is_bound()
    {
        // Check if it's bound to the container
        $this->assertTrue($this->app->bound('asset-manager'), 'Asset Manager is not bound in the container.');

        // Check if resolving it returns the correct class
        $instance = $this->app->make('asset-manager');
        $this->assertInstanceOf(AssetManager::class, $instance);
    }

    public function test_livewire_components_are_registered()
    {
        $expectedComponents = [
            'asset-manager.media-browser' => \Innopanda\AssetManager\Livewire\MediaBrowser::class,
            'asset-manager.browser-toolbar' => \Innopanda\AssetManager\Livewire\BrowserToolbar::class,
            'asset-manager.browser-grid' => \Innopanda\AssetManager\Livewire\BrowserGrid::class,
            'asset-manager.asset-card' => \Innopanda\AssetManager\Livewire\AssetCard::class,
            'asset-manager.browser-preview' => \Innopanda\AssetManager\Livewire\BrowserPreview::class,
            'asset-manager.browser-uploader' => \Innopanda\AssetManager\Livewire\BrowserUploader::class,
            'asset-manager.browser-sidebar' => \Innopanda\AssetManager\Livewire\BrowserSidebar::class,
            'asset-manager.asset-picker' => \Innopanda\AssetManager\Livewire\AssetPicker::class,
            'asset-manager.folder-node' => \Innopanda\AssetManager\Livewire\FolderNode::class,
            'asset-manager.create-folder' => \Innopanda\AssetManager\Livewire\CreateFolder::class,
            'asset-manager.asset-picker-modal' => \Innopanda\AssetManager\Livewire\AssetPickerModal::class,
            'asset-manager.asset-uploader' => \Innopanda\AssetManager\Livewire\AssetUploader::class,
            'asset-manager.folder-tree' => \Innopanda\AssetManager\Livewire\FolderTree::class,

            'asset-manager.replace-file-drawer' => \Innopanda\AssetManager\Livewire\ReplaceFileDrawer::class,
        ];

        // In Livewire v3, components are registered in the ComponentRegistry.
        $registry = $this->app->make(\Livewire\Mechanisms\ComponentRegistry::class);

        foreach ($expectedComponents as $alias => $class) {
            $this->assertEquals(
                $class,
                $registry->getClass($alias),
                "Livewire component [{$alias}] is not properly registered to [{$class}]."
            );
        }
    }
}
