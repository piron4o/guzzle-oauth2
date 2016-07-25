<?php

namespace Tomhaj\GuzzleOAuth2\Tests\GrantType\Concrete;

use Tomhaj\GuzzleOAuth2\GrantType\AbstractGrantType;
use Tomhaj\GuzzleOAuth2\GrantType\Concrete\RefreshTokenGrantType;
use Tomhaj\GuzzleOAuth2\GrantType\RefreshTokenGrantTypeInterface;
use Tomhaj\GuzzleOAuth2\Token\TokenInterface;
use Tomhaj\GuzzleOAuth2\Token\TokenRetrieveHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class RefreshTokenGrantTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testClass()
    {
        $this->assertTrue(class_exists(RefreshTokenGrantType::class));

        $object = new RefreshTokenGrantType(
            $this->getMockBuilder(TokenRetrieveHelper::class)->disableOriginalConstructor()->getMock(),
            []
        );

        $this->assertInstanceOf(RefreshTokenGrantTypeInterface::class, $object);
        $this->assertInstanceOf(AbstractGrantType::class, $object);
    }

    /**
     * @depends testClass
     */
    public function testValidGrantTypeName()
    {
        $grantType = new RefreshTokenGrantType(
            $this->getMockBuilder(TokenRetrieveHelper::class)->disableOriginalConstructor()->getMock(),
            []
        );

        $this->assertEquals('refresh_token', $grantType->getGrantType());
    }

    /**
     * @depends testClass
     */
    public function testValidRequest()
    {
        $mock = new MockHandler(
            [
                [$this, 'assertValidRequest'],
            ]
        );

        $handler   = HandlerStack::create($mock);
        $client    = new Client(['handler' => $handler]);
        $helper    = new TokenRetrieveHelper($client, '/token');
        $grantType = new RefreshTokenGrantType(
            $helper,
            [
                'client_id'     => '123',
                'client_secret' => 'abc',
            ]
        );

        $grantType->setRefreshToken('def');

        $this->assertInstanceOf(TokenInterface::class, $grantType->getToken());
    }

    public function assertValidRequest(RequestInterface $request)
    {
        $postData = $request->getBody()->getContents();

        $this->assertEquals('POST', strtoupper($request->getMethod()));
        $this->assertContains('grant_type=refresh_token', $postData);
        $this->assertContains('client_id=123', $postData);
        $this->assertContains('client_secret=abc', $postData);
        $this->assertContains('refresh_token=def', $postData);

        $json = [
            'access_token'  => 'asdasfaf',
            'expires_in'    => 15,
            'token_type'    => 'barear',
            'scope'         => 'user',
            'refresh_token' => 'esd',
        ];

        return new Response(200, [], json_encode($json));
    }
}
