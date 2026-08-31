<?php

namespace Innopanda\AssetManager\Filament\Forms\Components;

use Filament\Forms\Components\Field;
use Innopanda\AssetManager\Models\Asset;
use Illuminate\Support\Facades\Storage;
use Closure;

class AssetPicker extends Field
{
    protected string $view = 'asset-manager::filament.forms.components.asset-picker';

    protected bool | Closure $isMultiple = false;
    protected bool | Closure $returnsIds = false;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function getDisplayUrls(): array
    {
        $state = $this->getState();
        
        if (blank($state)) {
            return [];
        }

        if (! $this->returnsIds()) {
            $urls = is_array($state) ? array_values($state) : [$state];
            return array_map(fn($url) => ['id' => null, 'url' => $url], $urls);
        }

        $ids = is_array($state) ? $state : [$state];
        $assets = Asset::whereIn('id', $ids)
            ->get()
            ->sortBy(fn ($asset) => array_search($asset->id, $ids))
            ->values();
        
        return $assets->map(function ($asset) {
            $url = $asset->getUrl();
            return [
                'id' => $asset->id,
                'url' => $url,
            ];
        })->filter()->toArray();
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function returnIds(bool | Closure $condition = true): static
    {
        $this->returnsIds = $condition;

        return $this;
    }

    public function saveAsId(bool | Closure $condition = true): static
    {
        return $this->returnIds($condition);
    }

    public function returnsIds(): bool
    {
        return (bool) $this->evaluate($this->returnsIds);
    }
}
