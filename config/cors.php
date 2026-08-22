<?php

/*
|--------------------------------------------------------------------------
| Cross-Origin Resource Sharing (CORS) Configuration
|--------------------------------------------------------------------------
|
| This config controls which cross-origin requests Laravel will allow.
| We need this open so the Lovable frontend (hosted on a different domain)
| can call the WasteWise API without CORS errors.
|
| For production, replace '*' in allowed_origins with the exact Lovable
| frontend URL, e.g. 'https://your-app.lovable.app'
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
    | During development, '*' allows all origins.
    | In production, replace with your exact Lovable frontend URL.
    | Example: 'https://your-wastewise-app.lovable.app'
    */
    'allowed_origins' => ['*'],

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
    | Keep false while authentication is not yet implemented.
    */
    'supports_credentials' => false,

];
