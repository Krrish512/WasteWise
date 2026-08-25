<?php

/*
|--------------------------------------------------------------------------
| Cross-Origin Resource Sharing (CORS) Configuration
|--------------------------------------------------------------------------
|
| This config controls which cross-origin requests Laravel will allow.
| supports_credentials must be true so the browser sends the Laravel
| session cookie with fetch() calls from the same site.
|
| IMPORTANT: 'allowed_origins' must NOT use '*' (wildcard) when
| supports_credentials is true — browsers reject that combination.
| List the exact origins the app is served from instead.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Paths that CORS headers should be applied to
    |--------------------------------------------------------------------------
    */
    'paths' => ['api/*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed HTTP methods
    |--------------------------------------------------------------------------
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed origins
    |--------------------------------------------------------------------------
    | Must be explicit (no wildcard) when supports_credentials is true.
    | List every origin the frontend may be served from during development.
    */
    'allowed_origins' => [
        'http://localhost',
        'http://localhost:8000',
        'http://127.0.0.1',
        'http://127.0.0.1:8000',
    ],

    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed request headers
    |--------------------------------------------------------------------------
    */
    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed headers and max age (seconds to cache preflight)
    |--------------------------------------------------------------------------
    */
    'exposed_headers' => [],

    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports credentials (cookies, Authorization headers)
    |--------------------------------------------------------------------------
    | Must be true so the browser includes the Laravel session cookie in
    | fetch() calls to /api/waste/search, allowing auth()->check() to work.
    */
    'supports_credentials' => true,

];

