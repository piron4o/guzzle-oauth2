<?php

namespace Tomhaj\GuzzleOAuth2\Token;

/**
 * @author Tomasz Hajduk <thajduk@codenetium.com>
 */
interface TokenInterface extends \Serializable
{
    /**
     * @return string
     */
    public function getAccessToken();

    /**
     * @return int
     */
    public function getExpiresIn();

    /**
     * @return bool
     */
    public function isExpired();

    /**
     * @return string
     */
    public function getTokenType();

    /**
     * @return null|string
     */
    public function getScope();

    /**
     * @return null|string
     */
    public function getRefreshToken();
}
