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
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionManagerTest extends TestCase
{
    public function testIsAuthenticated(): void
    {
        /** @var ObjectProphecy&Session $session */
        $session = $this->prophesize(Session::class);
        $session->get('_CORE23_FACEBOOK_TOKEN')
            ->willReturn(true)
        ;

        $manager = new SessionManager($session->reveal());
        $this->assertTrue($manager->isAuthenticated());
    }

    public function testIsNotAuthenticated(): void
    {
        /** @var ObjectProphecy&Session $session */
        $session = $this->prophesize(Session::class);
        $session->get('_CORE23_FACEBOOK_TOKEN')
            ->willReturn(false)
        ;

        $manager = new SessionManager($session->reveal());
        $this->assertFalse($manager->isAuthenticated());
    }

    public function testGetUsername(): void
    {
        /** @var ObjectProphecy&Session $session */
        $session = $this->prophesize(Session::class);
        $session->get('_CORE23_FACEBOOK_NAME')
            ->willReturn('MyUser')
        ;

        $manager = new SessionManager($session->reveal());
        $this->assertSame('MyUser', $manager->getUsername());
    }

    public function testGetUsernameNotExist(): void
    {
        /** @var ObjectProphecy&Session $session */
        $session = $this->prophesize(Session::class);
        $session->get('_CORE23_FACEBOOK_NAME')
            ->willReturn(null)
        ;

        $manager = new SessionManager($session->reveal());
        $this->assertNull($manager->getUsername());
    }

    public function testStore(): void
    {
        $facebookSession = new FacebookSession('4711', 'YourName', 'YourToken', new DateTime());

        /** @var ObjectProphecy&Session $session */
        $session = $this->prophesize(Session::class);
        $session->set('_CORE23_FACEBOOK_ID', '4711')->shouldBeCalled();
        $session->set('_CORE23_FACEBOOK_NAME', 'YourName')->shouldBeCalled();
        $session->set('_CORE23_FACEBOOK_TOKEN', 'YourToken')->shouldBeCalled();
        $session->set('_CORE23_FACEBOOK_EXPIRES', Argument::type(DateTime::class))->shouldBeCalled();

        $manager = new SessionManager($session->reveal());
        $manager->store($facebookSession);

        $this->assertTrue(true);
    }

    public function testStoreWithNoExpiryDate(): void
    {
        $facebookSession = new FacebookSession('4711', 'YourName', 'YourToken', null);

        /** @var ObjectProphecy&Session $session */
        $session = $this->prophesize(Session::class);
        $session->set('_CORE23_FACEBOOK_ID', '4711')->shouldBeCalled();
        $session->set('_CORE23_FACEBOOK_NAME', 'YourName')->shouldBeCalled();
        $session->set('_CORE23_FACEBOOK_TOKEN', 'YourToken')->shouldBeCalled();
        $session->set('_CORE23_FACEBOOK_EXPIRES', Argument::is(null))->shouldBeCalled();

        $manager = new SessionManager($session->reveal());
        $manager->store($facebookSession);

        $this->assertTrue(true);
    }

    public function testClear(): void
    {
        /** @var ObjectProphecy&Session $session */
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

        /** @var ObjectProphecy&Session $session */
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

        $this->assertNotNull($facebookSession);
        $this->assertSame('0815', $facebookSession->getFacebookId());
        $this->assertSame('MyUser', $facebookSession->getName());
        $this->assertSame('TheToken', $facebookSession->getToken());
        $this->assertSame($tomorrow, $facebookSession->getExpireDate());
    }
}
