<?php

namespace Tomhaj\GuzzleOAuth2\GrantType\Concrete;

use Tomhaj\GuzzleOAuth2\GrantType\AbstractGrantType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordGrantType extends AbstractGrantType
{
    /**
     * @inheritDoc
     */
    public function getGrantType()
    {
        return 'password';
    }

    /**
     * @inheritDoc
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired(['username', 'password']);
    }
}
