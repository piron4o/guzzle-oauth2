<?php

namespace Tomhaj\GuzzleOAuth2\Token;

use Tomhaj\GuzzleOAuth2\Exception\TokenRetrieveException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;

class TokenRetrieveHelper
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $tokenEndpoint;

    /**
     * @param ClientInterface $client
     * @param string          $tokenEndpoint
     */
    public function __construct(ClientInterface $client, $tokenEndpoint)
    {
        $this->client        = $client;
        $this->tokenEndpoint = $tokenEndpoint;
    }

    /**
     * @param array $formParams
     *
     * @return TokenInterface
     */
    public function getAccessToken(array $formParams)
    {
        try {
            $response = $this
                ->client
                ->request(
                    'POST',
                    $this->tokenEndpoint,
                    [
                        'form_params' => $formParams,
                    ]
                );

            $statusCode = $response->getStatusCode();
        } catch (BadResponseException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
        }

        if (200 !== $statusCode) {
            throw TokenRetrieveException::invalidStatusCode($statusCode);
        }

        $json = json_decode($response->getBody(), true);

        if (null === $json || false === is_array($json)) {
            throw TokenRetrieveException::invalidResponseBody((string) $response->getBody());
        }

        return new Token(
            $json['access_token'],
            $json['expires_in'],
            $json['token_type'],
            $json['scope'],
            $json['refresh_token']
        );
    }
}
