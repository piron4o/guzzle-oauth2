<?php

namespace Tomhaj\GuzzleOAuth2\Tests\Token;

use Tomhaj\GuzzleOAuth2\Exception\TokenRetrieveException;
use Tomhaj\GuzzleOAuth2\Token\TokenInterface;
use Tomhaj\GuzzleOAuth2\Token\TokenRetrieveHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class TokenRetrieveHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testClass()
    {
        $this->assertTrue(class_exists(TokenRetrieveHelper::class));
    }

    /**
     * @depends      testClass
     * @dataProvider invalidStatusCodeDataProvider
     *
     * @param int $statusCode
     */
    public function testInvalidStatusCode($statusCode)
    {
        $this->setExpectedException(
            TokenRetrieveException::class,
            sprintf('Server returns %d status code', $statusCode)
        );

        $mock    = new MockHandler([new Response($statusCode)]);
        $handler = HandlerStack::create($mock);

        $client = new Client(['handler' => $handler]);

        $helper = new TokenRetrieveHelper($client, '/token');
        $helper->getAccessToken([]);
    }

    /**
     * @return array
     */
    public function invalidStatusCodeDataProvider()
    {
        return [
            [201],
            [204],
            [301],
            [400],
            [500],
        ];
    }

    /**
     * @depends      testClass
     * @dataProvider invalidBodyDataProvider
     *
     * @param string $responseBody
     */
    public function testInvalidResponseBody($responseBody)
    {
        $this->setExpectedException(
            TokenRetrieveException::class,
            sprintf(
                'Server returns invalid body: %s',
                is_string($responseBody) ? $responseBody : print_r($responseBody, true)
            )
        );

        $mock    = new MockHandler([new Response(200, [], $responseBody)]);
        $handler = HandlerStack::create($mock);

        $client = new Client(['handler' => $handler]);

        $helper = new TokenRetrieveHelper($client, '/token');
        $helper->getAccessToken([]);
    }

    /**
     * @return array
     */
    public function invalidBodyDataProvider()
    {
        return [
            [''],
            [' '],
            ['asdasfsfasf'],
            ['1212412414'],
            ['{safasfasfa'],
            ['asfasas}:{'],
            ['{"asdasd":asdasa"}'],
        ];
    }

    /**
     * @depends testClass
     * @dataProvider requestParamsDataProvider
     *
     * @param array  $postParams
     * @param string $expectedRequestBody
     */
    public function testClientRequest(array $postParams, $expectedRequestBody)
    {
        $container = [];
        $history   = Middleware::history($container);
        $json      = [
            'access_token'  => 'asdasfaf',
            'expires_in'    => 15,
            'token_type'    => 'barear',
            'scope'         => 'user',
            'refresh_token' => 'esd',
        ];

        $mock    = new MockHandler([new Response(200, [], json_encode($json))]);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        $client = new Client(['handler' => $handler]);

        $helper = new TokenRetrieveHelper($client, '/token');
        $helper->getAccessToken($postParams);

        $this->assertCount(1, $container);

        /** @var RequestInterface $request */
        $request = $container[0]['request'];

        $this->assertEquals(strtolower('POST'), strtolower($request->getMethod()));
        $this->assertEquals('/token', $request->getRequestTarget(), 'Invalid request path');
        $this->assertEquals($expectedRequestBody, (string) $request->getBody(), 'Invalid request body');
    }

    /**
     * @return array
     */
    public function requestParamsDataProvider()
    {
        return [
            [
                [
                    'client_id'     => 123,
                    'client_secret' => 'abc',
                    'grant_type'    => 'client_credentials',
                ],
                'client_id=123&client_secret=abc&grant_type=client_credentials'
            ],
            [
                [
                    'client_id'     => 123,
                    'client_secret' => 'abc',
                ],
                'client_id=123&client_secret=abc'
            ],
        ];
    }

    /**
     * @depends testClass
     */
    public function testReturnToken()
    {
        $json = [
            'access_token'  => 'asdasfaf',
            'expires_in'    => 15,
            'token_type'    => 'barear',
            'scope'         => 'user',
            'refresh_token' => 'esd',
        ];

        $mock    = new MockHandler([new Response(200, [], json_encode($json))]);
        $handler = HandlerStack::create($mock);

        $client = new Client(['handler' => $handler]);
        $helper = new TokenRetrieveHelper($client, '/token');

        $token = $helper->getAccessToken([]);

        $this->assertInstanceOf(TokenInterface::class, $token, 'Helper should return TokenInterfaceInstance');
        $this->assertEquals('asdasfaf', $token->getAccessToken(), 'Helper not set correct access token');
        $this->assertEquals(15, $token->getExpiresIn(), 'Helper not set correct expires in');
        $this->assertEquals('barear', $token->getTokenType(), 'Helper not set correct token type');
        $this->assertEquals('user', $token->getScope(), 'Helper not set correct scope');
        $this->assertEquals('esd', $token->getRefreshToken(), 'Helper not set correct refresh token');
    }
}
