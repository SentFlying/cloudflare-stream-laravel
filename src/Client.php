<?php

namespace SentFlying\CloudflareStreamLaravel;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response;
use InvalidArgumentException;
use SentFlying\CloudflareStreamLaravel\Exceptions\AuthenticationException;
use SentFlying\CloudflareStreamLaravel\Exceptions\CloudflareStreamApiException;
use SentFlying\CloudflareStreamLaravel\Exceptions\NotFoundException;
use SentFlying\CloudflareStreamLaravel\Exceptions\ValidationException;

class Client
{
    /**
     * The HTTP client factory instance.
     */
    protected Factory $http;
    
    /**
     * The configuration array.
     */
    protected array $config;
    
    /**
     * The base URL for API requests.
     */
    protected string $baseUrl;
    
    /**
     * The account ID for API requests.
     */
    protected string $accountId;
    
    /**
     * The request timeout in seconds.
     */
    protected int $timeout;
    
    /**
     * The authentication headers.
     */
    protected array $authHeaders = [];
    
    /**
     * Create a new Cloudflare Stream client instance.
     *
     * @param Factory $http The HTTP client factory
     * @param array $config The configuration array
     * @throws InvalidArgumentException If required configuration is missing
     */
    public function __construct(Factory $http, array $config)
    {
        $this->http = $http;
        $this->config = $config;
        
        $this->validateConfig();
        $this->setupClient();
    }
    
    /**
     * Validate the configuration.
     *
     * @throws InvalidArgumentException If required configuration is missing
     */
    protected function validateConfig(): void
    {
        if (!isset($this->config['account_id']) || empty($this->config['account_id'])) {
            throw new InvalidArgumentException('Account ID is required');
        }
        
        $authType = $this->config['auth_type'] ?? 'token';
        
        if ($authType === 'token') {
            if (!isset($this->config['api_token']) || empty($this->config['api_token'])) {
                throw new InvalidArgumentException('API token is required when using token authentication');
            }
        } elseif ($authType === 'key') {
            if (!isset($this->config['api_key']) || empty($this->config['api_key']) || 
                !isset($this->config['email']) || empty($this->config['email'])) {
                throw new InvalidArgumentException('API key and email are required when using key authentication');
            }
        } else {
            throw new InvalidArgumentException('Invalid authentication type. Must be "token" or "key"');
        }
    }
    
    /**
     * Set up the client with configuration values.
     */
    protected function setupClient(): void
    {
        $this->baseUrl = $this->config['base_url'] ?? 'https://api.cloudflare.com/client/v4';
        $this->accountId = $this->config['account_id'];
        $this->timeout = $this->config['timeout'] ?? 30;
        
        $authType = $this->config['auth_type'] ?? 'token';
        
        if ($authType === 'token') {
            $this->authHeaders = [
                'Authorization' => 'Bearer ' . $this->config['api_token'],
            ];
        } else {
            $this->authHeaders = [
                'X-Auth-Email' => $this->config['email'],
                'X-Auth-Key' => $this->config['api_key'],
            ];
        }
    }
    
    /**
     * Make a request to the Cloudflare API.
     *
     * @param string $method The HTTP method (GET, POST, PUT, DELETE)
     * @param string $endpoint The API endpoint path
     * @param array $data Optional request data
     * @return Response The HTTP response
     * @throws CloudflareStreamApiException If the request fails
     */
    public function makeRequest(string $method, string $endpoint, array $data = []): Response
    {
        $url = $this->baseUrl . '/accounts/' . $this->accountId . '/stream/' . $endpoint;
        
        $headers = array_merge([
            'Accept' => 'application/json',
        ], $this->authHeaders);
        
        if (in_array($method, ['POST', 'PUT']) && !empty($data)) {
            $headers['Content-Type'] = 'application/json';
        }
        
        $request = $this->http->withHeaders($headers)->timeout($this->timeout);
        
        $response = match (strtoupper($method)) {
            'GET' => $request->get($url),
            'POST' => $request->post($url, $data),
            'PUT' => $request->put($url, $data),
            'DELETE' => $request->delete($url),
            default => throw new InvalidArgumentException("Unsupported HTTP method: {$method}"),
        };
        
        if ($response->failed()) {
            $this->handleRequestError($response);
        }
        
        // Check if the response indicates success
        if ($response->json('success') === false) {
            throw new CloudflareStreamApiException(
                'The request was not successful',
                $response->status(),
                $response->json('errors') ?? []
            );
        }
        
        return $response;
    }
    
    /**
     * Handle request errors by throwing appropriate exceptions.
     *
     * @param Response $response The HTTP response
     * @throws CloudflareStreamApiException The appropriate exception based on the error
     */
    protected function handleRequestError(Response $response): void
    {
        $status = $response->status();
        $errors = $response->json('errors') ?? [];
        $errorMessage = count($errors) > 0 ? $errors[0]['message'] ?? 'Unknown error' : 'Unknown error';
        
        throw match (true) {
            $status === 401 || $status === 403 => new AuthenticationException($errorMessage, $status, $errors),
            $status === 404 => new NotFoundException($errorMessage, $status, $errors),
            $status === 400 || $status === 422 => new ValidationException($errorMessage, $status, $errors),
            default => new CloudflareStreamApiException($errorMessage, $status, $errors),
        };
    }
    
    /**
     * List all Live Inputs.
     *
     * @return array The list of Live Inputs
     * @throws CloudflareStreamApiException If the request fails
     */
    public function listLiveInputs(): array
    {
        $response = $this->makeRequest('GET', 'live_inputs');
        return $response->json('result');
    }
    
    /**
     * Create a new Live Input.
     *
     * @param array $meta Metadata for the Live Input
     * @param array|null $recording Recording configuration
     * @param string|null $uid Custom UID for the Live Input
     * @param int|null $deleteRecordingAfterDays Number of days after which to delete the recording
     * @return array The created Live Input
     * @throws CloudflareStreamApiException If the request fails
     */
    public function createLiveInput(
        array $meta,
        ?array $recording = null,
        ?string $uid = null,
        ?int $deleteRecordingAfterDays = null
    ): array {
        $data = ['meta' => $meta];
        
        if ($recording !== null) {
            $data['recording'] = $recording;
        }
        
        if ($uid !== null) {
            $data['uid'] = $uid;
        }
        
        if ($deleteRecordingAfterDays !== null) {
            $data['deleteRecordingAfterDays'] = $deleteRecordingAfterDays;
        }
        
        $response = $this->makeRequest('POST', 'live_inputs', $data);
        return $response->json('result');
    }
    
    /**
     * Get a specific Live Input by ID.
     *
     * @param string $liveInputId The ID of the Live Input to retrieve
     * @return array The Live Input details
     * @throws CloudflareStreamApiException If the request fails
     */
    public function getLiveInput(string $liveInputId): array
    {
        $response = $this->makeRequest('GET', "live_inputs/{$liveInputId}");
        return $response->json('result');
    }
    
    /**
     * Update a Live Input.
     *
     * @param string $liveInputId The ID of the Live Input to update
     * @param array|null $meta Updated metadata for the Live Input
     * @param array|null $recording Updated recording configuration
     * @param int|null $deleteRecordingAfterDays Updated number of days after which to delete the recording
     * @return array The updated Live Input
     * @throws CloudflareStreamApiException If the request fails
     */
    public function updateLiveInput(
        string $liveInputId,
        ?array $meta = null,
        ?array $recording = null,
        ?int $deleteRecordingAfterDays = null
    ): array {
        $data = [];
        
        if ($meta !== null) {
            $data['meta'] = $meta;
        }
        
        if ($recording !== null) {
            $data['recording'] = $recording;
        }
        
        if ($deleteRecordingAfterDays !== null) {
            $data['deleteRecordingAfterDays'] = $deleteRecordingAfterDays;
        }
        
        $response = $this->makeRequest('PUT', "live_inputs/{$liveInputId}", $data);
        return $response->json('result');
    }
    
    /**
     * Delete a Live Input.
     *
     * @param string $liveInputId The ID of the Live Input to delete
     * @return bool True if the deletion was successful
     * @throws CloudflareStreamApiException If the request fails
     */
    public function deleteLiveInput(string $liveInputId): bool
    {
        $this->makeRequest('DELETE', "live_inputs/{$liveInputId}");
        return true;
    }
}
