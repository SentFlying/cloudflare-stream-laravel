<?php

namespace SentFlying\CloudflareStreamLaravel\Tests\Unit\Facades;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Mockery;
use SentFlying\CloudflareStreamLaravel\Client;
use SentFlying\CloudflareStreamLaravel\Facades\Stream;
use SentFlying\CloudflareStreamLaravel\Tests\TestCase;

class StreamTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_facade_calls_underlying_client_method()
    {
        // Create a mock client
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('listLiveInputs')
            ->once()
            ->andReturn(['test' => 'data']);
        
        // Replace the client in the container with our mock
        $this->app->instance(Client::class, $mockClient);
        
        // Call the facade method
        $result = Stream::listLiveInputs();
        
        // Assert the result is what we expect
        $this->assertEquals(['test' => 'data'], $result);
    }
}
