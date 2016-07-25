<?php

namespace Tomhaj\GuzzleOAuth2\Token;

/**
 * @author Tomasz Hajduk <thajduk@codenetium.com>
 */
class Token implements TokenInterface
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var int
     */
    private $expiresIn;

    /**
     * @var \DateTime
     */
    private $expiresAt;

    /**
     * @var string
     */
    private $tokenType;

    /**
     * @var null|string
     */
    private $scope;

    /**
     * @var null|string
     */
    private $refreshToken;

    public function __construct($accessToken, $expiresIn, $tokenType, $scope = null, $refreshToken = null)
    {
        $this->accessToken  = trim($accessToken);
        $this->expiresIn    = (int) $expiresIn;
        $this->tokenType    = trim($tokenType);
        $this->scope        = true === empty(trim($scope)) ? null : trim($scope);
        $this->refreshToken = true === empty(trim($refreshToken)) ? null : trim($refreshToken);

        $this->expiresAt = new \DateTime();
        $this->expiresAt->add(new \DateInterval(sprintf('PT%sS', $this->expiresIn)));

        $this->validate();
    }

    /**
     * {{@inheritDoc}}
     */
    public function serialize()
    {
        return serialize(
            [
                $this->accessToken,
                $this->expiresIn,
                $this->expiresAt,
                $this->tokenType,
                $this->scope,
                $this->refreshToken,
            ]
        );
    }

    /**
     * {{@inheritDoc}}
     */
    public function unserialize($serialized)
    {
        list(
            $this->accessToken,
            $this->expiresIn,
            $this->expiresAt,
            $this->tokenType,
            $this->scope,
            $this->refreshToken,
            ) = unserialize($serialized);
    }

    /**
     * {@inheritDoc}
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * {@inheritDoc}
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * {@inheritDoc}
     */
    public function isExpired()
    {
        return $this->expiresAt !== null && $this->expiresAt->getTimestamp() < time();
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * {@inheritDoc}
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * {@inheritDoc}
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    private function validate()
    {
        if (true === empty($this->accessToken)) {
            throw new \InvalidArgumentException('Access token is empty');
        }

        if (true === empty($this->tokenType)) {
            throw new \InvalidArgumentException('Token type is empty');
        }
    }
}
