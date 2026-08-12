<?php

namespace Innopanda\AssetManager\Tests\Feature\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Innopanda\AssetManager\Models\Folder;
use Innopanda\AssetManager\Tests\TestCase;
use Livewire\Livewire;
use Innopanda\AssetManager\Livewire\CreateFolder;
use Innopanda\AssetManager\Livewire\FolderTree;
use Innopanda\AssetManager\Livewire\MediaBrowser;

class FolderOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_folder_via_livewire()
    {
        Livewire::test(CreateFolder::class)
            ->set('name', 'New Test Folder')
            ->call('save')
            ->assertDispatched('folder-created')
            ->assertSet('open', false);

        $this->assertDatabaseHas('asset_folders', [
            'name' => 'New Test Folder',
            'parent_id' => null,
        ]);
    }

    public function test_it_can_create_a_nested_folder_via_livewire()
    {
        $parent = Folder::create(['name' => 'Parent Folder']);

        Livewire::test(CreateFolder::class)
            ->call('openModal', $parent->id)
            ->set('name', 'Child Folder')
            ->call('save')
            ->assertDispatched('folder-created')
            ->assertSet('open', false);

        $this->assertDatabaseHas('asset_folders', [
            'name' => 'Child Folder',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_it_validates_folder_name_is_required()
    {
        Livewire::test(CreateFolder::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_folder_tree_renders_folders()
    {
        $parent = Folder::create(['name' => 'Parent 1']);
        Folder::create(['name' => 'Child 1', 'parent_id' => $parent->id]);

        Livewire::test(FolderTree::class)
            ->assertSee('Parent 1')
            ->assertSee('Child 1');
    }

    public function test_media_browser_can_select_folder()
    {
        $folder = Folder::create(['name' => 'Target Folder']);

        Livewire::test(MediaBrowser::class)
            ->call('selectFolder', $folder->id)
            ->assertSet('folder', $folder->id);
    }
}
