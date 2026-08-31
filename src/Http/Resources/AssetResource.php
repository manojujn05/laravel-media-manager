<?php

namespace Innopanda\AssetManager\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AssetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'alt_text' => $this->alt,
            'path' => $this->path,
            'url' => $this->getUrl(),
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'formatted_size' => number_format($this->size / 1024, 2) . ' KB',
            'disk' => $this->disk,
            'folder_id' => $this->folder_id,
            'is_favorite' => (bool) $this->is_favorite,
            'folder' => $this->whenLoaded('folder', function () {
                return $this->folder ? [
                    'id' => $this->folder->id,
                    'name' => $this->folder->name,
                ] : null;
            }),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}