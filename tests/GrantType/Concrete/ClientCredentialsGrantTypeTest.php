<?php

namespace Tomhaj\GuzzleOAuth2\Tests\GrantType\Concrete;

use Tomhaj\GuzzleOAuth2\GrantType\AbstractGrantType;
use Tomhaj\GuzzleOAuth2\GrantType\Concrete\ClientCredentialsGrantType;
use Tomhaj\GuzzleOAuth2\Token\TokenInterface;
use Tomhaj\GuzzleOAuth2\Token\TokenRetrieveHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class ClientCredentialsGrantTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testClass()
    {
        $this->assertTrue(class_exists(ClientCredentialsGrantType::class));
        $this->assertInstanceOf(
            AbstractGrantType::class,
            new ClientCredentialsGrantType(
                $this->getMockBuilder(TokenRetrieveHelper::class)->disableOriginalConstructor()->getMock(),
                []
            )
        );
    }

    /**
     * @depends testClass
     */
    public function testValidName()
    {
        $grantType = new ClientCredentialsGrantType(
            $this->getMockBuilder(TokenRetrieveHelper::class)->disableOriginalConstructor()->getMock(),
            []
        );

        $this->assertEquals('client_credentials', $grantType->getGrantType());
    }

    public function testValidGrantTypeAuth()
    {
        $mock = new MockHandler(
            [
                [$this, 'assertValidGrantType'],
            ]
        );

        $handler   = HandlerStack::create($mock);
        $client    = new Client(['handler' => $handler]);
        $helper    = new TokenRetrieveHelper($client, '/token');
        $grantType = new ClientCredentialsGrantType(
            $helper,
            [
                'client_id'     => '123',
                'client_secret' => 'abc',
            ]
        );

        $this->assertInstanceOf(TokenInterface::class, $grantType->getToken());
    }

    /**
     * @param RequestInterface $request
     * @param array            $opts
     *
     * @return Response
     */
    public function assertValidGrantType(RequestInterface $request, array $opts)
    {
        $postData = $request->getBody()->getContents();

        $this->assertEquals('POST', strtoupper($request->getMethod()));
        $this->assertContains('grant_type=client_credentials', $postData);
        $this->assertContains('client_id=123', $postData);
        $this->assertContains('client_secret=abc', $postData);

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
