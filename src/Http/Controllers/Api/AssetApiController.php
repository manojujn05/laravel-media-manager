<?php

namespace Innopanda\AssetManager\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Innopanda\AssetManager\Models\Asset;
use Innopanda\AssetManager\Http\Resources\AssetResource;
use Innopanda\AssetManager\Http\Requests\Api\UploadAssetRequest;
use Innopanda\AssetManager\Http\Requests\Api\MoveAssetRequest;
use Innopanda\AssetManager\Http\Requests\Api\TagAssetRequest;
use Innopanda\AssetManager\Http\Requests\Api\DeleteAssetRequest;

class AssetApiController extends Controller
{
    /**
     * GET /assets
     * Fetch asset collection with pagination and search/filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Asset::with(['folder']);

        if ($request->has('folder_id')) {
            $query->where('folder_id', $request->query('folder_id'));
        }

        if ($search = $request->query('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($type = $request->query('type')) {
            $query->where('mime_type', 'like', "{$type}%");
        }

        $perPage = (int) $request->query('per_page', 15);
        $assets = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => AssetResource::collection($assets),
            'meta' => [
                'current_page' => $assets->currentPage(),
                'last_page' => $assets->lastPage(),
                'per_page' => $assets->perPage(),
                'total' => $assets->total(),
            ],
        ]);
    }

  
    public function upload(UploadAssetRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $disk = config('asset-manager.disk', 'public');

        // Store file safely and get relative path (e.g., assets/filename.png)
        $path = $file->store('assets', $disk);

        $asset = Asset::create([
            'title' => $request->input('title') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'alt_text' => $request->input('alt_text'),
            'filename' => $file->getClientOriginalName(),
            'path' => $path, // Ensures only relative storage path is saved
            'disk' => $disk,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'folder_id' => $request->input('folder_id'),
        ]);



        return response()->json([
            'success' => true,
            'message' => 'Asset uploaded successfully.',
            'data' => new AssetResource($asset->load(['folder'])),
        ], 201);
    }

    /**
     * DELETE /assets
     * Soft or force delete selected assets.
     */
    public function destroy(DeleteAssetRequest $request): JsonResponse
    {
        $assetIds = $request->input('asset_ids');
        $force = $request->boolean('force', false);

        $assets = Asset::whereIn('id', $assetIds)->get();

        foreach ($assets as $asset) {
            if ($force) {
                Storage::disk($asset->disk)->delete($asset->path);
                $asset->forceDelete();
            } else {
                $asset->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($assets) . ' asset(s) deleted successfully.',
        ]);
    }

    /**
     * POST /assets/move
     * Move assets to a specified folder.
     */
    public function move(MoveAssetRequest $request): JsonResponse
    {
        $assetIds = $request->input('asset_ids');
        $folderId = $request->input('folder_id');

        Asset::whereIn('id', $assetIds)->update([
            'folder_id' => $folderId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assets moved successfully.',
        ]);
    }


}