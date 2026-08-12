<?php

namespace Innopanda\AssetManager\Tests\Feature\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Tests\TestCase;
use Livewire\Livewire;
use Innopanda\AssetManager\Livewire\MediaBrowser;

class SelectionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_dispatches_event_when_asset_is_selected()
    {
        $asset = Asset::create([
            'title' => 'Select Me',
            'disk' => 'public',
            'path' => 'assets/select.png',
            'mime_type' => 'image/png'
        ]);

        Livewire::test(MediaBrowser::class)
            ->set('pickerId', 'test-picker')
            ->call('selectAsset', $asset->id)
            ->assertOk();
    }

    public function test_it_can_toggle_multiple_assets()
    {
        $asset1 = Asset::create(['title' => 'Asset 1', 'path' => 'assets/1.png']);
        $asset2 = Asset::create(['title' => 'Asset 2', 'path' => 'assets/2.png']);

        Livewire::test(MediaBrowser::class)
            ->call('toggleSelection', $asset1->id)
            ->assertSet('selected', [$asset1->id])
            ->call('toggleSelection', $asset2->id)
            ->assertSet('selected', [$asset1->id, $asset2->id])
            ->call('toggleSelection', $asset1->id)
            ->assertSet('selected', [$asset2->id])
            ->call('clearSelection')
            ->assertSet('selected', []);
    }
}
