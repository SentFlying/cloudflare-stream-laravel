# Cloudflare Stream Live Inputs for Laravel

> **⚠️ Pre-Release Software**: This package is currently in alpha/beta development. The API may change before the stable 1.0 release. Use in production at your own risk.

A Laravel package that provides a clean, fluent interface to the Cloudflare Stream Live Inputs API, specifically focused on managing Live Inputs.

## Features

- Simple, fluent interface to Cloudflare Stream Live Inputs API
- Support for both API Token and API Key authentication methods
- Comprehensive error handling with specific exception types
- Laravel Facade for convenient static access
- Full test coverage including integration tests
- Compatible with Laravel 11.0+ and 12.0+

## Requirements

- PHP 8.1+
- Laravel 11.0+ or 12.0+
- Cloudflare account with Stream service enabled

## Installation

You can install the package via Composer:

```bash
composer require sent-flying/cloudflare-stream-laravel
```

The package will automatically register its service provider if you're using Laravel's package auto-discovery.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="cloudflare-stream-config"
```

This will create a `config/cloudflare-stream.php` file in your application. You should configure your Cloudflare API credentials in your `.env` file:

```
# Required configuration
CLOUDFLARE_ACCOUNT_ID=your-account-id

# Token authentication (recommended)
CLOUDFLARE_AUTH_TYPE=token
CLOUDFLARE_API_TOKEN=your-api-token

# OR Key authentication (alternative)
# CLOUDFLARE_AUTH_TYPE=key
# CLOUDFLARE_API_KEY=your-api-key
# CLOUDFLARE_EMAIL=your-email@example.com

# Optional configuration
# CLOUDFLARE_API_BASE_URL=https://api.cloudflare.com/client/v4
# CLOUDFLARE_API_TIMEOUT=30
```

## Usage

### Available Methods

The package provides the following methods for managing Live Inputs:

| Method | Description |
|--------|-------------|
| `listLiveInputs()` | List all Live Inputs |
| `createLiveInput($meta, $recording, $uid, $deleteRecordingAfterDays)` | Create a new Live Input |
| `getLiveInput($liveInputId)` | Get a specific Live Input by ID |
| `updateLiveInput($liveInputId, $meta, $recording, $deleteRecordingAfterDays)` | Update a Live Input |
| `deleteLiveInput($liveInputId)` | Delete a Live Input |

### Using the Facade

```php
use SentFlying\CloudflareStreamLaravel\Facades\Stream;

// List all Live Inputs
$liveInputs = Stream::listLiveInputs();

// Create a new Live Input
$meta = ['name' => 'My Live Stream'];
$recording = [
    'mode' => 'automatic',
    'timeoutSeconds' => 60,
    'requireSignedURLs' => false,
    'allowedOrigins' => ['*.example.com']
];
$liveInput = Stream::createLiveInput($meta, $recording);

// Get a specific Live Input
$liveInput = Stream::getLiveInput('live-input-id');

// Update a Live Input
$updatedMeta = ['name' => 'Updated Live Stream'];
$updatedLiveInput = Stream::updateLiveInput('live-input-id', $updatedMeta);

// Delete a Live Input
$result = Stream::deleteLiveInput('live-input-id');
```

### Using Dependency Injection

```php
use SentFlying\CloudflareStreamLaravel\Client;

class LiveStreamController extends Controller
{
    protected $streamClient;
    
    public function __construct(Client $streamClient)
    {
        $this->streamClient = $streamClient;
    }
    
    public function index()
    {
        $liveInputs = $this->streamClient->listLiveInputs();
        
        return view('live-streams.index', compact('liveInputs'));
    }
    
    public function store(Request $request)
    {
        $meta = ['name' => $request->input('name')];
        $recording = [
            'mode' => $request->input('recording_mode', 'automatic'),
            'timeoutSeconds' => $request->input('timeout', 60),
        ];
        
        $liveInput = $this->streamClient->createLiveInput($meta, $recording);
        
        return redirect()->route('live-streams.show', $liveInput['uid']);
    }
}
```

## Error Handling

The package throws specific exceptions for different types of errors:

| Exception | HTTP Status | Description |
|-----------|-------------|-------------|
| `AuthenticationException` | 401, 403 | Authentication or authorization errors |
| `ValidationException` | 400, 422 | Validation errors in request data |
| `NotFoundException` | 404 | Resource not found |
| `CloudflareStreamApiException` | Any | Base exception for all other errors |

All exceptions include the original error message from Cloudflare and provide access to the full error details via the `getErrors()` method.

Example:

```php
use SentFlying\CloudflareStreamLaravel\Exceptions\AuthenticationException;
use SentFlying\CloudflareStreamLaravel\Exceptions\NotFoundException;
use SentFlying\CloudflareStreamLaravel\Exceptions\ValidationException;
use SentFlying\CloudflareStreamLaravel\Facades\Stream;

try {
    $liveInput = Stream::getLiveInput('non-existent-id');
} catch (NotFoundException $e) {
    // Handle not found error
    return response()->json([
        'error' => 'Live input not found', 
        'message' => $e->getMessage(),
        'details' => $e->getErrors()
    ], 404);
} catch (AuthenticationException $e) {
    // Handle authentication error
    return response()->json(['error' => 'Authentication failed'], 401);
} catch (ValidationException $e) {
    // Handle validation error
    return response()->json(['error' => 'Validation failed', 'details' => $e->getErrors()], 422);
} catch (\Exception $e) {
    // Handle other errors
    return response()->json(['error' => 'An error occurred'], 500);
}
```

## Testing

The package includes both unit tests and integration tests.

### Unit Tests

Run the unit tests with:

```bash
composer test
# or more specifically
composer test:unit
```

### Feature Tests

Run the feature tests with:

```bash
composer test:feature
```

### Integration Tests

The package includes integration tests that make real API calls to Cloudflare.

To run the integration tests:

1. Create a `.env.testing` file in the root of your package with your Cloudflare test account credentials:

```
CLOUDFLARE_TEST_AUTH_TYPE=token
CLOUDFLARE_TEST_API_TOKEN=your_test_api_token
CLOUDFLARE_TEST_ACCOUNT_ID=your_test_account_id
```

2. Run the integration tests:

```bash
composer test:integration
```

To run all tests (unit, feature, and integration):

```bash
composer test
```

> **Warning**: Integration tests will create, update, and delete real Live Inputs in your Cloudflare account. Always use a test account or a sandbox environment.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

