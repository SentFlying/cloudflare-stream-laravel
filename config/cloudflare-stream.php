<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudflare Authentication
    |--------------------------------------------------------------------------
    |
    | Configure your Cloudflare API authentication. You can use either:
    | - 'token': A Cloudflare API token (recommended)
    | - 'key': Cloudflare API key and email combination
    |
    */
    'auth_type' => env('CLOUDFLARE_AUTH_TYPE', 'token'),
    
    // API Token (recommended auth method)
    'api_token' => env('CLOUDFLARE_API_TOKEN'),
    
    // API Key and Email (alternative auth method)
    'api_key' => env('CLOUDFLARE_API_KEY'),
    'email' => env('CLOUDFLARE_EMAIL'),
    
    /*
    |--------------------------------------------------------------------------
    | Cloudflare Account Identifier
    |--------------------------------------------------------------------------
    |
    | Your Cloudflare account identifier, required for all API requests.
    |
    */
    'account_id' => env('CLOUDFLARE_ACCOUNT_ID'),
    
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Base URL and request timeout settings for the Cloudflare API.
    |
    */
    'base_url' => env('CLOUDFLARE_API_BASE_URL', 'https://api.cloudflare.com/client/v4'),
    'timeout' => env('CLOUDFLARE_API_TIMEOUT', 30),
];
