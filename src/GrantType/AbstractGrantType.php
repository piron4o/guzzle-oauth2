<?php

namespace Tomhaj\GuzzleOAuth2\GrantType;

use Tomhaj\GuzzleOAuth2\Token\TokenRetrieveHelper;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractGrantType implements GrantTypeInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var TokenRetrieveHelper
     */
    private $helper;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    public function __construct(TokenRetrieveHelper $helper, array $config = [])
    {
        $this->config       = $config;
        $this->helper       = $helper;

        $this->optionsResolver = new OptionsResolver();
        $this->optionsResolver->setRequired(['client_id', 'client_secret', 'grant_type']);
        $this->optionsResolver->setDefault('grant_type', $this->getGrantType());
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        $this->configureOptions($this->optionsResolver);

        return $this->helper->getAccessToken($this->optionsResolver->resolve($this->config));
    }

    /**
     * @param OptionsResolver $optionsResolver
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
    }
}
