# Laravel Asset Manager

A professional, drop-in asset management package for Laravel, built with **Livewire** and powered by **Spatie Laravel Media Library**.

Laravel Asset Manager provides a complete interface for uploading, organizing, browsing, selecting, replacing, versioning, and managing media assets. It can be used independently with Livewire or integrated directly into **Filament** forms.

---

## ✨ Features

* 📁 **Virtual Folder Tree**
  Organize assets into nested folders without changing their physical storage paths.

* 🎯 **Asset Picker**
  Browse, search, upload, preview, and select assets through a reusable modal interface.

* 🖼️ **Thumbnail Generation**
  Generate optimized previews and thumbnails using Spatie Laravel Media Library.

* 📦 **Livewire Components**
  Use the asset browser, uploader, folder tree, picker, preview, replacement drawer, and other components independently.

* ☑️ **Multiple Asset Selection**
  Select one or multiple assets depending on your application requirements.

* 🔄 **File Replacement**
  Replace an existing asset while preserving its previous versions and asset relationships.

* 🕘 **Asset Version History**
  Previous versions of replaced files are preserved and can be reviewed or restored.

* 🗑️ **Custom Delete Confirmation**
  Assets and folders use styled Livewire confirmation modals instead of native browser JavaScript confirmation dialogs.

* 🏢 **Tenant-Aware Architecture**
  Supports optional tenant-based asset isolation.

* 🧩 **Filament Integration**
  Use `AssetPicker` directly inside Filament forms.

* 💾 **Spatie Media Library Integration**
  Leverages Spatie Laravel Media Library for media storage, collections, conversions, and file management.

---

# 📋 Requirements

* PHP **8.3+**
* Laravel **12 or 13**
* Livewire **4.x**
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

## Publish Configuration

Publish the Asset Manager configuration file:

```bash
php artisan vendor:publish --tag=asset-manager-config
```

This creates:

```text
config/asset-manager.php
```

## Publish Database Migrations

Publish the Asset Manager migrations to your application's `database/migrations` directory:

```bash
php artisan vendor:publish --tag=asset-manager-migrations --force
```

Then run the migrations:

```bash
php artisan migrate
```

Verify the migration status:

```bash
php artisan migrate:status
```

## Publish Frontend Assets

Publish the compiled Asset Manager frontend assets:

```bash
php artisan vendor:publish --tag=asset-manager-assets --force
```

The assets will be copied to:

```text
public/vendor/asset-manager
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

The package includes all required database migrations for asset management.

Publish the package migrations to your application's `database/migrations` directory:

```bash
php artisan vendor:publish --tag=asset-manager-migrations --force
```

This publishes the Asset Manager migrations, including:

```text
database/migrations/
├── 2026_07_30_124420_create_asset_folders_table.php
├── 2026_07_30_124502_create_assets_table.php
├── 2026_07_30_124544_create_asset_usages_table.php
├── 2026_07_30_124554_create_asset_activity_logs_table.php
├── 2026_08_04_061714_create_media_table.php
└── 2026_08_13_000000_create_asset_versions_table.php
```

The `asset_versions` migration is required for **File Replacement** and **Asset Version History** functionality.

After publishing the migrations, run:

```bash
php artisan migrate
```

To verify the migration status:

```bash
php artisan migrate:status
```

> **Note:** The package also loads its migrations automatically. Publishing them is recommended when you want the migration files available directly in your Laravel application's `database/migrations` directory and managed alongside your application's migrations.

### Spatie Media Library

Laravel Asset Manager uses **Spatie Laravel Media Library** for media storage and management.

Make sure the required Spatie migrations are also available in your application.

If required, publish the Spatie Media Library migrations and then run:

```bash
php artisan migrate
```

The Asset Manager requires the media-related tables to be available before uploading and managing assets.

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

# ☁️ S3 Existing Files

When configuring the Asset Manager to use an S3-compatible disk:

```env
ASSET_MANAGER_DISK=s3
```

You can discover and index files that were already uploaded to your S3 bucket (or uploaded externally via AWS Console, another application, etc.).

## Synchronization

To synchronize existing S3 objects into the Asset Manager database:

```bash
php artisan asset-manager:sync
```

**How it works:**
1. The command scans the configured root of your S3 bucket.
2. It creates an `Asset` database record for any file not currently indexed.
3. **No physical duplicates are created.** Files remain in S3 and no unnecessary Spatie Media records are created.
4. **No files are deleted.** If an S3 file is missing, the sync will report it but it won't destructively delete the database record.
5. The `assets` table serves as the metadata index, so the Media Browser uses efficient database pagination instead of querying the S3 API directly.

## Configuration Options

You can control S3 discovery behavior in `config/asset-manager.php`:

```php
'sync' => [
    // Define the root folder in your bucket to scan (default: '/')
    // Useful if your bucket is shared with other applications.
    'root_path' => env('ASSET_MANAGER_ROOT', '/'),
    // Set to true if your S3 bucket is private
    'private_urls' => env('ASSET_MANAGER_PRIVATE_URLS', false),
    // Duration in minutes for temporary signed URLs
    'temporary_url_expiration' => env('ASSET_MANAGER_TEMP_URL_EXPIRES', 60),
],
```

## Scheduled Synchronization

To automatically keep Asset Manager up-to-date with your bucket, you can schedule the sync command in your application's `routes/console.php` or `app/Console/Kernel.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('asset-manager:sync')->hourly();
```

## Private S3 Buckets

If your bucket is private, ensure you configure `ASSET_MANAGER_PRIVATE_URLS=true` in your `.env`. 
The Asset Manager will securely generate **temporary signed URLs** for previews and downloads without exposing your AWS credentials to the browser.
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

To allow multiple assets, simply use `->multiple()`.

By default, the Asset Picker will return an array of URLs for backward compatibility. 

```php
AssetPicker::make('gallery')
    ->label('Gallery Images')
    ->multiple();
```

Resulting Filament Form State:
```php
'gallery' => [
    'https://example.com/storage/image-1.jpg',
    'https://example.com/storage/image-2.jpg',
]
```

### Save as IDs

If your application prefers to store Asset IDs instead of URLs (e.g. for `BelongsToMany` relationships or JSON columns), use the `->returnIds()` method (or `->saveAsId()`):

```php
AssetPicker::make('gallery')
    ->label('Gallery Images')
    ->multiple()
    ->returnIds(); // or ->saveAsId()
```

Resulting Filament Form State:
```php
'gallery' => [
    12,
    18,
    25
]
```

You can also use `->returnIds()` with single selection:
```php
AssetPicker::make('image')
    ->returnIds(); // State will be: 'image' => 12
```

This makes the Asset Manager suitable for:

* Profile images
* Product images
* Gallery images
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
