<?php

return [

    'disk' => env('ASSET_MANAGER_DISK', 'public'),

    'max_upload_size' => 10240,

    'allowed_mime_types' => [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/svg+xml',
        'application/pdf',
    ],

    'usage_models' => [
        'Workouts'    => \App\Models\Workout::class,
        'Memberships' => \App\Models\Membership::class,
        'Exercises'   => \App\Models\Exercise::class,
        'Products'    => \App\Models\Product::class, 
    ],

    'thumbnails' => [

        'small' => [
            'width' => 150,
            'height' => 150,
        ],

        'medium' => [
            'width' => 400,
            'height' => 400,
        ],

        'large' => [
            'width' => 800,
            'height' => 800,
        ],
    ],

    'chunk_upload' => true,

    'duplicate_detection' => true,

    'favorites' => true,

    'folders' => true,

    'activity_log' => true,

    'version_history' => true,

    /*
    |--------------------------------------------------------------------------
    | REST API Configuration (Phase 13)
    |--------------------------------------------------------------------------
    */
    'api' => [
        'prefix' => 'api/asset-manager',

        /*
        | API Middlewares:
        | For standard Laravel: ['api', 'auth:sanctum']
        | For Filament Users:  ['api', \Filament\Http\Middleware\Authenticate::class]
        */
        'middleware' => [
            'api',
            // 'auth:sanctum',
        ],
    ],

];