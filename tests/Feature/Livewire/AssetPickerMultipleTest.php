<?php

namespace Innopanda\AssetManager\Tests\Feature\Livewire;

use Innopanda\AssetManager\Livewire\MediaBrowser;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Models\Folder;
use Innopanda\AssetManager\Filament\Forms\Components\AssetPicker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Innopanda\AssetManager\Tests\TestCase;

class AssetPickerMultipleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create some assets
        Asset::create(['id' => 12, 'name' => 'Image A', 'path' => 'a.jpg', 'disk' => 'public', 'mime_type' => 'image/jpeg']);
        Asset::create(['id' => 18, 'name' => 'Image B', 'path' => 'b.jpg', 'disk' => 'public', 'mime_type' => 'image/jpeg']);
        Asset::create(['id' => 25, 'name' => 'Image C', 'path' => 'c.jpg', 'disk' => 'public', 'mime_type' => 'image/jpeg']);
    }

    public function test_single_selection_dispatches_correct_event()
    {
        Livewire::test(MediaBrowser::class, ['multiple' => false, 'pickerId' => 'test-picker'])
            ->call('selectAsset', 12)
            ->assertDispatched('asset-manager:image-selected', function ($event, $payload) {
                // Livewire v3 payload is passed directly in dispatch if no named arguments or array
                // Depending on the exact Livewire version, the payload might be the first argument
                return $payload['pickerId'] === 'test-picker' 
                    && $payload['id'] === 12
                    && str_contains($payload['url'], 'a.jpg');
            })
            ->assertNotDispatched('asset-manager:assets-selected');
    }

    public function test_multiple_selection_can_select_three_assets()
    {
        Livewire::test(MediaBrowser::class, ['multiple' => true, 'pickerId' => 'test-picker'])
            ->call('selectAsset', 12)
            ->call('selectAsset', 18)
            ->call('selectAsset', 25)
            ->assertSet('pickerSelectedAssets', [12, 18, 25]);
    }

    public function test_deselecting_an_already_selected_asset()
    {
        Livewire::test(MediaBrowser::class, ['multiple' => true, 'pickerId' => 'test-picker'])
            ->call('selectAsset', 12)
            ->call('selectAsset', 18)
            ->call('selectAsset', 25)
            ->call('selectAsset', 18)
            ->assertSet('pickerSelectedAssets', [12, 25]);
    }

    public function test_confirm_selection_dispatches_correct_event_with_order()
    {
        Livewire::test(MediaBrowser::class, ['multiple' => true, 'pickerId' => 'test-picker'])
            ->call('selectAsset', 25)
            ->call('selectAsset', 12)
            ->call('selectAsset', 18)
            ->call('confirmSelection')
            ->assertDispatched('asset-manager:assets-selected', function ($event, $payload) {
                return $payload['pickerId'] === 'test-picker'
                    && count($payload['assets']) === 3
                    && $payload['assets'][0]['id'] === 25
                    && $payload['assets'][1]['id'] === 12
                    && $payload['assets'][2]['id'] === 18;
            })
            ->assertNotDispatched('asset-manager:image-selected');
    }

    public function test_multiple_selection_survives_pagination_and_folder_navigation()
    {
        Folder::create(['id' => 99, 'name' => 'Test Folder', 'parent_id' => null]);
        
        Livewire::test(MediaBrowser::class, ['multiple' => true])
            ->call('selectAsset', 12)
            ->call('selectFolder', 99)
            ->assertSet('pickerSelectedAssets', [12])
            ->set('page', 2)
            ->call('selectAsset', 18)
            ->assertSet('pickerSelectedAssets', [12, 18]);
    }

    public function test_existing_ids_are_loaded_via_event()
    {
        Livewire::test(MediaBrowser::class, ['multiple' => true, 'pickerId' => 'test-picker'])
            ->dispatch('asset-picker-opened', pickerId: 'test-picker', selection: [12, 25])
            ->assertSet('pickerSelectedAssets', [12, 25]);
    }
    
    public function test_existing_ids_ignore_other_pickers()
    {
        Livewire::test(MediaBrowser::class, ['multiple' => true, 'pickerId' => 'test-picker'])
            ->dispatch('asset-picker-opened', pickerId: 'other-picker', selection: [12, 25])
            ->assertSet('pickerSelectedAssets', []);
    }

    public function test_return_ids_configuration()
    {
        $field = AssetPicker::make('gallery')->multiple()->returnIds();
        $this->assertTrue($field->isMultiple());
        $this->assertTrue($field->returnsIds());
    }

    public function test_default_multiple_mode_returns_urls()
    {
        $field = AssetPicker::make('gallery')->multiple();
        $this->assertTrue($field->isMultiple());
        $this->assertFalse($field->returnsIds());
    }
}
