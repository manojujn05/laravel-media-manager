<?php

return [

    'version' => '1.0.0',

    'disk' => env('ASSET_MANAGER_DISK', 'public'),

    'sync' => [
        'root_path' => env('ASSET_MANAGER_ROOT', '/'),
        'private_urls' => env('ASSET_MANAGER_PRIVATE_URLS', false),
        'temporary_url_expiration' => (int) env('ASSET_MANAGER_TEMP_URL_EXPIRES', 60),
    ],

    'uploads' => [
        'max_size' => 10240, // KB
        
        'allowed_mimes' => [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
            'image/svg+xml',
            'application/pdf',
        ],

        'allowed_extensions' => [
            'jpg',
            'jpeg',
            'png',
            'webp',
            'gif',
            'svg',
            'pdf',
        ],
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

    'duplicates' => [
        'enabled' => true,
        'behavior' => 'warn', // warn, reject, allow
    ],

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