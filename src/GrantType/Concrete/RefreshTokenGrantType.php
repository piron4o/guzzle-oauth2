<?php

namespace Tomhaj\GuzzleOAuth2\GrantType\Concrete;

use Tomhaj\GuzzleOAuth2\GrantType\AbstractGrantType;
use Tomhaj\GuzzleOAuth2\GrantType\RefreshTokenGrantTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RefreshTokenGrantType extends AbstractGrantType implements RefreshTokenGrantTypeInterface
{
    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @inheritDoc
     */
    public function getGrantType()
    {
        return 'refresh_token';
    }

    /**
     * @inheritDoc
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefaults(
            [
                'refresh_token' => $this->refreshToken,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }
}
