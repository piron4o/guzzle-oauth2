<?php

namespace Tomhaj\GuzzleOAuth2\GrantType;

/**
 * @author Tomasz Hajduk <thajduk@codenetium.com>
 */
interface RefreshTokenGrantTypeInterface extends GrantTypeInterface
{
    public function setRefreshToken($refreshToken);
}
