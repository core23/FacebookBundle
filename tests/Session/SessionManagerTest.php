<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Tests\Session;

use Core23\FacebookBundle\Session\Session as FacebookSession;
use Core23\FacebookBundle\Session\SessionInterface;
use Core23\FacebookBundle\Session\SessionManager;
use DateTime;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionManagerTest extends TestCase
{
    public function testIsAuthenticated(): void
    {
        $session = $this->prophesize(Session::class);
        $session->get('_CORE23_FACEBOOK_TOKEN')
            ->willReturn(true)
        ;

        $manager = new SessionManager($session->reveal());
        static::assertTrue($manager->isAuthenticated());
    }

    public function testIsNotAuthenticated(): void
    {
        $session = $this->prophesize(Session::class);
        $session->get('_CORE23_FACEBOOK_TOKEN')
            ->willReturn(false)
        ;

        $manager = new SessionManager($session->reveal());
        static::assertFalse($manager->isAuthenticated());
    }

    public function testGetUsername(): void
    {
        $session = $this->prophesize(Session::class);
        $session->get('_CORE23_FACEBOOK_NAME')
            ->willReturn('MyUser')
        ;

        $manager = new SessionManager($session->reveal());
        static::assertSame('MyUser', $manager->getUsername());
    }

    public function testGetUsernameNotExist(): void
    {
        $session = $this->prophesize(Session::class);
        $session->get('_CORE23_FACEBOOK_NAME')
            ->willReturn(null)
        ;

        $manager = new SessionManager($session->reveal());
        static::assertNull($manager->getUsername());
    }

    public function testStore(): void
    {
        $facebookSession = new FacebookSession('4711', 'YourName', 'YourToken', new DateTime());

        $session = $this->prophesize(Session::class);
        $session->set('_CORE23_FACEBOOK_ID', '4711')->shouldBeCalled();
        $session->set('_CORE23_FACEBOOK_NAME', 'YourName')->shouldBeCalled();
        $session->set('_CORE23_FACEBOOK_TOKEN', 'YourToken')->shouldBeCalled();
        $session->set('_CORE23_FACEBOOK_EXPIRES', Argument::type(DateTime::class))->shouldBeCalled();

        $manager = new SessionManager($session->reveal());
        $manager->store($facebookSession);

        static::assertTrue(true);
    }

    public function testStoreWithNoExpiryDate(): void
    {
        $facebookSession = new FacebookSession('4711', 'YourName', 'YourToken', null);

        $session = $this->prophesize(Session::class);
        $session->set('_CORE23_FACEBOOK_ID', '4711')->shouldBeCalled();
        $session->set('_CORE23_FACEBOOK_NAME', 'YourName')->shouldBeCalled();
        $session->set('_CORE23_FACEBOOK_TOKEN', 'YourToken')->shouldBeCalled();
        $session->set('_CORE23_FACEBOOK_EXPIRES', Argument::is(null))->shouldBeCalled();

        $manager = new SessionManager($session->reveal());
        $manager->store($facebookSession);

        static::assertTrue(true);
    }

    public function testClear(): void
    {
        $session = $this->prophesize(Session::class);
        $session->remove('_CORE23_FACEBOOK_ID')->shouldBeCalled();
        $session->remove('_CORE23_FACEBOOK_TOKEN')->shouldBeCalled();
        $session->remove('_CORE23_FACEBOOK_NAME')->shouldBeCalled();
        $session->remove('_CORE23_FACEBOOK_EXPIRES')->shouldBeCalled();

        $manager = new SessionManager($session->reveal());
        $manager->clear();
    }

    public function testGetSession(): void
    {
        $tomorrow = new DateTime('tomorrow');

        $session = $this->prophesize(Session::class);
        $session->get('_CORE23_FACEBOOK_ID')
            ->willReturn('0815')
        ;
        $session->get('_CORE23_FACEBOOK_TOKEN')
            ->willReturn('TheToken')
        ;
        $session->get('_CORE23_FACEBOOK_NAME')
            ->willReturn('MyUser')
        ;
        $session->get('_CORE23_FACEBOOK_EXPIRES')
            ->willReturn($tomorrow)
        ;

        $manager = new SessionManager($session->reveal());

        /** @var SessionInterface $facebookSession */
        $facebookSession = $manager->getSession();

        static::assertNotNull($facebookSession);
        static::assertSame('0815', $facebookSession->getFacebookId());
        static::assertSame('MyUser', $facebookSession->getName());
        static::assertSame('TheToken', $facebookSession->getToken());
        static::assertSame($tomorrow, $facebookSession->getExpireDate());
    }

    public function testGetSessionWithNoAuth(): void
    {
        $session = $this->prophesize(Session::class);
        $session->get('_CORE23_FACEBOOK_TOKEN')
            ->willReturn(null)
        ;

        $manager = new SessionManager($session->reveal());

        static::assertNull($manager->getSession());
    }
}
