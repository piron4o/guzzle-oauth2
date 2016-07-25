<?php

namespace Tomhaj\GuzzleOAuth2\Exception;

use Tomhaj\GuzzleOAuth2\GrantType\GrantTypeInterface;

class TokenGrantTypeException extends \DomainException
{
    public static function notInstanceOfToken(GrantTypeInterface $grantType, $value)
    {
        return new self(
            sprintf(
                'GrantType %s should return TokenInterface instance. "%s" given',
                $grantType->getGrantType(),
                (true === is_object($value) ? get_class($value) : gettype($value))
            )
        );
    }
}
