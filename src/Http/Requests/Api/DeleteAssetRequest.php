<?php

namespace Innopanda\AssetManager\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAssetRequest extends FormRequest
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
            'force' => 'nullable|boolean',
        ];
    }
}