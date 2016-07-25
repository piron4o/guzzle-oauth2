<?php

namespace Tomhaj\GuzzleOAuth2\Tests\Storage;

use Tomhaj\GuzzleOAuth2\Exception\Storage\StorageException;
use Tomhaj\GuzzleOAuth2\Storage\SessionStorage;
use Tomhaj\GuzzleOAuth2\Storage\TokenStorageInterface;
use Tomhaj\GuzzleOAuth2\Token\Token;
use Tomhaj\GuzzleOAuth2\Token\TokenInterface;

class SessionStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testClass()
    {
        $this->assertTrue(
            class_exists(SessionStorage::class),
            'class Tomhaj\GuzzleOAuth2\Storage\SessionStorage not exists'
        );

        $this->assertInstanceOf(
            TokenStorageInterface::class,
            new SessionStorage(false),
            'Tomhaj\GuzzleOAuth2\Storage\SessionStorage must implements Tomhaj\GuzzleOAuth2\Storage\TokenStorageInterface'
        );
    }

    public function testNoAccessToken()
    {
        $storage = new SessionStorage(false);

        $this->assertFalse($storage->hasAccessToken(), 'Storage should not have a token');
    }

    public function testRetrieveWithoutToken()
    {
        $this->expectException(StorageException::class);
        $this->expectExceptionMessage('No access token in Tomhaj\GuzzleOAuth2\Storage\SessionStorage');

        (new SessionStorage(false))->retrieveAccessToken();
    }

    public function testStoreToken()
    {
        $token   = new Token('abc', 13, 'barear');
        $storage = new SessionStorage(false);

        $storage->storeAccessToken($token);

        $this->assertTrue($storage->hasAccessToken(), 'Access token not stored correctly');
        $this->assertInstanceOf(TokenInterface::class, $storage->retrieveAccessToken());
    }

    public function testClearToken()
    {
        $token   = new Token('abc', 13, 'barear');
        $storage = new SessionStorage(false);

        $storage->storeAccessToken($token);
        $storage->clearToken();

        $this->assertFalse($storage->hasAccessToken(), 'Access token should be cleared');
    }
}
