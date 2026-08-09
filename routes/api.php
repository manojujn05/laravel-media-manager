<?php

use Illuminate\Support\Facades\Route;
use Innopanda\AssetManager\Http\Controllers\Api\AssetApiController;

Route::prefix(config('asset-manager.api.prefix', 'api/asset-manager'))
    ->middleware(config('asset-manager.api.middleware', ['api']))
    ->group(function () {
        Route::get('/assets', [AssetApiController::class, 'index']);
        Route::post('/assets/upload', [AssetApiController::class, 'upload']);
        Route::delete('/assets', [AssetApiController::class, 'destroy']);
        Route::post('/assets/move', [AssetApiController::class, 'move']);
    });