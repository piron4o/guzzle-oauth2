<?php

namespace Tomhaj\GuzzleOAuth2\Tests\Exception;

use Tomhaj\GuzzleOAuth2\Exception\TokenGrantTypeException;
use Tomhaj\GuzzleOAuth2\GrantType\GrantTypeInterface;

class TokenGrantTypeExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testClass()
    {
        $this->assertTrue(class_exists(TokenGrantTypeException::class));
        $this->assertInstanceOf(\DomainException::class, new TokenGrantTypeException());
    }

    /**
     * @depends testClass
     */
    public function testNotInstanceOfToken()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|GrantTypeInterface $grantType */
        $grantType = $this->getMockBuilder(GrantTypeInterface::class)->getMock();
        $grantType
            ->expects($this->once())
            ->method('getGrantType')
            ->willReturn('test_grant_type');

        $exception = TokenGrantTypeException::notInstanceOfToken($grantType, 'test');

        $this->assertInstanceOf(TokenGrantTypeException::class, $exception);
        $this->assertEquals(
            'GrantType test_grant_type should return TokenInterface instance. "string" given',
            $exception->getMessage()
        );
    }
}
