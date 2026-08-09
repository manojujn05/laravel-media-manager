# Media Picker Component

The `MediaPicker` component provides a fully reusable Livewire interface for selecting and assigning assets from the Media Library. It is designed to be embedded directly into any form (e.g., Recipe Forms, User Profile Forms) to provide a CMS-style media selector.

## Basic Usage

Embed the component within your form using the Livewire directive:

```blade
@livewire('asset-manager.media-picker')
```

## Advanced Usage

You can customize the component's behavior by passing properties to it:

```blade
@livewire(
    'asset-manager.media-picker',
    [
        'collection' => 'recipe-images',
        'selectedAssetId' => 10,
        'showPreview' => true,
        'showRemove' => true,
        'inputClass' => 'custom-class'
    ]
)
```

### Component Properties

| Property | Type | Default | Description |
|---|---|---|---|
| `collection` | `?string` | `null` | The specific Spatie media collection to use when resolving the image URL. (e.g. `avatars`, `banners`). |
| `selectedAssetId` | `?int` | `null` | Pre-load an existing image into the component by providing its Asset ID. |
| `showPreview` | `bool` | `true` | Toggle the display of the image preview thumbnail. |
| `showRemove` | `bool` | `true` | Toggle the "Remove Image" button. |
| `showUrl` | `bool` | `true` | Toggle the display of the read-only URL input field. |
| `inputClass` | `string` | `''` | Custom CSS classes to apply to the URL input field. |
| `buttonClass` | `string` | `''` | Custom CSS classes to apply to the buttons. |
| `wrapperClass` | `string` | `''` | Custom CSS classes to apply to the outer component wrapper. |

## Handling Selection in the Parent Component

When a user selects or removes an image, the `MediaPicker` dispatches events that the parent form component must listen to. **It is highly recommended that you store the `assetId` in your database, not the URL.**

### 1. `media-selected` Event
Emitted when a user successfully selects an asset.

**Payload:**
- `assetId` (int)
- `url` (string)

**Listener Example:**
```php
use Livewire\Attributes\On;

#[On('media-selected')]
public function setImage($assetId, $url)
{
    $this->imageAssetId = $assetId;
}
```

### 2. `media-removed` Event
Emitted when a user clicks the "Remove Image" button.

**Listener Example:**
```php
use Livewire\Attributes\On;

#[On('media-removed')]
public function removeImage()
{
    $this->imageAssetId = null;
}
```

## Event Validation & Error Handling

If a user selects an asset that is missing its associated Spatie media record or if a public URL cannot be generated, the component will automatically dispatch a `notify` event with an error message and gracefully close the picker without emitting `media-selected`.
