<?php

namespace SentFlying\CloudflareStreamLaravel\Tests\Unit;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;
use SentFlying\CloudflareStreamLaravel\Client;
use SentFlying\CloudflareStreamLaravel\Exceptions\AuthenticationException;
use SentFlying\CloudflareStreamLaravel\Exceptions\NotFoundException;
use SentFlying\CloudflareStreamLaravel\Exceptions\ValidationException;

class ClientTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_constructor_with_token_auth()
    {
        $httpFactory = Mockery::mock(Factory::class);
        $config = [
            'auth_type' => 'token',
            'api_token' => 'test-token',
            'account_id' => 'test-account',
            'base_url' => 'https://api.test.com',
            'timeout' => 30,
        ];

        $client = new Client($httpFactory, $config);
        
        $this->assertInstanceOf(Client::class, $client);
    }

    public function test_constructor_with_key_auth()
    {
        $httpFactory = Mockery::mock(Factory::class);
        $config = [
            'auth_type' => 'key',
            'api_key' => 'test-key',
            'email' => 'test@example.com',
            'account_id' => 'test-account',
            'base_url' => 'https://api.test.com',
            'timeout' => 30,
        ];

        $client = new Client($httpFactory, $config);
        
        $this->assertInstanceOf(Client::class, $client);
    }

    public function test_constructor_throws_exception_for_missing_token()
    {
        $httpFactory = Mockery::mock(Factory::class);
        $config = [
            'auth_type' => 'token',
            'account_id' => 'test-account',
            'base_url' => 'https://api.test.com',
            'timeout' => 30,
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('API token is required when using token authentication');
        
        new Client($httpFactory, $config);
    }

    public function test_constructor_throws_exception_for_missing_key_or_email()
    {
        $httpFactory = Mockery::mock(Factory::class);
        $config = [
            'auth_type' => 'key',
            'api_key' => 'test-key',
            'account_id' => 'test-account',
            'base_url' => 'https://api.test.com',
            'timeout' => 30,
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('API key and email are required when using key authentication');
        
        new Client($httpFactory, $config);
    }

    public function test_constructor_throws_exception_for_missing_account_id()
    {
        $httpFactory = Mockery::mock(Factory::class);
        $config = [
            'auth_type' => 'token',
            'api_token' => 'test-token',
            'base_url' => 'https://api.test.com',
            'timeout' => 30,
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Account ID is required');
        
        new Client($httpFactory, $config);
    }

    public function test_make_request_success()
    {
        $httpFactory = Mockery::mock(Factory::class);
        $pendingRequest = Mockery::mock(PendingRequest::class);
        $response = Mockery::mock(Response::class);
        
        $config = [
            'auth_type' => 'token',
            'api_token' => 'test-token',
            'account_id' => 'test-account',
            'base_url' => 'https://api.test.com',
            'timeout' => 30,
        ];
        
        $httpFactory->shouldReceive('withHeaders')->once()->with([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer test-token',
        ])->andReturn($pendingRequest);
        
        $pendingRequest->shouldReceive('timeout')->once()->with(30)->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('get')->once()->with('https://api.test.com/accounts/test-account/stream/test-endpoint')->andReturn($response);
        
        $response->shouldReceive('failed')->once()->andReturn(false);
        $response->shouldReceive('json')->once()->with('success')->andReturn(true);
        
        $client = new Client($httpFactory, $config);
        $result = $client->makeRequest('GET', 'test-endpoint');
        
        $this->assertSame($response, $result);
    }

    public function test_make_request_with_post_data()
    {
        $httpFactory = Mockery::mock(Factory::class);
        $pendingRequest = Mockery::mock(PendingRequest::class);
        $response = Mockery::mock(Response::class);
        
        $config = [
            'auth_type' => 'token',
            'api_token' => 'test-token',
            'account_id' => 'test-account',
            'base_url' => 'https://api.test.com',
            'timeout' => 30,
        ];
        
        $data = ['name' => 'test'];
        
        $httpFactory->shouldReceive('withHeaders')->once()->with([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer test-token',
            'Content-Type' => 'application/json',
        ])->andReturn($pendingRequest);
        
        $pendingRequest->shouldReceive('timeout')->once()->with(30)->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('post')->once()->with('https://api.test.com/accounts/test-account/stream/test-endpoint', $data)->andReturn($response);
        
        $response->shouldReceive('failed')->once()->andReturn(false);
        $response->shouldReceive('json')->once()->with('success')->andReturn(true);
        
        $client = new Client($httpFactory, $config);
        $result = $client->makeRequest('POST', 'test-endpoint', $data);
        
        $this->assertSame($response, $result);
    }

    public function test_make_request_throws_authentication_exception()
    {
        $httpFactory = Mockery::mock(Factory::class);
        $pendingRequest = Mockery::mock(PendingRequest::class);
        $response = Mockery::mock(Response::class);
        
        $config = [
            'auth_type' => 'token',
            'api_token' => 'test-token',
            'account_id' => 'test-account',
            'base_url' => 'https://api.test.com',
            'timeout' => 30,
        ];
        
        $httpFactory->shouldReceive('withHeaders')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('timeout')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('get')->once()->andReturn($response);
        
        $response->shouldReceive('failed')->once()->andReturn(true);
        $response->shouldReceive('status')->once()->andReturn(401);
        $response->shouldReceive('json')->once()->andReturn([
            'errors' => [['message' => 'Authentication failed']],
            'success' => false
        ]);
        
        $this->expectException(AuthenticationException::class);
        
        $client = new Client($httpFactory, $config);
        $client->makeRequest('GET', 'test-endpoint');
    }

    public function test_make_request_throws_not_found_exception()
    {
        $httpFactory = Mockery::mock(Factory::class);
        $pendingRequest = Mockery::mock(PendingRequest::class);
        $response = Mockery::mock(Response::class);
        
        $config = [
            'auth_type' => 'token',
            'api_token' => 'test-token',
            'account_id' => 'test-account',
            'base_url' => 'https://api.test.com',
            'timeout' => 30,
        ];
        
        $httpFactory->shouldReceive('withHeaders')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('timeout')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('get')->once()->andReturn($response);
        
        $response->shouldReceive('failed')->once()->andReturn(true);
        $response->shouldReceive('status')->once()->andReturn(404);
        $response->shouldReceive('json')->once()->andReturn([
            'errors' => [['message' => 'Resource not found']],
            'success' => false
        ]);
        
        $this->expectException(NotFoundException::class);
        
        $client = new Client($httpFactory, $config);
        $client->makeRequest('GET', 'test-endpoint');
    }

    public function test_make_request_throws_validation_exception()
    {
        $httpFactory = Mockery::mock(Factory::class);
        $pendingRequest = Mockery::mock(PendingRequest::class);
        $response = Mockery::mock(Response::class);
        
        $config = [
            'auth_type' => 'token',
            'api_token' => 'test-token',
            'account_id' => 'test-account',
            'base_url' => 'https://api.test.com',
            'timeout' => 30,
        ];
        
        $httpFactory->shouldReceive('withHeaders')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('timeout')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('get')->once()->andReturn($response);
        
        $response->shouldReceive('failed')->once()->andReturn(true);
        $response->shouldReceive('status')->once()->andReturn(422);
        $response->shouldReceive('json')->once()->andReturn([
            'errors' => [['message' => 'Validation failed']],
            'success' => false
        ]);
        
        $this->expectException(ValidationException::class);
        
        $client = new Client($httpFactory, $config);
        $client->makeRequest('GET', 'test-endpoint');
    }
}
