<?php

namespace Innopanda\AssetManager\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Innopanda\AssetManager\AssetManagerServiceProvider;
use Innopanda\AssetManager\AssetManagerFilamentServiceProvider;
use Livewire\LivewireServiceProvider;
use Filament\FilamentServiceProvider;

class FilamentTestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        // Notice we are intentionally including FilamentServiceProvider here
        return [
            LivewireServiceProvider::class,
            \Spatie\MediaLibrary\MediaLibraryServiceProvider::class,
            \Filament\Support\SupportServiceProvider::class,
            FilamentServiceProvider::class,
            AssetManagerServiceProvider::class,
            // AssetManagerFilamentServiceProvider is automatically registered 
            // by AssetManagerServiceProvider if FilamentAsset exists.
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        
        $app['config']->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');
        
        $app['config']->set('cache.default', 'array');
        $app['config']->set('session.driver', 'array');
    }

    protected function defineDatabaseMigrations()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $spatiePath = __DIR__.'/../vendor/spatie/laravel-medialibrary/database/migrations';
        if (is_dir($spatiePath)) {
            $this->loadMigrationsFrom($spatiePath);
        } else {
            $this->artisan('migrate', ['--database' => 'testing'])->run();
        }
    }
}
