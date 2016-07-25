<?php

namespace Tomhaj\GuzzleOAuth2\Exception;

/**
 * @author Tomasz Hajduk <thajduk@codenetium.com>
 */
class TokenRetrieveException extends \DomainException
{
    /**
     * @param string $statusCode
     *
     * @return TokenRetrieveException
     */
    public static function invalidStatusCode($statusCode)
    {
        return new self(sprintf('Server returns %d status code', $statusCode));
    }

    /**
     * @param string $responseBody
     *
     * @return TokenRetrieveException
     */
    public static function invalidResponseBody($responseBody)
    {
        return new self(
            sprintf(
                'Server returns invalid body: %s',
                is_string($responseBody) ? $responseBody : print_r($responseBody, true)
            )
        );
    }
}
