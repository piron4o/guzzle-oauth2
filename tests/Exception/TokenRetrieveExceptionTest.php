<?php

namespace Tomhaj\GuzzleOAuth2\Tests\Exception;

use Tomhaj\GuzzleOAuth2\Exception\TokenRetrieveException;

class TokenRetrieveExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testClass()
    {
        $this->assertTrue(class_exists(TokenRetrieveException::class));
        $this->assertInstanceOf(\DomainException::class, new TokenRetrieveException());
    }

    /**
     * @depends testClass
     */
    public function testInvalidStatusCode()
    {
        $exception = TokenRetrieveException::invalidStatusCode(500);

        $this->assertInstanceOf(TokenRetrieveException::class, $exception);
        $this->assertEquals('Server returns 500 status code', $exception->getMessage());
    }

    /**
     * @depends testClass
     */
    public function testInvalidResponseBody()
    {
        $exception = TokenRetrieveException::invalidResponseBody('abcdef');

        $this->assertInstanceOf(TokenRetrieveException::class, $exception);
        $this->assertEquals('Server returns invalid body: abcdef', $exception->getMessage());
    }
}
