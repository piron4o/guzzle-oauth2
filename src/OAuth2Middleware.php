<?php

namespace Tomhaj\GuzzleOAuth2;

use Tomhaj\GuzzleOAuth2\Exception\TokenGrantTypeException;
use Tomhaj\GuzzleOAuth2\GrantType\GrantTypeInterface;
use Tomhaj\GuzzleOAuth2\GrantType\RefreshTokenGrantTypeInterface;
use Tomhaj\GuzzleOAuth2\Storage\TokenStorageInterface;
use Tomhaj\GuzzleOAuth2\Token\TokenInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @author Tomasz Hajduk <thajduk@codenetium.com>
 */
class OAuth2Middleware
{
    /**
     * @var GrantTypeInterface
     */
    private $grantType;

    /**
     * @var RefreshTokenGrantTypeInterface
     */
    private $refreshGrantType;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param GrantTypeInterface             $grantType
     * @param TokenStorageInterface          $tokenStorage
     * @param RefreshTokenGrantTypeInterface $refreshGrantType
     */
    public function __construct(
        GrantTypeInterface $grantType,
        TokenStorageInterface $tokenStorage,
        RefreshTokenGrantTypeInterface $refreshGrantType = null
    ) {
        $this->grantType        = $grantType;
        $this->tokenStorage     = $tokenStorage;
        $this->refreshGrantType = $refreshGrantType;
    }

    public function __invoke(RequestInterface $request)
    {
        return $request->withHeader('Authorization', sprintf('Bearer %s', $this->getToken()));
    }

    /**
     * @return string
     */
    private function getToken()
    {
        if (true === $this->tokenStorage->hasAccessToken()) {
            $token = $this->tokenStorage->retrieveAccessToken();
        } else {
            $token = $this->retrieveToken($this->grantType);
        }

        if (true === $token->isExpired() && null !== $this->refreshGrantType) {
            $this->refreshGrantType->setRefreshToken($token->getRefreshToken());

            $token = $this->retrieveToken($this->refreshGrantType);
        }

        return $token->getAccessToken();
    }

    /**
     * @param GrantTypeInterface $grantTypeInterface
     *
     * @return TokenInterface
     */
    private function retrieveToken(GrantTypeInterface $grantTypeInterface)
    {
        $token = $grantTypeInterface->getToken();

        if (false === ($token instanceof TokenInterface)) {
            throw TokenGrantTypeException::notInstanceOfToken($grantTypeInterface, $token);
        }

        return $token;
    }
}
