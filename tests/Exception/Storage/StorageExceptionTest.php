<?php

namespace Tomhaj\GuzzleOAuth2\Tests\Exception\Storage;

use Tomhaj\GuzzleOAuth2\Exception\Storage\StorageException;
use Tomhaj\GuzzleOAuth2\Storage\TokenStorageInterface;

class StorageExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testClass()
    {
        $this->assertTrue(class_exists(StorageException::class));
        $this->assertInstanceOf(\DomainException::class, new StorageException());
    }

    /**
     * @depends testClass
     */
    public function testNoAccessToken()
    {
        $storage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();

        $exception = StorageException::noAccessToken($storage);

        $this->assertInstanceOf(StorageException::class, $exception);
        $this->assertEquals(sprintf('No access token in %s', get_class($storage)), $exception->getMessage());
    }
}
