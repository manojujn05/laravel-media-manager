<?php

namespace Innopanda\AssetManager\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TagAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_ids' => 'required|array|min:1',
            'asset_ids.*' => 'exists:assets,id',
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:asset_tags,id',
            'action' => 'nullable|in:attach,detach,sync',
        ];
    }
}