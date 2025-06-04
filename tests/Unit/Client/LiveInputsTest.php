<?php

namespace SentFlying\CloudflareStreamLaravel\Tests\Unit\Client;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Mockery;
use PHPUnit\Framework\TestCase;
use SentFlying\CloudflareStreamLaravel\Client;

class LiveInputsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_list_live_inputs()
    {
        // Mock dependencies
        $httpFactory = Mockery::mock(Factory::class);
        $pendingRequest = Mockery::mock(PendingRequest::class);
        $response = Mockery::mock(Response::class);
        
        // Mock config
        $config = [
            'auth_type' => 'token',
            'api_token' => 'test-token',
            'account_id' => 'test-account',
            'base_url' => 'https://api.test.com',
            'timeout' => 30,
        ];
        
        // Mock expected response data
        $responseData = [
            'success' => true,
            'result' => [
                [
                    'uid' => 'live-input-1',
                    'rtmps' => ['url' => 'rtmps://live.cloudflare.com/live/'],
                    'meta' => ['name' => 'Test Live Input 1'],
                ],
                [
                    'uid' => 'live-input-2',
                    'rtmps' => ['url' => 'rtmps://live.cloudflare.com/live/'],
                    'meta' => ['name' => 'Test Live Input 2'],
                ],
            ],
        ];
        
        // Set up mocks
        $httpFactory->shouldReceive('withHeaders')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('timeout')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('get')->once()->with('https://api.test.com/accounts/test-account/stream/live_inputs')->andReturn($response);
        
        $response->shouldReceive('failed')->once()->andReturn(false);
        $response->shouldReceive('json')->with('success')->andReturn(true);
        $response->shouldReceive('json')->with('result')->andReturn($responseData['result']);
        
        // Create client and call method
        $client = new Client($httpFactory, $config);
        $result = $client->listLiveInputs();
        
        // Assert result
        $this->assertEquals($responseData['result'], $result);
    }
    
    public function test_create_live_input()
    {
        // Mock dependencies
        $httpFactory = Mockery::mock(Factory::class);
        $pendingRequest = Mockery::mock(PendingRequest::class);
        $response = Mockery::mock(Response::class);
        
        // Mock config
        $config = [
            'auth_type' => 'token',
            'api_token' => 'test-token',
            'account_id' => 'test-account',
            'base_url' => 'https://api.test.com',
            'timeout' => 30,
        ];
        
        // Test data
        $meta = ['name' => 'Test Live Input'];
        $recording = [
            'mode' => 'automatic',
            'timeoutSeconds' => 60,
            'requireSignedURLs' => false,
            'allowedOrigins' => ['*.example.com']
        ];
        $customUid = 'custom-live-input-uid';
        $deleteRecordingAfterDays = 30;
        
        // Expected request data
        $expectedRequestData = [
            'meta' => $meta,
            'recording' => $recording,
            'uid' => $customUid,
            'deleteRecordingAfterDays' => $deleteRecordingAfterDays
        ];
        
        // Mock expected response data
        $responseData = [
            'success' => true,
            'result' => [
                'uid' => $customUid,
                'rtmps' => ['url' => 'rtmps://live.cloudflare.com/live/'],
                'meta' => $meta,
                'recording' => $recording,
                'deleteRecordingAfterDays' => $deleteRecordingAfterDays
            ],
        ];
        
        // Set up mocks
        $httpFactory->shouldReceive('withHeaders')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('timeout')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('post')
            ->once()
            ->with('https://api.test.com/accounts/test-account/stream/live_inputs', $expectedRequestData)
            ->andReturn($response);
        
        $response->shouldReceive('failed')->once()->andReturn(false);
        $response->shouldReceive('json')->with('success')->andReturn(true);
        $response->shouldReceive('json')->with('result')->andReturn($responseData['result']);
        
        // Create client and call method
        $client = new Client($httpFactory, $config);
        $result = $client->createLiveInput($meta, $recording, $customUid, $deleteRecordingAfterDays);
        
        // Assert result
        $this->assertEquals($responseData['result'], $result);
    }
    
    public function test_get_live_input()
    {
        // Mock dependencies
        $httpFactory = Mockery::mock(Factory::class);
        $pendingRequest = Mockery::mock(PendingRequest::class);
        $response = Mockery::mock(Response::class);
        
        // Mock config
        $config = [
            'auth_type' => 'token',
            'api_token' => 'test-token',
            'account_id' => 'test-account',
            'base_url' => 'https://api.test.com',
            'timeout' => 30,
        ];
        
        // Test data
        $liveInputId = 'live-input-123';
        
        // Mock expected response data
        $responseData = [
            'success' => true,
            'result' => [
                'uid' => $liveInputId,
                'rtmps' => ['url' => 'rtmps://live.cloudflare.com/live/'],
                'meta' => ['name' => 'Test Live Input'],
                'created' => '2023-01-01T00:00:00Z',
                'modified' => '2023-01-01T00:00:00Z',
            ],
        ];
        
        // Set up mocks
        $httpFactory->shouldReceive('withHeaders')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('timeout')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('get')
            ->once()
            ->with("https://api.test.com/accounts/test-account/stream/live_inputs/{$liveInputId}")
            ->andReturn($response);
        
        $response->shouldReceive('failed')->once()->andReturn(false);
        $response->shouldReceive('json')->with('success')->andReturn(true);
        $response->shouldReceive('json')->with('result')->andReturn($responseData['result']);
        
        // Create client and call method
        $client = new Client($httpFactory, $config);
        $result = $client->getLiveInput($liveInputId);
        
        // Assert result
        $this->assertEquals($responseData['result'], $result);
    }
    
    public function test_update_live_input()
    {
        // Mock dependencies
        $httpFactory = Mockery::mock(Factory::class);
        $pendingRequest = Mockery::mock(PendingRequest::class);
        $response = Mockery::mock(Response::class);
        
        // Mock config
        $config = [
            'auth_type' => 'token',
            'api_token' => 'test-token',
            'account_id' => 'test-account',
            'base_url' => 'https://api.test.com',
            'timeout' => 30,
        ];
        
        // Test data
        $liveInputId = 'live-input-123';
        $meta = ['name' => 'Updated Live Input'];
        $recording = [
            'mode' => 'manual',
            'timeoutSeconds' => 120,
        ];
        $deleteRecordingAfterDays = 60;
        
        // Expected request data
        $expectedRequestData = [
            'meta' => $meta,
            'recording' => $recording,
            'deleteRecordingAfterDays' => $deleteRecordingAfterDays
        ];
        
        // Mock expected response data
        $responseData = [
            'success' => true,
            'result' => [
                'uid' => $liveInputId,
                'rtmps' => ['url' => 'rtmps://live.cloudflare.com/live/'],
                'meta' => $meta,
                'recording' => $recording,
                'deleteRecordingAfterDays' => $deleteRecordingAfterDays,
                'modified' => '2023-01-02T00:00:00Z',
            ],
        ];
        
        // Set up mocks
        $httpFactory->shouldReceive('withHeaders')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('timeout')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('put')
            ->once()
            ->with("https://api.test.com/accounts/test-account/stream/live_inputs/{$liveInputId}", $expectedRequestData)
            ->andReturn($response);
        
        $response->shouldReceive('failed')->once()->andReturn(false);
        $response->shouldReceive('json')->with('success')->andReturn(true);
        $response->shouldReceive('json')->with('result')->andReturn($responseData['result']);
        
        // Create client and call method
        $client = new Client($httpFactory, $config);
        $result = $client->updateLiveInput($liveInputId, $meta, $recording, $deleteRecordingAfterDays);
        
        // Assert result
        $this->assertEquals($responseData['result'], $result);
    }
    
    public function test_delete_live_input()
    {
        // Mock dependencies
        $httpFactory = Mockery::mock(Factory::class);
        $pendingRequest = Mockery::mock(PendingRequest::class);
        $response = Mockery::mock(Response::class);
        
        // Mock config
        $config = [
            'auth_type' => 'token',
            'api_token' => 'test-token',
            'account_id' => 'test-account',
            'base_url' => 'https://api.test.com',
            'timeout' => 30,
        ];
        
        // Test data
        $liveInputId = 'live-input-123';
        
        // Mock expected response data
        $responseData = [
            'success' => true,
            'result' => [],
        ];
        
        // Set up mocks
        $httpFactory->shouldReceive('withHeaders')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('timeout')->once()->andReturn($pendingRequest);
        $pendingRequest->shouldReceive('delete')
            ->once()
            ->with("https://api.test.com/accounts/test-account/stream/live_inputs/{$liveInputId}")
            ->andReturn($response);
        
        $response->shouldReceive('failed')->once()->andReturn(false);
        $response->shouldReceive('json')->with('success')->andReturn(true);
        
        // Create client and call method
        $client = new Client($httpFactory, $config);
        $result = $client->deleteLiveInput($liveInputId);
        
        // Assert result
        $this->assertTrue($result);
    }
}
