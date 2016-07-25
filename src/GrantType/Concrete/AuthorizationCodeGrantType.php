<?php

namespace Tomhaj\GuzzleOAuth2\GrantType\Concrete;

use Tomhaj\GuzzleOAuth2\GrantType\AbstractGrantType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorizationCodeGrantType extends AbstractGrantType
{
    /**
     * @inheritDoc
     */
    public function getGrantType()
    {
        return 'authorization_code';
    }

    /**
     * @inheritDoc
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired('code');
        $optionsResolver->setDefault('redirect_uri', '');
    }
}
