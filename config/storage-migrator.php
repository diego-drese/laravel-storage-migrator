<?php

return [
    'source' => [
        'driver' => 's3',
        'key' => env('STORAGE_MIGRATOR_SOURCE_KEY'),
        'secret' => env('STORAGE_MIGRATOR_SOURCE_SECRET'),
        'region' => env('STORAGE_MIGRATOR_SOURCE_REGION'),
        'bucket' => env('STORAGE_MIGRATOR_SOURCE_BUCKET'),
        'endpoint' => env('STORAGE_MIGRATOR_SOURCE_ENDPOINT'),
        'use_path_style_endpoint' => env('STORAGE_MIGRATOR_SOURCE_PATH_STYLE', false),
    ],

    'target' => [
        'driver' => 's3',
        'key' => env('STORAGE_MIGRATOR_TARGET_KEY'),
        'secret' => env('STORAGE_MIGRATOR_TARGET_SECRET'),
        'region' => env('STORAGE_MIGRATOR_TARGET_REGION'),
        'bucket' => env('STORAGE_MIGRATOR_TARGET_BUCKET'),
        'endpoint' => env('STORAGE_MIGRATOR_TARGET_ENDPOINT'),
        'use_path_style_endpoint' => env('STORAGE_MIGRATOR_TARGET_PATH_STYLE', false),
    ],

    'replace' => false,
];

