<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'auth/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://adeguatiassettiimpresa.it',
        'https://www.adeguatiassettiimpresa.it',
        'http://localhost:3000',
        'http://localhost:3001',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
