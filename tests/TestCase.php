<?php

namespace Innopanda\AssetManager\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Innopanda\AssetManager\AssetManagerServiceProvider;
use Livewire\LivewireServiceProvider;
use Filament\FilamentServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            FilamentServiceProvider::class,
            AssetManagerServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        
        // Use array for testing cache and session
        $app['config']->set('cache.default', 'array');
        $app['config']->set('session.driver', 'array');
    }

    protected function defineDatabaseMigrations()
    {
        // Run package migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Run Spatie Media Library migrations (typically needed if our package relies on it)
        $this->artisan('migrate', ['--database' => 'testing'])->run();
        
        // We can publish and migrate spatie migrations if not present, but since it's a testbench
        // environment, we might need to load Spatie's migrations directly.
        $spatiePath = __DIR__.'/../vendor/spatie/laravel-medialibrary/database/migrations';
        if (is_dir($spatiePath)) {
            $this->loadMigrationsFrom($spatiePath);
        }
    }
}
