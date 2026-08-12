<?php

namespace Innopanda\AssetManager\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Models\Folder;
use Innopanda\AssetManager\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

// Dummy model to test HasAssetDependencies
class DummyUsageModel extends Model
{
    protected $table = 'dummy_usages';
    protected $fillable = ['asset_id'];
    public $timestamps = false;
}

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    protected function defineDatabaseMigrations()
    {
        parent::defineDatabaseMigrations();
        
        // Setup dummy table for usage testing
        Schema::create('dummy_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
        });
    }

    /** @test */
    public function test_asset_automatically_generates_uuid_on_creation()
    {
        $asset = Asset::create([
            'title' => 'Test Asset',
            'disk' => 'public',
            'path' => 'assets/test.png',
            'mime_type' => 'image/png',
        ]);

        $this->assertNotNull($asset->uuid);
        $this->assertIsString($asset->uuid);
    }

    /** @test */
    public function test_folder_has_recursive_children_relationship()
    {
        $rootFolder = Folder::create(['name' => 'Root']);
        $childFolder = Folder::create(['name' => 'Child', 'parent_id' => $rootFolder->id]);
        $grandChildFolder = Folder::create(['name' => 'Grandchild', 'parent_id' => $childFolder->id]);

        $rootFolder->load('childrenRecursive');

        $this->assertCount(1, $rootFolder->childrenRecursive);
        $this->assertEquals('Child', $rootFolder->childrenRecursive->first()->name);
        $this->assertCount(1, $rootFolder->childrenRecursive->first()->childrenRecursive);
        $this->assertEquals('Grandchild', $rootFolder->childrenRecursive->first()->childrenRecursive->first()->name);
    }

    /** @test */
    public function test_folder_parent_relationship()
    {
        $rootFolder = Folder::create(['name' => 'Root']);
        $childFolder = Folder::create(['name' => 'Child', 'parent_id' => $rootFolder->id]);

        $this->assertEquals($rootFolder->id, $childFolder->parent->id);
    }

    /** @test */
    public function test_asset_deletion_is_prevented_if_used_in_dependencies()
    {
        // Override the config for this test
        config(['asset-manager.usage_models' => [
            'Dummy' => DummyUsageModel::class,
        ]]);

        $asset = Asset::create([
            'title' => 'Test Asset',
            'disk' => 'public',
            'path' => 'assets/test.png',
            'mime_type' => 'image/png',
        ]);

        // Create a usage record
        DummyUsageModel::create(['asset_id' => $asset->id]);

        $this->assertTrue($asset->isUsedInDependencies());
        
        $usageDetails = $asset->getUsageDetails();
        $this->assertArrayHasKey('Dummy', $usageDetails);
        $this->assertEquals(1, $usageDetails['Dummy']);

        // Expect exception when trying to delete
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cannot delete asset because it is currently linked to active dependencies.");
        
        $asset->delete();
    }
}
