<?php

namespace SentFlying\CloudflareStreamLaravel\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use SentFlying\CloudflareStreamLaravel\Exceptions\CloudflareStreamApiException;

class CloudflareStreamApiExceptionTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $exception = new CloudflareStreamApiException('Test message', 400);
        
        $this->assertInstanceOf(CloudflareStreamApiException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
    }
    
    public function test_it_can_store_and_retrieve_errors()
    {
        $errors = [
            ['code' => 1000, 'message' => 'Error 1'],
            ['code' => 1001, 'message' => 'Error 2'],
        ];
        
        $exception = new CloudflareStreamApiException('Multiple errors', 400, $errors);
        
        $this->assertEquals($errors, $exception->getErrors());
    }
}
