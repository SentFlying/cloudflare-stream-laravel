<?php

namespace SentFlying\CloudflareStreamLaravel\Tests\Integration;

use SentFlying\CloudflareStreamLaravel\Client;
use SentFlying\CloudflareStreamLaravel\Exceptions\CloudflareStreamApiException;

class LiveInputsIntegrationTest extends CloudflareTestCase
{
    /**
     * The Cloudflare Stream client instance.
     */
    protected Client $client;

    /**
     * IDs of resources created during tests that need cleanup.
     */
    protected array $createdLiveInputIds = [];

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->app->make(Client::class);
    }

    /**
     * Clean up any resources created during tests.
     */
    protected function tearDown(): void
    {
        // Delete any live inputs created during tests
        foreach ($this->createdLiveInputIds as $liveInputId) {
            try {
                $this->client->deleteLiveInput($liveInputId);
            } catch (CloudflareStreamApiException $e) {
                // Ignore errors during cleanup
            }
        }

        parent::tearDown();
    }

    public function test_can_list_live_inputs()
    {
        $liveInputs = $this->client->listLiveInputs();
        
        $this->assertIsArray($liveInputs);
        // Even if there are no live inputs, we should get an empty array, not null
        $this->assertNotNull($liveInputs);
    }

    public function test_can_create_and_get_live_input()
    {
        // Create a live input
        $meta = ['name' => 'Test Live Input ' . uniqid()];
        $liveInput = $this->client->createLiveInput($meta);
        
        // Store ID for cleanup
        $this->createdLiveInputIds[] = $liveInput['uid'];
        
        // Verify the live input was created with the correct metadata
        $this->assertArrayHasKey('uid', $liveInput);
        $this->assertArrayHasKey('meta', $liveInput);
        $this->assertEquals($meta['name'], $liveInput['meta']['name']);
        
        // Get the live input and verify it matches
        $retrievedLiveInput = $this->client->getLiveInput($liveInput['uid']);
        $this->assertEquals($liveInput['uid'], $retrievedLiveInput['uid']);
        $this->assertEquals($meta['name'], $retrievedLiveInput['meta']['name']);
    }

    public function test_can_update_live_input()
    {
        // Create a live input
        $meta = ['name' => 'Test Live Input ' . uniqid()];
        $liveInput = $this->client->createLiveInput($meta);
        
        // Store ID for cleanup
        $this->createdLiveInputIds[] = $liveInput['uid'];
        
        // Update the live input
        $updatedMeta = ['name' => 'Updated Live Input ' . uniqid()];
        $updatedLiveInput = $this->client->updateLiveInput($liveInput['uid'], $updatedMeta);
        
        // Verify the update was successful
        $this->assertEquals($liveInput['uid'], $updatedLiveInput['uid']);
        $this->assertEquals($updatedMeta['name'], $updatedLiveInput['meta']['name']);
        
        // Get the live input and verify it was updated
        $retrievedLiveInput = $this->client->getLiveInput($liveInput['uid']);
        $this->assertEquals($updatedMeta['name'], $retrievedLiveInput['meta']['name']);
    }

    public function test_can_delete_live_input()
    {
        // Create a live input
        $meta = ['name' => 'Test Live Input To Delete ' . uniqid()];
        $liveInput = $this->client->createLiveInput($meta);
        
        // Delete the live input
        $result = $this->client->deleteLiveInput($liveInput['uid']);
        
        // Verify deletion was successful
        $this->assertTrue($result);
        
        // Verify the live input no longer exists
        $this->expectException(CloudflareStreamApiException::class);
        $this->client->getLiveInput($liveInput['uid']);
    }

    public function test_can_create_live_input_with_recording_config()
    {
        // Create a live input with recording configuration
        $meta = ['name' => 'Test Live Input With Recording ' . uniqid()];
        $recording = [
            'mode' => 'automatic',
            'timeoutSeconds' => 60,
            'requireSignedURLs' => false,
            'allowedOrigins' => ['*.example.com']
        ];
        
        $liveInput = $this->client->createLiveInput($meta, $recording);
        
        // Store ID for cleanup
        $this->createdLiveInputIds[] = $liveInput['uid'];
        
        // Verify the recording configuration was set correctly
        $this->assertArrayHasKey('recording', $liveInput);
        $this->assertEquals('automatic', $liveInput['recording']['mode']);
        $this->assertEquals(60, $liveInput['recording']['timeoutSeconds']);
        $this->assertFalse($liveInput['recording']['requireSignedURLs']);
        $this->assertEquals(['*.example.com'], $liveInput['recording']['allowedOrigins']);
    }
}
