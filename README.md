# Laravel Media Manager

A professional, drop-in asset management ecosystem for Laravel. Built entirely on top of **Livewire** and backed by the rock-solid **Spatie Laravel Media Library**, this package bridges the gap between complex file attachments and a seamless user experience.

Whether you need a robust dashboard to organize files into nested directories or a dynamic modal popup to pick an image inside a form, this package provides polished, ready-to-use blade/livewire components out of the box.

---

## ✨ Key Features

* 📁 **Virtual Folder Tree** — Drag, drop, and structure your media assets into logical nested directories without affecting physical storage paths.
* 🎯 **Contextual Asset Picker** — A clean, searchable grid modal designed to let users browse, select, or upload images directly within your existing forms.
* 🖼️ **Thumbnail Engine** — Fast, responsive, and highly optimized image previews driven by an intelligent background caching layer.
* 📦 **Modular Livewire Components** — From toolbars and sidebar details to upload dropzones, everything is componentized for deep structural customization.
* 🗂️ **Multiple Asset Selection** — Pick one or many files at a time depending on the context of your form.
* 🏢 **Tenant-Aware Media Management** — Optional multi-tenant isolation so each tenant only sees and manages their own assets.
* 🧩 **Filament Integration** — A ready-made `AssetPicker` form component for effortless use inside Filament panels.
* ⚡ **Livewire Integration** — Standalone Livewire components that work independently of Filament, for use anywhere in your app.
* 📚 **Spatie Media Library Powered** — Built on top of the trusted `spatie/laravel-medialibrary` package for storage, conversions, and collections.

---

## 📋 Requirements

* PHP 8.3+
* Laravel 12 / 13
* Filament 5
* Livewire 4
* Spatie Laravel Media Library

---

## 📦 Installation

Install the package via Composer:

```bash
composer require innopanda/laravel-asset-manager
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=asset-manager-config
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag=asset-manager-migrations
php artisan migrate
```

(Optional) Publish the views if you'd like to customize the UI:

```bash
php artisan vendor:publish --tag=asset-manager-views
```

---

## 🎛️ Filament Usage

Add the `AssetPicker` component to any Filament form:

```php
use Innopanda\AssetManager\Filament\Forms\Components\AssetPicker;

AssetPicker::make('image')
    ->label('Image');
```

You can also allow multiple selections:

```php
AssetPicker::make('gallery')
    ->label('Gallery Images')
    ->multiple();
```

---

## ⚡ Livewire Usage

Drop the media picker component directly into any Blade view:

```blade
<livewire:asset-manager.media-picker />
```

To bind the selected image back to a parent Livewire component, listen for the dispatched event:

```php
use Livewire\Attributes\On;

#[On('asset-selected')]
public function updateImage($image)
{
    $this->selectedImage = $image;
}
```

---

## ⚙️ Configuration

After publishing the config file, you can customize behavior such as storage disk, allowed file types, folder structure, and tenant scoping in:

```
config/asset-manager.php
```

Example options include:

```php
return [
    'disk' => env('ASSET_MANAGER_DISK', 'public'),
    'max_upload_size' => 10240, // in KB
    'allowed_mime_types' => ['image/jpeg', 'image/png', 'image/webp'],
    'tenant_aware' => false,
];
```

---

## 🛠️ Contributing

Contributions are welcome! Please open an issue or submit a pull request on GitHub.

---

## 📜 Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for more information on recent changes.

---

## 📄 License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.