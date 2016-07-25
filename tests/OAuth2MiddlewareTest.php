<?php

namespace Tomhaj\GuzzleOAuth2\Tests;

use Tomhaj\GuzzleOAuth2\Exception\TokenGrantTypeException;
use Tomhaj\GuzzleOAuth2\GrantType\GrantTypeInterface;
use Tomhaj\GuzzleOAuth2\GrantType\RefreshTokenGrantTypeInterface;
use Tomhaj\GuzzleOAuth2\OAuth2Middleware;
use Tomhaj\GuzzleOAuth2\Storage\TokenStorageInterface;
use Tomhaj\GuzzleOAuth2\Token\TokenInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

/**
 * @covers \Tomhaj\GuzzleOAuth2\OAuth2Middleware
 */
class OAuth2MiddlewareTest extends \PHPUnit_Framework_TestCase
{
    public function testClass()
    {
        $this->assertTrue(class_exists(OAuth2Middleware::class), 'Class not exists');

        $grantType    = $this->getMockBuilder(GrantTypeInterface::class)->getMock();
        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $middleWare   = new OAuth2Middleware($grantType, $tokenStorage);

        $this->assertTrue(method_exists($middleWare, '__invoke'), 'Method __invoke not exists');
    }

    /**
     * @depends testClass
     */
    public function testInvalidTokenTypeFromBaseGrantType()
    {
        $this->expectException(TokenGrantTypeException::class);

        $grantType = $this->getMockBuilder(GrantTypeInterface::class)->getMock();
        $grantType
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $tokenStorage
            ->expects($this->once())
            ->method('hasAccessToken')
            ->willReturn(false);

        $middleWare = new OAuth2Middleware($grantType, $tokenStorage);

        $handler = HandlerStack::create();
        $handler->push(Middleware::mapRequest($middleWare));

        $client = new Client(['handler' => $handler]);
        $client->get('/');
    }

    /**
     * @depends testClass
     */
    public function testInvalidTokenTypeFromRefreshGrant()
    {
        $this->expectException(TokenGrantTypeException::class);

        $token = $this->getMockBuilder(TokenInterface::class)->getMock();

        $token
            ->expects($this->once())
            ->method('isExpired')
            ->willReturn(true);

        $token
            ->expects($this->once())
            ->method('getRefreshToken')
            ->willReturn('def');

        $grantType = $this->getMockBuilder(GrantTypeInterface::class)->getMock();
        $grantType
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $refreshGrantType = $this->getMockBuilder(RefreshTokenGrantTypeInterface::class)->getMock();
        $refreshGrantType
            ->expects($this->once())
            ->method('setRefreshToken')
            ->with('def');
        $refreshGrantType
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $tokenStorage
            ->expects($this->once())
            ->method('hasAccessToken')
            ->willReturn(false);

        $middleWare = new OAuth2Middleware($grantType, $tokenStorage, $refreshGrantType);

        $handler = HandlerStack::create();
        $handler->push(Middleware::mapRequest($middleWare));

        $client = new Client(['handler' => $handler]);
        $client->get('/');
    }

    /**
     * @depends testClass
     */
    public function testOAuthHeader()
    {
        $token = $this->getMockBuilder(TokenInterface::class)->getMock();
        $token
            ->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('abc');

        $grantType = $this->getMockBuilder(GrantTypeInterface::class)->getMock();
        $grantType
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $tokenStorage
            ->expects($this->once())
            ->method('hasAccessToken')
            ->willReturn(false);

        $middleWare = new OAuth2Middleware($grantType, $tokenStorage);

        $mock = new MockHandler([new Response(200),]);
        $handler = HandlerStack::create($mock);
        $handler->push(Middleware::mapRequest($middleWare));

        $container = [];
        $history   = Middleware::history($container);

        $handler->push($history);

        $client = new Client(['handler' => $handler]);
        $client->get('/');

        $this->assertCount(1, $container);

        /** @var RequestInterface $request */
        $request = $container[0]['request'];

        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertEquals('Bearer abc', $request->getHeader('Authorization')[0]);
    }
}
