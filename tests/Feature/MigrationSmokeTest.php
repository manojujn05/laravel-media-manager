<?php

namespace Innopanda\AssetManager\Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Innopanda\AssetManager\Tests\TestCase;

class MigrationSmokeTest extends TestCase
{
    /** @test */
    public function test_it_boots_and_creates_all_expected_tables()
    {
        $this->assertTrue(Schema::hasTable('asset_folders'));
        $this->assertTrue(Schema::hasTable('assets'));
        $this->assertTrue(Schema::hasTable('asset_usages'));
        $this->assertTrue(Schema::hasTable('asset_activity_logs'));
        $this->assertTrue(Schema::hasTable('media'));
        $this->assertTrue(Schema::hasTable('users'));
    }

    /** @test */
    public function test_it_can_rollback_all_migrations_successfully()
    {
        // Assert tables exist first
        $this->assertTrue(Schema::hasTable('assets'));

        // Run rollback
        $this->artisan('migrate:rollback', ['--database' => 'testing'])->run();

        // Assert our tables have been removed
        $this->assertFalse(Schema::hasTable('assets'));
        $this->assertFalse(Schema::hasTable('asset_folders'));
        $this->assertFalse(Schema::hasTable('asset_usages'));
        $this->assertFalse(Schema::hasTable('asset_activity_logs'));
    }
}
