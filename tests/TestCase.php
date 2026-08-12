<?php

namespace Innopanda\AssetManager\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Innopanda\AssetManager\AssetManagerServiceProvider;
use Livewire\LivewireServiceProvider;

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
            \Spatie\MediaLibrary\MediaLibraryServiceProvider::class,
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
        $app['config']->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');
    }

    protected function defineDatabaseMigrations()
    {
        // Create mock users table for foreign keys
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Run package migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Run Spatie Media Library migrations
        $spatiePath = __DIR__.'/../vendor/spatie/laravel-medialibrary/database/migrations';
        if (is_dir($spatiePath)) {
            $this->loadMigrationsFrom($spatiePath);
        } else {
            // Load them using artisan if vendor path not available (e.g., globally installed or symlinked)
            $this->artisan('migrate', ['--database' => 'testing'])->run();
        }
    }
}
