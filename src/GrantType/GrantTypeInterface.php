<?php

namespace Tomhaj\GuzzleOAuth2\GrantType;

use Tomhaj\GuzzleOAuth2\Token\TokenInterface;

/**
 * @author Tomasz Hajduk <thajduk@codenetium.com>
 */
interface GrantTypeInterface
{
    /**
     * @return TokenInterface
     */
    public function getToken();

    /**
     * @return string
     */
    public function getGrantType();
}
