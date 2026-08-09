<?php

namespace Innopanda\AssetManager\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UploadAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxSize = config('asset-manager.max_upload_size', 10240);
        $allowedTypes = implode(',', config('asset-manager.allowed_mime_types', []));

        $fileRules = ['required', 'file', "max:{$maxSize}"];
        if (!empty($allowedTypes)) {
            $fileRules[] = "mimetypes:{$allowedTypes}";
        }

        return [
            'file' => $fileRules,
            'folder_id' => 'nullable|exists:asset_folders,id',
            'title' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
        ];
    }
}