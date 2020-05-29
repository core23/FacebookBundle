<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\FacebookBundle\Tests\Action;

use Core23\FacebookBundle\Action\AuthSuccessAction;
use Core23\FacebookBundle\Core23FacebookEvents;
use Core23\FacebookBundle\Event\AuthSuccessEvent;
use Core23\FacebookBundle\Session\SessionInterface;
use Core23\FacebookBundle\Session\SessionManagerInterface;
use Core23\FacebookBundle\Tests\EventDispatcher\TestEventDispatcher;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class AuthSuccessActionTest extends TestCase
{
    /**
     * @var Environment|ObjectProphecy
     */
    private $twig;

    /**
     * @var ObjectProphecy|RouterInterface
     */
    private $router;

    /**
     * @var ObjectProphecy|SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var TestEventDispatcher
     */
    private $eventDispatcher;

    protected function setUp(): void
    {
        $this->twig            = $this->prophesize(Environment::class);
        $this->router          = $this->prophesize(RouterInterface::class);
        $this->sessionManager  = $this->prophesize(SessionManagerInterface::class);
        $this->eventDispatcher = new TestEventDispatcher();
    }

    public function testExecute(): void
    {
        $session = $this->prophesize(SessionInterface::class);

        $this->sessionManager->isAuthenticated()->willReturn(true);
        $this->sessionManager->getSession()->willReturn($session);
        $this->sessionManager->getUsername()->willReturn('FooUser');

        $this->twig->render(
            '@Core23Facebook/Auth/success.html.twig',
            [
                'name' => 'FooUser',
            ]
        )->shouldBeCalled();

        $this->eventDispatcher->dispatch(
            Argument::type(AuthSuccessEvent::class),
            Core23FacebookEvents::AUTH_SUCCESS
        );

        $action = new AuthSuccessAction(
            $this->twig->reveal(),
            $this->router->reveal(),
            $this->sessionManager->reveal(),
            $this->eventDispatcher
        );

        $response = $action();

        static::assertNotInstanceOf(RedirectResponse::class, $response);
        static::assertSame(200, $response->getStatusCode());
    }

    public function testExecuteWithCaughtEvent(): void
    {
        $session = $this->prophesize(SessionInterface::class);

        $this->sessionManager->isAuthenticated()->willReturn(true);
        $this->sessionManager->getSession()->willReturn($session);
        $this->sessionManager->getUsername()->willReturn('FooUser');

        $eventResponse = new Response();

        $this->eventDispatcher->setResponse($eventResponse);

        $this->eventDispatcher->dispatch(
            Argument::type(AuthSuccessEvent::class),
            Core23FacebookEvents::AUTH_SUCCESS
        );

        $action = new AuthSuccessAction(
            $this->twig->reveal(),
            $this->router->reveal(),
            $this->sessionManager->reveal(),
            $this->eventDispatcher
        );

        $response = $action();

        static::assertSame($eventResponse, $response);
    }

    public function testExecuteNoAuth(): void
    {
        $this->sessionManager->isAuthenticated()
            ->willReturn(false)
        ;

        $this->router->generate('core23_facebook_error')
            ->willReturn('/success')
            ->shouldBeCalled()
        ;

        $action = new AuthSuccessAction(
            $this->twig->reveal(),
            $this->router->reveal(),
            $this->sessionManager->reveal(),
            $this->eventDispatcher
        );

        static::assertInstanceOf(RedirectResponse::class, $action());
    }

    public function testExecuteNoSession(): void
    {
        $this->sessionManager->isAuthenticated()
            ->willReturn(true)
        ;
        $this->sessionManager->getSession()
            ->willReturn(null)
        ;

        $this->router->generate('core23_facebook_error')
            ->willReturn('/success')
            ->shouldBeCalled()
        ;

        $action = new AuthSuccessAction(
            $this->twig->reveal(),
            $this->router->reveal(),
            $this->sessionManager->reveal(),
            $this->eventDispatcher
        );

        static::assertInstanceOf(RedirectResponse::class, $action());
    }
}
