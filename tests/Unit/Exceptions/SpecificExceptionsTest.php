<?php

namespace SentFlying\CloudflareStreamLaravel\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use SentFlying\CloudflareStreamLaravel\Exceptions\AuthenticationException;
use SentFlying\CloudflareStreamLaravel\Exceptions\NotFoundException;
use SentFlying\CloudflareStreamLaravel\Exceptions\ValidationException;
use SentFlying\CloudflareStreamLaravel\Exceptions\CloudflareStreamApiException;

class SpecificExceptionsTest extends TestCase
{
    public function test_authentication_exception_extends_base_exception()
    {
        $exception = new AuthenticationException('Authentication failed', 401);
        
        $this->assertInstanceOf(CloudflareStreamApiException::class, $exception);
        $this->assertEquals('Authentication failed', $exception->getMessage());
        $this->assertEquals(401, $exception->getCode());
    }
    
    public function test_validation_exception_extends_base_exception()
    {
        $exception = new ValidationException('Validation failed', 422);
        
        $this->assertInstanceOf(CloudflareStreamApiException::class, $exception);
        $this->assertEquals('Validation failed', $exception->getMessage());
        $this->assertEquals(422, $exception->getCode());
    }
    
    public function test_not_found_exception_extends_base_exception()
    {
        $exception = new NotFoundException('Resource not found', 404);
        
        $this->assertInstanceOf(CloudflareStreamApiException::class, $exception);
        $this->assertEquals('Resource not found', $exception->getMessage());
        $this->assertEquals(404, $exception->getCode());
    }
}
