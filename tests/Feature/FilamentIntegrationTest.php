<?php

namespace Innopanda\AssetManager\Tests\Feature;

use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Innopanda\AssetManager\AssetManagerFilamentServiceProvider;
use Innopanda\AssetManager\Tests\FilamentTestCase;

class FilamentIntegrationTest extends FilamentTestCase
{
    /** @test */
    public function test_it_boots_filament_integration_provider_when_filament_is_present()
    {
        $providers = $this->app->getLoadedProviders();
        $this->assertArrayHasKey(AssetManagerFilamentServiceProvider::class, $providers, 'Filament integration provider did not boot.');
    }

    /** @test */
    public function test_it_registers_asset_manager_styles_with_filament()
    {
        $assets = FilamentAsset::getStyles();
        
        $hasAsset = false;
        foreach ($assets as $asset) {
            if ($asset->getId() === 'asset-manager-styles') {
                $hasAsset = true;
                break;
            }
        }
        
        $this->assertTrue($hasAsset, 'Asset Manager styles were not registered with Filament.');
    }

    /** @test */
    public function test_it_publishes_assets_via_filament_command()
    {
        // Clean up previous test runs if any
        $publicPath = public_path('css/innopanda/asset-manager');
        if (File::exists($publicPath)) {
            File::deleteDirectory($publicPath);
        }

        // Run the filament asset publishing command
        $exitCode = Artisan::call('filament:assets');
        $this->assertEquals(0, $exitCode);

        // Verify that filament placed the asset in its expected location
        $this->assertTrue(File::exists(public_path('css/innopanda/asset-manager/asset-manager-styles.css')), 'Filament did not publish the CSS to the expected path.');
    }
}
