<?php

namespace Tomhaj\GuzzleOAuth2\Exception\Storage;

use Tomhaj\GuzzleOAuth2\Storage\TokenStorageInterface;

class StorageException extends \DomainException
{
    public static function noAccessToken(TokenStorageInterface $tokenStorage)
    {
        return new self(sprintf('No access token in %s', get_class($tokenStorage)));
    }
}
