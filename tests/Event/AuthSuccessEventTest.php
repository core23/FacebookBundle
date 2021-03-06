<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Tests\Event;

use Core23\FacebookBundle\Event\AuthSuccessEvent;
use Core23\FacebookBundle\Session\SessionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class AuthSuccessEventTest extends TestCase
{
    public function testGetUsername(): void
    {
        $session = $this->prophesize(SessionInterface::class);
        $session->getName()->willReturn('MyUser');

        $event = new AuthSuccessEvent($session->reveal());

        static::assertSame('MyUser', $event->getUsername());
    }

    public function testGetSession(): void
    {
        $session = $this->prophesize(SessionInterface::class);

        $event = new AuthSuccessEvent($session->reveal());

        static::assertSame($session->reveal(), $event->getSession());
    }

    public function testGetResponse(): void
    {
        $session = $this->prophesize(SessionInterface::class);

        $event = new AuthSuccessEvent($session->reveal());

        static::assertNull($event->getResponse());
    }

    public function testSetResponse(): void
    {
        $session = $this->prophesize(SessionInterface::class);

        $reponse = $this->prophesize(Response::class);

        $event = new AuthSuccessEvent($session->reveal());
        $event->setResponse($reponse->reveal());

        static::assertSame($reponse->reveal(), $event->getResponse());
    }
}
