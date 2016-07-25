<?php

namespace Tomhaj\GuzzleOAuth2\Tests\GrantType\Concrete;

use Tomhaj\GuzzleOAuth2\GrantType\AbstractGrantType;
use Tomhaj\GuzzleOAuth2\GrantType\Concrete\PasswordGrantType;
use Tomhaj\GuzzleOAuth2\Token\TokenInterface;
use Tomhaj\GuzzleOAuth2\Token\TokenRetrieveHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class PasswordGrantTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testClass()
    {
        $this->assertTrue(class_exists(PasswordGrantType::class));
        $this->assertInstanceOf(
            AbstractGrantType::class,
            new PasswordGrantType(
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
        $grantType = new PasswordGrantType(
            $this->getMockBuilder(TokenRetrieveHelper::class)->disableOriginalConstructor()->getMock(),
            []
        );

        $this->assertEquals('password', $grantType->getGrantType());
    }

    /**
     * @depends testClass
     */
    public function testMissingRequireParams()
    {
        $this->setExpectedException(
            MissingOptionsException::class,
            'The required options "password", "username" are missing.'
        );

        $client    = new Client();
        $helper    = new TokenRetrieveHelper($client, '/token');
        $grantType = new PasswordGrantType(
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
        $grantType = new PasswordGrantType(
            $helper,
            [
                'client_id'     => '123',
                'client_secret' => 'abc',
                'username'      => 'test_user',
                'password'      => 'test_password',
            ]
        );

        $this->assertInstanceOf(TokenInterface::class, $grantType->getToken());
    }

    public function assertValidRequest(RequestInterface $request)
    {
        $postData = $request->getBody()->getContents();

        $this->assertEquals('POST', strtoupper($request->getMethod()));
        $this->assertContains('grant_type=password', $postData);
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
