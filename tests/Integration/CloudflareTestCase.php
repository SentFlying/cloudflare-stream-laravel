<?php

namespace SentFlying\CloudflareStreamLaravel\Tests\Integration;

use Dotenv\Dotenv;
use SentFlying\CloudflareStreamLaravel\Tests\TestCase;

abstract class CloudflareTestCase extends TestCase
{
    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        // Load .env.testing file if it exists
        $this->loadTestingEnvironment();

        parent::setUp();

        // Set up real Cloudflare credentials from environment variables
        $this->app['config']->set('cloudflare-stream.auth_type', env('CLOUDFLARE_TEST_AUTH_TYPE', 'token'));
        $this->app['config']->set('cloudflare-stream.api_token', env('CLOUDFLARE_TEST_API_TOKEN'));
        $this->app['config']->set('cloudflare-stream.api_key', env('CLOUDFLARE_TEST_API_KEY'));
        $this->app['config']->set('cloudflare-stream.email', env('CLOUDFLARE_TEST_EMAIL'));
        $this->app['config']->set('cloudflare-stream.account_id', env('CLOUDFLARE_TEST_ACCOUNT_ID'));
        $this->app['config']->set('cloudflare-stream.base_url', env('CLOUDFLARE_TEST_BASE_URL', 'https://api.cloudflare.com/client/v4'));
        $this->app['config']->set('cloudflare-stream.timeout', env('CLOUDFLARE_TEST_TIMEOUT', 30));
    }

    /**
     * Load the .env.testing file if it exists.
     */
    protected function loadTestingEnvironment(): void
    {
        $envTestingPath = __DIR__ . '/../../.env.testing';
        
        if (file_exists($envTestingPath)) {
            $dotenv = Dotenv::createImmutable(dirname($envTestingPath), '.env.testing');
            $dotenv->load();
        }
    }

}
