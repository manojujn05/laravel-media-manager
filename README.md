# Laravel Asset Manager

A professional, drop-in asset management package for Laravel, built with **Livewire** and powered by **Spatie Laravel Media Library**.

Laravel Asset Manager provides a complete interface for uploading, organizing, browsing, selecting, replacing, and managing media assets. It can be used independently with Livewire or integrated directly into **Filament** forms.

## ✨ Features

* 📁 **Virtual Folder Tree**
  Organize assets into nested folders without changing their physical storage paths.

* 🕒 **File Version History**
  Keep track of file versions and restore previous versions when needed.

* 🔄 **Seamless File Replacement**
  Replace an existing asset while preserving its database relationships and associations.

* 🎯 **Asset Picker**
  Browse, search, upload, and select assets through a reusable modal interface.

* 🖼️ **Thumbnail Generation**
  Generate optimized previews and thumbnails using Spatie Laravel Media Library.

* 📦 **Livewire Components**
  Use the asset browser, uploader, folder tree, picker, preview, and other components independently.

* ☑️ **Multiple Asset Selection**
  Select one or multiple assets depending on your application requirements.

* 🏢 **Tenant-Aware Architecture**
  Supports optional tenant-based asset isolation.

* 🧩 **Filament Integration**
  Use `AssetPicker` directly inside Filament forms.

* 💾 **Spatie Media Library Integration**
  Leverages Spatie Laravel Media Library for media storage, collections, conversions, and file management.

---

## 📋 Requirements

* PHP **8.3+**
* Laravel **12 or 13**
* Livewire **3.x**
* Spatie Laravel Media Library **11.x**
* Filament **4.x** — optional, only required for Filament integration

> Filament is an optional dependency. The core Asset Manager and Livewire components can be used without Filament.

---

# 📦 Installation

Install the package using Composer:

```bash
composer require innopanda/laravel-asset-manager
```

After installation, Laravel automatically discovers the package service provider.

Verify that the package is installed:

```bash
php artisan package:discover
```

You should see:

```text
innopanda/laravel-asset-manager ................................ DONE
```

---

# ⚙️ Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=asset-manager-config
```

This creates:

```text
config/asset-manager.php
```

Example configuration:

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    */

    'disk' => env('ASSET_MANAGER_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Maximum Upload Size
    |--------------------------------------------------------------------------
    |
    | Value is in KB.
    |
    */

    'max_upload_size' => 10240,

    /*
    |--------------------------------------------------------------------------
    | Allowed MIME Types
    |--------------------------------------------------------------------------
    */

    'allowed_mime_types' => [
        'image/jpeg',
        'image/png',
        'image/webp',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Awareness
    |--------------------------------------------------------------------------
    */

    'tenant_aware' => false,

];
```

Adjust the configuration according to your application's requirements.

---

# 🗄️ Database Migrations

The package contains its migrations and automatically loads them through the package service provider.

You do **not** need to publish the package migrations manually.

Run:

```bash
php artisan migrate
```

To verify the migrations:

```bash
php artisan migrate:status
```

The package creates the required tables for asset management, folders, tags, usages, activity logs, versions, and related functionality.

> Spatie Laravel Media Library also requires its own `media` table. Make sure the Spatie migrations have been published/applied in your application.

---

# 🖼️ Storage

If you are using the default Laravel `public` disk, create the storage symlink:

```bash
php artisan storage:link
```

Make sure your `.env` contains the appropriate disk configuration:

```env
ASSET_MANAGER_DISK=public
```

You can use another Laravel filesystem disk if required.

---

# 🎨 Publishing Frontend Assets

The package ships its compiled frontend assets.

To publish them:

```bash
php artisan vendor:publish --tag=asset-manager-assets
```

The assets will be copied to:

```text
public/vendor/asset-manager
```

Expected files:

```text
public/vendor/asset-manager/
├── css/
│   └── asset-manager.css
└── js/
    └── asset-manager.js
```

If you are developing the package locally, build the package assets from the package repository:

```bash
npm install
npm run build
```

---

# ⚡ Livewire Usage

Laravel Asset Manager can be used directly with Livewire without Filament.

## Media Browser

Add the Media Browser component to a Blade view:

```blade
<livewire:asset-manager.media-browser />
```

The browser provides functionality for:

* Folder navigation
* Asset browsing
* Asset searching
* Asset selection
* Asset uploading
* Asset previews

---

## Media Picker

To display the reusable asset picker:

```blade
<livewire:asset-manager.media-picker />
```

The picker can be used inside your own Livewire forms and components.

---

## Listening for Asset Selection

The asset picker dispatches an `asset-selected` event.

In the parent Livewire component:

```php
use Livewire\Attributes\On;

#[On('asset-selected')]
public function updateImage($image)
{
    $this->selectedImage = $image;
}
```

You can then use the selected asset in your application.

---

# 🧩 Filament Integration

Filament integration is available when Filament is installed in the host application.

Use the Asset Picker inside a Filament form:

```php
use Innopanda\AssetManager\Filament\Forms\Components\AssetPicker;

AssetPicker::make('image')
    ->label('Image');
```

## Multiple Selection

To allow multiple assets:

```php
AssetPicker::make('gallery')
    ->label('Gallery Images')
    ->multiple();
```

This makes the Asset Manager suitable for:

* Profile images
* Product images
* Gallery images
* Recipe images
* Workout images
* Documents
* Other application media

---

# 📁 Folder Management

Assets can be organized using virtual folders.

The folder structure does not require changing the physical storage location of the asset.

Example:

```text
Media
├── Images
│   ├── Products
│   ├── Recipes
│   └── Workouts
├── Documents
└── Videos
```

Folders can be nested and assets can be moved between folders.

---

# 🔄 File Replacement

Existing files can be replaced through the asset management interface.

The replacement functionality is designed to preserve the asset's existing relationships and associations while updating the underlying media.

---

# 🕒 Version History

Asset versions can be tracked through the version history functionality.

This allows applications to:

* View previous versions
* Track changes
* Restore older versions

---

# 🧱 Available Livewire Components

The package provides reusable Livewire components including:

```text
asset-manager.media-browser
asset-manager.media-picker
asset-manager.browser-toolbar
asset-manager.browser-grid
asset-manager.asset-card
asset-manager.browser-preview
asset-manager.browser-uploader
asset-manager.browser-sidebar
asset-manager.asset-picker
asset-manager.folder-node
asset-manager.create-folder
asset-manager.asset-picker-modal
asset-manager.asset-uploader
asset-manager.folder-tree
asset-manager.replace-file-drawer
```

These components are registered automatically by the package service provider.

---

# 🧪 Testing

The package contains its own PHPUnit test suite.

From the package repository:

```bash
composer install
```

Run the complete test suite:

```bash
vendor/bin/phpunit
```

Example:

```text
PHPUnit 12.x

......................... 25 / 25

OK (25 tests, 88 assertions)
```

## Run Individual Tests

Run model tests:

```bash
vendor/bin/phpunit tests/Unit/ModelsTest.php
```

Run API tests:

```bash
vendor/bin/phpunit tests/Feature/Api/AssetApiTest.php
```

Run Livewire tests:

```bash
vendor/bin/phpunit tests/Feature/Livewire
```

Run Spatie integration tests:

```bash
vendor/bin/phpunit tests/Feature/SpatieIntegrationTest.php
```

---

# 🎨 Build Frontend Assets

Install Node dependencies:

```bash
npm install
```

Build production assets:

```bash
npm run build
```

The compiled files are generated in:

```text
dist/
├── css/
│   └── asset-manager.css
└── js/
    └── asset-manager.js
```

---

# 🔍 Development

Clone the repository:

```bash
git clone https://github.com/manojujn05/laravel-media-manager.git
```

Enter the package directory:

```bash
cd laravel-asset-manager
```

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

Build assets:

```bash
npm run build
```

Run the test suite:

```bash
vendor/bin/phpunit
```

---

# 🏠 Local Package Testing

To test a development version of the package in a Laravel application, you can use a Composer path repository.

In the Laravel application's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../laravel-asset-manager"
        }
    ]
}
```

Then require the package:

```bash
composer require innopanda/laravel-asset-manager:@dev
```

Composer will use the local package instead of downloading it from Packagist.

This is recommended when developing and testing the package before creating a release.

---

# 🚀 Release Workflow

Before creating a release, run:

```bash
composer validate
```

Then:

```bash
composer install
vendor/bin/phpunit
npm install
npm run build
```

Verify the working tree:

```bash
git status
```

Commit the changes:

```bash
git add .
git commit -m "Prepare release"
```

Create a version tag:

```bash
git tag -a v1.0.0 -m "Release v1.0.0"
```

Push the branch and tag:

```bash
git push origin main
git push origin v1.0.0
```

---

# 🤝 Contributing

Contributions are welcome!

1. Fork the repository.
2. Create a feature branch:

```bash
git checkout -b feature/my-feature
```

3. Make your changes.
4. Add or update tests.
5. Run the test suite:

```bash
vendor/bin/phpunit
```

6. Build frontend assets:

```bash
npm run build
```

7. Commit your changes:

```bash
git add .
git commit -m "Add my feature"
```

8. Push your branch:

```bash
git push origin feature/my-feature
```

9. Open a Pull Request.

Please ensure that existing functionality continues to work and that new functionality includes appropriate tests.

---

# 📚 Architecture

The package is structured around several major areas:

```text
laravel-asset-manager/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── dist/
│   ├── css/
│   └── js/
├── resources/
│   └── views/
├── routes/
├── src/
│   ├── Api/
│   ├── Controllers/
│   ├── Filament/
│   ├── Livewire/
│   ├── Models/
│   ├── Services/
│   └── Support/
├── tests/
│   ├── Unit/
│   └── Feature/
└── composer.json
```

The package keeps the asset-management functionality isolated from the host Laravel application.

---

# 📜 License

Laravel Asset Manager is open-sourced software licensed under the **MIT License**.

See the `LICENSE` file for more information.

---

# 🔗 Repository

GitHub:

https://github.com/manojujn05/laravel-media-manager
