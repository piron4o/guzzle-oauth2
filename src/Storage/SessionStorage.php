<?php

namespace Tomhaj\GuzzleOAuth2\Storage;

use Tomhaj\GuzzleOAuth2\Exception\Storage\StorageException;
use Tomhaj\GuzzleOAuth2\Token\TokenInterface;

class SessionStorage implements TokenStorageInterface
{
    /**
     * @var bool
     */
    private $start;
    /**
     * @var string
     */
    private $sessionName;

    /**
     * SessionStorage constructor.
     *
     * @param bool   $start
     * @param string $sessionName
     */
    public function __construct($start = true, $sessionName = 'guzzle-oauth2-token')
    {
        $this->start       = $start;
        $this->sessionName = $sessionName;

        $this->initialize();
    }

    /**
     * @inheritDoc
     */
    public function retrieveAccessToken()
    {
        if (false === $this->hasAccessToken()) {
            throw StorageException::noAccessToken($this);
        }

        $token = unserialize($_SESSION[$this->sessionName]);

        return true === ($token instanceof TokenInterface) ? $token : null;
    }

    /**
     * @inheritDoc
     */
    public function storeAccessToken(TokenInterface $token)
    {
        $_SESSION[$this->sessionName] = serialize($token);
    }

    /**
     * @inheritDoc
     */
    public function hasAccessToken()
    {
        return true === isset($_SESSION[$this->sessionName]) && null !== $_SESSION[$this->sessionName];
    }

    /**
     * @inheritDoc
     */
    public function clearToken()
    {
        $_SESSION[$this->sessionName] = null;
    }

    /**
     * @return bool
     */
    private function isSessionStarted()
    {
        return session_status() != PHP_SESSION_NONE;
    }

    private function initialize()
    {
        if (true === $this->start && false === $this->isSessionStarted()) {
            session_start();
        }

        if (false === isset($_SESSION[$this->sessionName])) {
            $_SESSION[$this->sessionName] = null;
        }
    }
}
