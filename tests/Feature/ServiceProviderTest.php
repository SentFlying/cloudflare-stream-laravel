<?php

namespace SentFlying\CloudflareStreamLaravel\Tests\Feature;

use SentFlying\CloudflareStreamLaravel\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Set required config values for testing
        $this->app['config']->set('cloudflare-stream.account_id', 'test-account-id');
        $this->app['config']->set('cloudflare-stream.api_token', 'test-token');
    }
    
    public function test_it_loads_config_values()
    {
        $this->assertEquals('token', config('cloudflare-stream.auth_type'));
        $this->assertEquals('test-account-id', config('cloudflare-stream.account_id'));
        $this->assertEquals('https://api.cloudflare.com/client/v4', config('cloudflare-stream.base_url'));
        $this->assertEquals(30, config('cloudflare-stream.timeout'));
    }
}
