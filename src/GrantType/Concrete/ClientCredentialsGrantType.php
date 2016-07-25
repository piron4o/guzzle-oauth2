<?php

namespace Tomhaj\GuzzleOAuth2\GrantType\Concrete;

use Tomhaj\GuzzleOAuth2\GrantType\AbstractGrantType;

class ClientCredentialsGrantType extends AbstractGrantType
{
    /**
     * {@inheritDoc}
     */
    public function getGrantType()
    {
        return 'client_credentials';
    }

}
