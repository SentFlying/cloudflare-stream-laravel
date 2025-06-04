<?php

namespace SentFlying\CloudflareStreamLaravel\Tests\Unit;

use SentFlying\CloudflareStreamLaravel\Tests\TestCase;

class ConfigTest extends TestCase
{
    public function test_it_can_load_configuration()
    {
        $this->assertEquals('token', config('cloudflare-stream.auth_type'));
        $this->assertEquals('https://api.cloudflare.com/client/v4', config('cloudflare-stream.base_url'));
        $this->assertEquals(30, config('cloudflare-stream.timeout'));
    }
    
    public function test_it_can_override_configuration_with_env_variables()
    {
        $this->app['config']->set('cloudflare-stream.auth_type', 'key');
        $this->app['config']->set('cloudflare-stream.base_url', 'https://custom-api.cloudflare.com/client/v4');
        $this->app['config']->set('cloudflare-stream.timeout', 60);
        
        $this->assertEquals('key', config('cloudflare-stream.auth_type'));
        $this->assertEquals('https://custom-api.cloudflare.com/client/v4', config('cloudflare-stream.base_url'));
        $this->assertEquals(60, config('cloudflare-stream.timeout'));
    }
}
