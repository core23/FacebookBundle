<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Tests\Connection;

use Core23\FacebookBundle\Connection\FacebookConnection;
use Facebook\Facebook;
use PHPUnit\Framework\TestCase;

class FacebookConnectionTest extends TestCase
{
    public function testItIsInstantiable(): void
    {
        $connection = new FacebookConnection('key', 'secret');

        $this->assertInstanceOf(Facebook::class, $connection);
    }

    public function testGetApiId(): void
    {
        $connection = new FacebookConnection('key', 'secret');

        $this->assertSame('key', $connection->getApiId());
    }

    public function testGetSharedSecre(): void
    {
        $connection = new FacebookConnection('key', 'secret');

        $this->assertSame('secret', $connection->getSharedSecret());
    }
}
