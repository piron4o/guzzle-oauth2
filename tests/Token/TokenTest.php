<?php

namespace Tomhaj\GuzzleOAuth2\Tests\Token;

use Tomhaj\GuzzleOAuth2\Token\Token;
use Tomhaj\GuzzleOAuth2\Token\TokenInterface;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsInterface()
    {
        $token = new Token('1', 1, 'test');
        $this->assertInstanceOf(TokenInterface::class, $token);
    }

    /**
     * @depends testImplementsInterface
     * @dataProvider invalidAccessTokenDataProvider
     *
     * @param mixed $accessToken
     */
    public function testInvalidAccessToken($accessToken)
    {
        $this->setExpectedException(\InvalidArgumentException::class, 'Access token is empty');

        new Token($accessToken, 1, 'test');
    }

    /**
     * @return array
     */
    public function invalidAccessTokenDataProvider()
    {
        return [
            [' '],
            [''],
            [null]
        ];
    }

    /**
     * @depends testImplementsInterface
     * @dataProvider invalidTokenTypeDataProvider
     *
     * @param $tokenType
     */
    public function testInvalidTokenType($tokenType)
    {
        $this->setExpectedException(\InvalidArgumentException::class, 'Token type is empty');

        new Token('abc', 1, $tokenType);
    }

    /**
     * @return array
     */
    public function invalidTokenTypeDataProvider()
    {
        return [
            [' '],
            [''],
            [null]
        ];
    }

    /**
     * @depends testImplementsInterface
     */
    public function testSerialization()
    {
        $token = new Token('abcdef', 13, 'test');

        /** @var Token $unserializedToken */
        $unserializedToken = unserialize(serialize($token));

        $this->assertEquals($token->getAccessToken(), $unserializedToken->getAccessToken());
        $this->assertEquals($token->getExpiresIn(), $unserializedToken->getExpiresIn());
        $this->assertEquals($token->getExpiresAt(), $unserializedToken->getExpiresAt());
        $this->assertEquals($token->getTokenType(), $unserializedToken->getTokenType());
        $this->assertEquals($token->getScope(), $unserializedToken->getScope());
        $this->assertEquals($token->getRefreshToken(), $unserializedToken->getRefreshToken());
    }

    /**
     * @depends testImplementsInterface
     */
    public function testExpiration()
    {
        $token = new Token('abcdef', 0, 'test');
        sleep(1);
        $this->assertTrue($token->isExpired());

        $token = new Token('abcdef', 2, 'test');
        sleep(1);
        $this->assertFalse($token->isExpired());
    }

    /**
     * @depends testImplementsInterface
     */
    public function testObject()
    {
        $token = new Token('abcdef', 0, 'test');

        $this->assertTrue(is_string($token->getAccessToken()));
        $this->assertEquals('abcdef', $token->getAccessToken());
        $this->assertTrue(is_int($token->getExpiresIn()));
        $this->assertEquals(0, $token->getExpiresIn());
        $this->assertTrue(is_string($token->getTokenType()));
        $this->assertEquals('test', $token->getTokenType());
        $this->assertInstanceOf(\DateTime::class, $token->getExpiresAt());
        $this->assertNull($token->getScope());
        $this->assertNull($token->getRefreshToken());

        $token = new Token('abcdef', 0, 'test', 'user', 'xyz');
        $this->assertNotNull($token->getScope());
        $this->assertNotNull($token->getRefreshToken());
        $this->assertEquals('user', $token->getScope());
        $this->assertEquals('xyz', $token->getRefreshToken());
    }
}
