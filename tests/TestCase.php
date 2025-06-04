<?php

namespace SentFlying\CloudflareStreamLaravel\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SentFlying\CloudflareStreamLaravel\CloudflareStreamServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            CloudflareStreamServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        
        // Set default test values for Cloudflare Stream config
        $app['config']->set('cloudflare-stream.auth_type', 'token');
        $app['config']->set('cloudflare-stream.api_token', 'test-token');
        $app['config']->set('cloudflare-stream.account_id', 'test-account-id');
        $app['config']->set('cloudflare-stream.base_url', 'https://api.cloudflare.com/client/v4');
        $app['config']->set('cloudflare-stream.timeout', 30);
    }
}
