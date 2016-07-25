<?php

namespace Tomhaj\GuzzleOAuth2\Storage;

use Tomhaj\GuzzleOAuth2\Token\TokenInterface;

/**
 * @author Tomasz Hajduk <thajduk@codenetium.com>
 */
interface TokenStorageInterface
{
    /**
     * @return TokenInterface
     */
    public function retrieveAccessToken();

    /**
     * @param TokenInterface $token
     *
     * @return TokenStorageInterface
     */
    public function storeAccessToken(TokenInterface $token);

    /**
     * @return bool
     */
    public function hasAccessToken();

    /**
     * Delete the users token. Aka, log out.
     *
     * @return TokenStorageInterface
     */
    public function clearToken();
}
