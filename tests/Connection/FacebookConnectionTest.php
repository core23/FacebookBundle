<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Tests\Connection;

use Core23\FacebookBundle\Connection\FacebookConnection;
use PHPUnit\Framework\TestCase;

final class FacebookConnectionTest extends TestCase
{
    public function testGetApiId(): void
    {
        $connection = new FacebookConnection('key', 'secret');

        static::assertSame('key', $connection->getApiId());
    }

    public function testGetSharedSecre(): void
    {
        $connection = new FacebookConnection('key', 'secret');

        static::assertSame('secret', $connection->getSharedSecret());
    }
}
