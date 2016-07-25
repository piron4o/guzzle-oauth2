<?php

namespace Tomhaj\GuzzleOAuth2\Tests\GrantType;

use Tomhaj\GuzzleOAuth2\GrantType\AbstractGrantType;
use Tomhaj\GuzzleOAuth2\GrantType\GrantTypeInterface;
use Tomhaj\GuzzleOAuth2\Token\TokenRetrieveHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class AbstractGrantTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testClass()
    {
        $this->assertTrue(class_exists(AbstractGrantType::class));
        $this->assertInstanceOf(
            GrantTypeInterface::class,
            $this->getMockBuilder(AbstractGrantType::class)->disableOriginalConstructor()->getMock()
        );
    }

    /**
     * @depends testClass
     */
    public function testRequiredConfig()
    {
        $this->setExpectedException(MissingOptionsException::class);

        $mock = new MockHandler(
            [
                [$this, 'assertPostBody'],
            ]
        );

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);
        $helper  = new TokenRetrieveHelper($client, '/token');

        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractGrantType $grantType */
        $grantType = $this->getMockForAbstractClass(
            AbstractGrantType::class,
            [
                $helper,
                [
                ],
            ]
        );

        $grantType->getToken();
    }

    /**
     * @depends testClass
     */
    public function testTokenRequest()
    {
        $mock = new MockHandler(
            [
                [$this, 'assertPostBody'],
            ]
        );

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);
        $helper  = new TokenRetrieveHelper($client, '/token');

        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractGrantType $grantType */
        $grantType = $this->getMockForAbstractClass(
            AbstractGrantType::class,
            [
                $helper,
                [
                    'client_id'     => '123',
                    'client_secret' => '328',
                ],
            ]
        );

        $grantType->getToken();
    }

    public function assertPostBody(RequestInterface $request)
    {
        $postData = $request->getBody()->getContents();

        $this->assertEquals('POST', strtoupper($request->getMethod()));
        $this->assertContains('client_id=123', $postData);
        $this->assertContains('client_secret=328', $postData);

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
