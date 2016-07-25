<?php

namespace Tomhaj\GuzzleOAuth2\Tests\GrantType\Concrete;

use Tomhaj\GuzzleOAuth2\GrantType\AbstractGrantType;
use Tomhaj\GuzzleOAuth2\GrantType\Concrete\AuthorizationCodeGrantType;
use Tomhaj\GuzzleOAuth2\Token\TokenInterface;
use Tomhaj\GuzzleOAuth2\Token\TokenRetrieveHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class AuthorizationCodeGrantTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testClass()
    {
        $this->assertTrue(class_exists(AuthorizationCodeGrantType::class));
        $this->assertInstanceOf(
            AbstractGrantType::class,
            new AuthorizationCodeGrantType(
                $this->getMockBuilder(TokenRetrieveHelper::class)->disableOriginalConstructor()->getMock(),
                []
            )
        );
    }

    /**
     * @depends testClass
     */
    public function testValidGrantTypeName()
    {
        $grantType = new AuthorizationCodeGrantType(
            $this->getMockBuilder(TokenRetrieveHelper::class)->disableOriginalConstructor()->getMock(),
            []
        );

        $this->assertEquals('authorization_code', $grantType->getGrantType());
    }

    /**
     * @depends testClass
     */
    public function testMissingRequireParams()
    {
        $this->setExpectedException(
            MissingOptionsException::class,
            'The required option "code" is missing.'
        );

        $client    = new Client();
        $helper    = new TokenRetrieveHelper($client, '/token');
        $grantType = new AuthorizationCodeGrantType(
            $helper,
            [
                'client_id'     => '123',
                'client_secret' => 'abc',
            ]
        );

        $grantType->getToken();
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
        $grantType = new AuthorizationCodeGrantType(
            $helper,
            [
                'client_id'     => '123',
                'client_secret' => 'abc',
                'code'          => 'asd',
            ]
        );

        $this->assertInstanceOf(TokenInterface::class, $grantType->getToken());
    }

    public function assertValidRequest(RequestInterface $request)
    {
        $postData = $request->getBody()->getContents();

        $this->assertEquals('POST', strtoupper($request->getMethod()));
        $this->assertContains('grant_type=authorization_code', $postData);
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
