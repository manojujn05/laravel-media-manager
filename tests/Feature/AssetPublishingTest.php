<?php

namespace Innopanda\AssetManager\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Innopanda\AssetManager\Tests\TestCase;

class AssetPublishingTest extends TestCase
{
    /** @test */
    public function test_it_publishes_package_assets_using_laravel_vendor_publish()
    {
        // Clean up previous test runs
        $publicPath = public_path('vendor/asset-manager');
        if (File::exists($publicPath)) {
            File::deleteDirectory($publicPath);
        }

        // Run the publish command
        $exitCode = Artisan::call('vendor:publish', ['--tag' => 'asset-manager-assets']);
        $this->assertEquals(0, $exitCode);

        // Verify the CSS was published
        $this->assertTrue(File::exists(public_path('vendor/asset-manager/css/asset-manager.css')), 'Laravel vendor:publish did not publish the CSS to the expected path.');
    }
}
